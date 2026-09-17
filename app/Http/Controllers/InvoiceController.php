<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Product;
use App\Models\ProductLot;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SaleItemLot;
use App\Models\SalePayment;
use App\Models\StockBalance;
use App\Models\StockMovement;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    public function __construct(protected StockService $stockService) {}

    private function tenantId(): int
    {
        $tenantId = session('tenant_id');
        abort_unless($tenantId, 403, 'Nenhuma organização selecionada.');
        return (int) $tenantId;
    }

    public function index(Request $request)
    {
        $tenantId = $this->tenantId();

        $query = Sale::query()->where('tenant_id', $tenantId)->with('client');

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('number', 'like', "%{$search}%")
                ->orWhere('fiscal_number', 'like', "%{$search}%")
                ->orWhereHas('client', function ($client) use ($search) {
                    $client->where('name', 'like', "%{$search}%")->orWhere('nif', 'like', "%{$search}%");
                });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('document_type')) {
            $query->where('document_type', $request->document_type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('sale_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('sale_date', '<=', $request->date_to);
        }

        $sales = $query->latest('sale_date')->paginate(20)->withQueryString();

        return view('tenant.invoices.index', compact('sales'));
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */
    public function create()
    {
        $tenantId = $this->tenantId();

        $clients = Client::query()
            ->where('tenant_id', $tenantId)
            ->orderBy('name')
            ->get();

        return view('tenant.invoices.create', compact('clients'));
    }

    /*
    |--------------------------------------------------------------------------
    | PRODUCT SEARCH
    |--------------------------------------------------------------------------
    */

    public function products(Request $request)
    {
        $tenantId = $this->tenantId();

        $search = trim($request->get('search', ''));

        if ($search === '') {
            return response()->json([]);
        }

        $products = Product::query()
            ->where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->where('is_sellable', true)
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%")
                    ->orWhere('short_name', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->limit(20)
            ->get();

        return response()->json(
            $products->map(function (Product $product) {
                $stock = $product->manage_stock ? (float) ($product->stock_quantity ?? 0) : null;
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'code' => $product->code,
                    'barcode' => $product->barcode,
                    'unit'  => $product->unit,
                    'price' => (float) $product->sale_price_with_tax,
                    'price_without_tax'  => (float) $product->sale_price,
                    'tax_rate' => (float) $product->tax_rate,
                    'tax_type' => $product->tax_type,
                    'manage_stock' => $product->manage_stock,
                    'manage_lots' => $product->manage_lots,
                    'allow_negative_stock' => $product->allow_negative_stock,
                    'stock' => $stock,
                ];
            })
        );
    }

    public function store(Request $request)
    {
        $tenantId = $this->tenantId();

        $validated = $request->validate([
            'client_id' => ['nullable','integer','exists:clients,id',],
            'document_type' => ['required','string','in:invoice,receipt,proforma,quotation',],
            'series' => ['nullable','string','max:30',],
            'discount' => ['nullable','numeric','min:0',],
            'notes' => ['nullable','string',],
            'items' => ['required','array','min:1',],
            'items.*.product_id' => ['nullable','integer','exists:products,id',],
            'items.*.name' => ['nullable','string','max:255',],
            'items.*.quantity' => ['required','numeric','gt:0',],
            'items.*.unit_price' => ['required','numeric','min:0',],
            'items.*.discount' => ['nullable','numeric','min:0',],
            'items.*.tax_rate' => ['nullable','numeric','min:0','max:100',],
            'items.*.tax_type' => ['nullable','string','in:standard,exempt,zero',],
            'payments' => ['nullable','array',],
            'payments.*.method' => ['required','string','in:cash,multicaixa,transfer,tpa,credit',],
            'payments.*.amount' => ['required','numeric','gt:0',],
            'payments.*.reference' => ['nullable','string','max:100',],
        ]);

        if ($validated['client_id'] ?? null) {
            $clientExists = Client::where('tenant_id', $tenantId)->where('id', $validated['client_id'])->exists();

            if (!$clientExists) {
                throw ValidationException::withMessages([
                    'client_id' => 'O cliente selecionado não pertence ao seu tenant.',
                ]);
            }
        }

        return DB::transaction(function () use ( $request, $validated, $tenantId) {
            $items = $validated['items'];
            $subtotal = 0;
            $taxableAmount = 0;
            $exemptAmount = 0;
            $taxAmount = 0;
            $preparedItems = [];

            foreach ($items as $index => $item) {
                $product = null;
                if (!empty($item['product_id'])) {
                    $product = Product::query()->where('tenant_id', $tenantId)->where('id', $item['product_id'])->where('is_active', true)->where('is_sellable', true)->first();
                    if (!$product) {
                        throw ValidationException::withMessages([
                            "items.{$index}.product_id" => 'Produto inválido ou não disponível para venda.',
                        ]);
                    }
                }

                $quantity = (float) $item['quantity'];
                $unitPrice = round((float) $item['unit_price'], 2);
                $lineDiscount = round((float) ($item['discount'] ?? 0), 2);

                $gross = round($quantity * $unitPrice, 2);

                if ($lineDiscount > $gross) {
                    $lineDiscount = $gross;
                }

                $lineNet = round($gross - $lineDiscount, 2);
                $taxType = $item['tax_type'] ?? ($product->tax_type ?? 'standard');
                $taxRate = (float) ( $item['tax_rate'] ?? ($product->tax_rate ?? 0) );

                if ($taxType === 'standard' && $taxRate > 0) {
                    $lineTaxable = round($lineNet / (1 + ($taxRate / 100)), 2);
                    $lineTax = round($lineNet - $lineTaxable, 2);

                    $taxableAmount += $lineTaxable;
                    $taxAmount += $lineTax;
                } else {
                    $lineTaxable = 0;
                    $lineTax = 0;
                    $exemptAmount += $lineNet;
                }

                $subtotal += $gross;

                $preparedItems[] = [
                    'product'   => $product,
                    'product_id'=> $product?->id,
                    'name'      => $item['name'] ?? $product?->name ?? 'Item livre',
                    'quantity'  => $quantity,
                    'unit_price'=> $unitPrice,
                    'discount' => $lineDiscount,
                    'tax_rate' => $taxRate,
                    'tax_type' => $taxType,
                    'subtotal'  => $lineNet,
                    'tax_amount'  => $lineTax,
                    'total'    => $lineNet,
                ];
            }

            /*
             * ======================================================
             * DESCONTO GLOBAL
             * ======================================================
             */

            $globalDiscount = round((float) ($validated['discount'] ?? 0), 2);

            $grossSubtotal = round($subtotal, 2);

            if ($globalDiscount > $grossSubtotal) {
                $globalDiscount = $grossSubtotal;
            }

            $total = round($grossSubtotal - $globalDiscount, 2);

            /*
             * ======================================================
             * DISTRIBUIR DESCONTO GLOBAL
             * ======================================================
             *
             * Para manter a consistência fiscal, distribuímos
             * proporcionalmente pelos itens.
             */

            if ($globalDiscount > 0 && $grossSubtotal > 0) {
                $remainingDiscount = $globalDiscount;
                $lastIndex = count($preparedItems) - 1;
                foreach ($preparedItems as $index => &$prepared) {

                    if ($index === $lastIndex) {
                        $itemGlobalDiscount = $remainingDiscount;
                    } else {
                        $itemGross = $prepared['quantity'] * $prepared['unit_price'];
                        $itemGlobalDiscount = round($globalDiscount * ($itemGross / $grossSubtotal),2);
                        $remainingDiscount -= $itemGlobalDiscount;
                    }

                    $prepared['discount'] += $itemGlobalDiscount;
                    $net = max(0, ($prepared['quantity'] * $prepared['unit_price']) - $prepared['discount']);
                    $prepared['subtotal'] = round($net, 2);

                    if ($prepared['tax_type'] === 'standard' && $prepared['tax_rate'] > 0) {
                        $prepared['tax_amount'] = round($net - ($net / (1 + $prepared['tax_rate'] / 100)), 2);
                    } else {
                        $prepared['tax_amount'] = 0;
                    }
                    $prepared['total'] = $net;
                }

                unset($prepared);
            }

            /*
             * Recalcular valores fiscais depois do desconto.
             */

            $taxableAmount = 0;
            $exemptAmount = 0;
            $taxAmount = 0;

            foreach ($preparedItems as &$prepared) {

                if ($prepared['tax_type'] === 'standard' && $prepared['tax_rate'] > 0) {
                    $base = round($prepared['total'] / (1 + ($prepared['tax_rate'] / 100)), 2);
                    $tax = round($prepared['total'] - $base, 2);
                    $taxableAmount += $base;
                    $taxAmount += $tax;
                    $prepared['tax_amount'] = $tax;
                } else {
                    $exemptAmount += $prepared['total'];
                    $prepared['tax_amount'] = 0;
                }
            }

            unset($prepared);

            /*
             * ======================================================
             * PAGAMENTOS
             * ======================================================
             */

            $payments = $validated['payments'] ?? [];

            $paidAmount = 0;

            foreach ($payments as $payment) {
                $paidAmount += (float) $payment['amount'];
            }

            $paidAmount = round($paidAmount, 2);

            /*
             * Crédito pode deixar saldo em aberto.
             *
             * Nos outros casos exigimos pagamento suficiente.
             */

            $hasCredit = collect($payments)->contains('method', 'credit');

            if (!$hasCredit && $paidAmount < $total) {
                throw ValidationException::withMessages([
                    'payments' => 'O valor pago é inferior ao total da factura.',
                ]);
            }

            $changeAmount = 0;

            if (!$hasCredit && $paidAmount > $total) {
                $changeAmount = round($paidAmount - $total, 2);
            }

            $balanceDue = max(0, round($total - $paidAmount, 2));

            /*
             * ======================================================
             * DOCUMENTO
             * ======================================================
             */

            $series = $validated['series'] ?? 'FT';

            $number = $this->stockService->generateNumber($tenantId, $series);

            /*
             * ======================================================
             * MÉTODO PRINCIPAL
             * ======================================================
             */

            $methods = collect($payments)->pluck('method')->unique()->values();

            if ($methods->count() > 1) {
                $paymentMethod = 'mixed';
            } else {
                $paymentMethod = $methods->first() ?? null;
            }

            /*
             * ======================================================
             * SALES
             * ======================================================
            */

            $sale = Sale::create([
                'tenant_id' => $tenantId,
                'client_id' => $validated['client_id'] ?? null,
                'number'=> $number,
                'status'=> 'completed',
                'sale_date' => now(),
                'subtotal'  => round($grossSubtotal, 2),
                'discount'  => round($globalDiscount, 2),
                'tax_amount'=> round($taxAmount, 2),
                'total' => round($total, 2),
                'paid_amount'  => $paidAmount,
                'change_amount'  => $changeAmount,
                'payment_method'  => $paymentMethod,
                'notes' => $validated['notes'] ?? null,
                'created_by'=> Auth::user()->id,
                'document_type'  => $validated['document_type'],
                'series'   => $series,
                'fiscal_number'  => null,
                'issued_at'=> now(),
                'currency' => 'AOA',
                'taxable_amount' => round($taxableAmount, 2),
                'exempt_amount' => round($exemptAmount, 2),
                'balance_due' => round($balanceDue, 2),
            ]);
            
            /*
             * ======================================================
             * ITENS
             * ======================================================
             */

            foreach ($preparedItems as $prepared) {
                /*
                 * Produtos livres são permitidos.
                 */
                $saleItem = SaleItem::create([
                    'sale_id'     => $sale->id,
                    'product_id'  => $prepared['product_id'] ?: $this->fallbackProductId($tenantId),
                    'quantity'    => $prepared['quantity'],
                    'unit_price'  => $prepared['unit_price'],
                    'discount'   => $prepared['discount'],
                    'tax_rate'   => $prepared['tax_rate'],
                    'tax_amount' => $prepared['tax_amount'],
                    'subtotal'   => $prepared['subtotal'],
                    'total'      => $prepared['total'],
                ]);

                /*
                 * ==================================================
                 * STOCK
                 * ==================================================
                 */
               
                if ($prepared['product']) {
                    $product = $prepared['product'];
                    if ($product->manage_stock) {
                        $this->stockService->removeStock($product, $prepared['quantity'], $saleItem, $number);
                    }
                }
            }
            /*
             * ======================================================
             * PAGAMENTOS
             * ======================================================
             */

            foreach ($payments as $payment) {
                SalePayment::create([
                    'sale_id'    => $sale->id,
                    'method'     => $payment['method'],
                    'amount'     => $payment['amount'],
                    'reference'  => $payment['reference'] ?? null,
                    'notes'      => $payment['notes'] ?? null,
                ]);
            }

            return redirect()->route('tenant.invoices.show', $sale->id)->with('success', 'Factura emitida com sucesso.');
        });
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW
    |--------------------------------------------------------------------------
    */

    public function show(string $id)
    {
        $sale = Sale::findOrFail($id);

        $this->authorizeSale($sale->id);

        $sale->load([
            'client',
            'creator',
            'items.product',
            'items.lots',
            'payments',
        ]);
        return view('tenant.invoices.show',compact('sale'));
    }

    /*
    |--------------------------------------------------------------------------
    | INVOICE
    |--------------------------------------------------------------------------
    */
    public function invoice(string $id)
    {
        $sale = Sale::findOrFail($id);

        $this->authorizeSale($sale->id);

        $sale->load([
            'client',
            'items.product',
            'payments',
        ]);

        return view('tenant.invoices.invoice', compact('sale'));
    }

    /*
    |--------------------------------------------------------------------------
    | CANCEL
    |--------------------------------------------------------------------------
    */
    public function cancel(Request $request, string $id)
    {
        $sale = Sale::findOrFail($id);

        $this->authorizeSale($sale->id);

        if ($sale->status === 'cancelled') {
            return back()->withErrors([
                'sale' => 'Esta factura já está anulada.',
            ]);
        }

        DB::transaction(function () use ($sale) {

            $sale->load([
                'items.product',
                'items.lots.productLot',
            ]);

            $this->stockService->cancel($sale);
        });

        return redirect()->route('tenant.invoices.show', $sale->id)->with('success', 'Factura anulada com sucesso.');
    }

    /*
    |--------------------------------------------------------------------------
    | RESTORE STOCK
    |--------------------------------------------------------------------------
    */
    private function restoreStock( string $saleId, Product $product, float $quantity): void 
    {
        $sale = Sale::findOrFail($saleId);

        $balance = StockBalance::query()
            ->where('tenant_id', $sale->tenant_id)
            ->where('product_id', $product->id)
            ->lockForUpdate()
            ->first();

        if (!$balance) {
            $balance = StockBalance::create([
                'tenant_id' => $sale->tenant_id,
                'product_id' => $product->id,
                'quantity' => 0,
                'reserved_quantity'=> 0,
            ]);
        }

        $before = (float) $balance->quantity;

        $after = $before + $quantity;

        $balance->update([
            'quantity' => $after,
        ]);

        $product->update([
            'stock_quantity' => $after,
        ]);

        StockMovement::create([
            'tenant_id' => $sale->tenant_id,
            'product_id'=> $product->id,
            'product_lot_id' => null,
            'type' => 'return_in',
            'quantity' => $quantity,
            'stock_before' => $before,
            'stock_after'=> $after,
            'unit_cost' => $product->cost_price,
            'total_cost'=> round($quantity * $product->cost_price, 2),
            'reference' => $sale->number,
            'document_type' => $sale->document_type,
            'document_number'=> $sale->number,
            'reason' => 'Anulação de venda',
            'movement_date' => now(),
            'created_by' => Auth::user()->id,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | AUTHORIZATION
    |--------------------------------------------------------------------------
    */
    private function authorizeSale(string $saleId): void
    {
        $sale = Sale::findOrFail($saleId);

        abort_if($sale->tenant_id !== $this->tenantId(), 403);
        //abort_unless((int) $sale->tenant_id === $this->tenantId(), 404);
    }

    /*
    |--------------------------------------------------------------------------
    | FALLBACK PRODUCT
    |--------------------------------------------------------------------------
    */
    private function fallbackProductId(int $tenantId): int
    {
        /*
         * IMPORTANTE:
         *
         * sale_items.product_id é obrigatório no teu banco.
         *
         * Portanto, para "factura livre", precisas de um produto
         * técnico por tenant.
         *
         * Exemplo: "ITEM LIVRE".
         */

        $product = Product::firstOrCreate([
            'tenant_id' => $tenantId,
            'code' => '__ITEM_LIVRE__',
        ], [
            'name' => 'Item livre',
            'short_name' => 'Item livre',
            'type'  => 'service',
            'unit'  => 'UN',
            'sale_price' => 0,
            'sale_price_with_tax'   => 0,
            'tax_type'   => 'standard',
            'tax_rate'   => 0,
            'manage_stock' => false,
            'allow_negative_stock' => true,
            'is_active'  => true,
            'is_sellable'  => true,
            'is_purchasable'        => false,
        ]);
        return $product->id;
    }
}
