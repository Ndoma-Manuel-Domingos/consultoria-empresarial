<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SalePayment;
use App\Models\StockMovement;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;

class SaleController extends Controller
{
    public function __construct(protected StockService $stockService) {}

    /**
     * LISTA
     */
    public function index(Request $request)
    {
        $tenantId = session('tenant_id');

        $sales = Sale::query()
            ->where('tenant_id', $tenantId)
            ->with('client')
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('number', 'like', "%{$search}%")->orWhereHas('client', function ($client) use ($search) {
                        $client->where('name', 'like', "%{$search}%")->orWhere('nif', 'like', "%{$search}%");
                    });
                });
            })
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->orderByDesc('sale_date')
            ->paginate(20)
            ->withQueryString();

        return view('tenant.sales.index',compact('sales'));
    }

    /**
     * POS
     */
    public function create()
    {
        $tenantId = session('tenant_id');

        $clients = Client::query()->where('tenant_id', $tenantId)->where('is_active', true)->orderBy('name')->get(['id','name','nif',]);

        return view('tenant.sales.create',compact('clients'));
    }

    /**
     * PESQUISA DE PRODUTOS DO POS
     */
    public function search(Request $request)
    {
        $tenantId = session('tenant_id');

        $search = trim((string) $request->get('search'));

        if ($search === '') {
            return response()->json([]);
        }
    
        $products = Product::query()
            ->where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->where('is_sellable', true)
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")->orWhere('short_name', 'like', "%{$search}%")->orWhere('code', 'like', "%{$search}%")->orWhere('barcode', 'like', "%{$search}%")->orWhere('brand', 'like', "%{$search}%");
            })
            ->limit(15)
            ->get();

        $result = $products->map(function ($product) {

            $stock = $this->stockService->getAvailableStock($product);

            return [
                'id' => $product->id,
                'code' => $product->code,
                'barcode' => $product->barcode,
                'name' => $product->name,
                'short_name' => $product->short_name,
                'unit' => $product->unit,
                'price' => (float) ( $product->sale_price_with_tax ?? $product->sale_price),
                'sale_price' => (float) $product->sale_price,
                'sale_price_with_tax' => (float) $product->sale_price_with_tax,
                'tax_rate' => (float) $product->tax_rate,
                'tax_type' => $product->tax_type,
                'manage_stock' =>  (bool) $product->manage_stock,
                'manage_lots' => (bool) $product->manage_lots,
                'stock' => $stock,
                'allow_negative_stock' => (bool) $product->allow_negative_stock,
            ];
        });

        return response()->json($result);
    }

    /**
     * GUARDAR VENDA
     */
    public function store(Request $request)
    {
        $tenantId = session('tenant_id');
      
        Log::info('STORE VENDA CHAMADO', [
            'time' => now()->format('Y-m-d H:i:s.u'),
            'ip' => $request->ip(),
        ]);

        $validated = $request->validate([
            'client_id' => ['nullable','integer','exists:clients,id',],
            'discount' => ['nullable','numeric','min:0',],
            'notes' => ['nullable','string','max:5000',],
            'items' => ['required','array','min:1',],
            'items.*.product_id' => ['required','integer','exists:products,id',],
            'items.*.quantity' => ['required','numeric','gt:0',],
            'items.*.unit_price' => ['required','numeric','min:0',],
            'items.*.discount' => ['nullable','numeric','min:0',],
            'payments' => ['required','array','min:1',],
            'payments.*.method' => ['required','in:cash,multicaixa,transfer,tpa,credit',],
            'payments.*.amount' => ['required','numeric','gt:0',],
            'payments.*.reference' => ['nullable','string','max:100',],
        ]);

        /*
         * Garante que o cliente pertence ao tenant.
         */
        if (!empty($validated['client_id'])) {
            $clientExists = Client::query()
                ->where('id', $validated['client_id'])
                ->where('tenant_id', $tenantId)
                ->exists();

            if (!$clientExists) {
                abort(422, 'Cliente inválido.');
            }
        }

        try {

            $sale = DB::transaction(function () use ($validated, $tenantId) {

                $number = $this->generateSaleNumber($tenantId);

                $subtotal = 0;
                $taxAmount = 0;
                $discount = (float) ($validated['discount'] ?? 0);

                /*
                 * Criamos a venda primeiro.
                 */
                $sale = Sale::create([
                    'tenant_id' => $tenantId,
                    'client_id' => $validated['client_id'] ?? null,
                    'number' => $number,
                    'status' => 'completed',
                    'sale_date' => now(),
                    'subtotal' => 0,
                    'discount' => 0,
                    'tax_amount' => 0,
                    'total' => 0,
                    'paid_amount' => 0,
                    'change_amount' => 0,
                    'payment_method' => null,
                    'notes' => $validated['notes'] ?? null,
                    'created_by' => Auth::user()->id,
                ]);

                /*
                 * ============================
                 * ITENS
                 * ============================
                 */
                foreach ($validated['items'] as $itemData) {
                    $product = Product::query()
                        ->where('tenant_id', $tenantId)
                        ->where('id', $itemData['product_id'])
                        ->where('is_active', true)
                        ->where('is_sellable', true)
                        ->lockForUpdate()
                        ->first();

                    if (!$product) {
                        throw new RuntimeException('Produto inválido ou indisponível.');
                    }

                    $quantity = (float) $itemData['quantity'];
                    $unitPrice = (float) $itemData['unit_price'];
                    $itemDiscount = (float) (     $itemData['discount'] ?? 0 );
                    $lineSubtotal = ($quantity * $unitPrice) - $itemDiscount;

                    if ($lineSubtotal < 0) {
                        throw new RuntimeException("Desconto inválido para {$product->name}.");
                    }

                    /*
                     * IVA
                     */
                    $taxRate = 0;

                    if ($product->tax_type === 'standard') {
                        $taxRate = (float) $product->tax_rate;
                    }

                    /*
                     * Assumimos que unit_price é preço COM IVA
                     * quando o POS usa sale_price_with_tax.
                     *
                     * Para obter o IVA incluído:
                     */
                    if ($taxRate > 0) {
                        $lineTax = $lineSubtotal - ($lineSubtotal / (1 + ($taxRate / 100)));
                    } else {
                        $lineTax = 0;
                    }

                    $lineTotal = $lineSubtotal;
                    $subtotal += $lineSubtotal;
                    $taxAmount += $lineTax;
                    $saleItem = SaleItem::create([
                        'sale_id' => $sale->id,
                        'product_id' => $product->id,
                        'quantity' => $quantity,
                        'unit_price' => $unitPrice,
                        'discount' => $itemDiscount,
                        'tax_rate' => $taxRate,
                        'tax_amount' => $lineTax,
                        'subtotal' => $lineSubtotal,
                        'total' => $lineTotal,
                    ]);

                    /*
                     * BAIXA STOCK
                     *
                     * Aqui entra o FIFO.
                     */

                    $this->stockService->removeStock($product, $quantity, $saleItem, $number);
                }

                /*
                 * DESCONTO GLOBAL
                 */
                if ($discount > $subtotal) {
                    throw new RuntimeException(
                        'O desconto não pode ser superior ao subtotal.'
                    );
                }

                $total = $subtotal - $discount;

                /*
                 * PAGAMENTOS
                 */
                $paidAmount = 0;

                foreach ( $validated['payments'] as $payment) {
                    $amount = (float) $payment['amount'];
                    $paidAmount += $amount;

                    SalePayment::create([
                        'sale_id' => $sale->id,
                        'method' => $payment['method'],
                        'amount' => $amount,
                        'reference' => $payment['reference'] ?? null,
                        'notes' => null,
                    ]);
                }

                /*
                 * Pagamento insuficiente.
                 */
                if ($paidAmount < $total) {

                    throw new RuntimeException(
                        'O valor pago é inferior ao total da venda.'
                    );
                }

                $change = $paidAmount - $total;

                /*
                 * MÉTODO
                 */
                $methods = collect($validated['payments'])->pluck('method')->unique();
                $paymentMethod = $methods->count() > 1 ? 'mixed' : $methods->first();
                $sale->update([
                    'subtotal' => $subtotal,
                    'discount' => $discount,
                    'tax_amount' => $taxAmount,
                    'total' => $total,
                    'paid_amount' => $paidAmount,
                    'change_amount' => $change,
                    'payment_method' => $paymentMethod,
                ]);

                return $sale;
            });

            return redirect()->route('tenant.sales.show',$sale)->with('success','Venda registada com sucesso.');

        } catch (RuntimeException $e) {
            return back()
                ->withInput()
                ->withErrors([
                    'sale' => $e->getMessage(),
                ]);
        }
    }

    /**
     * DETALHE
     */
    public function show(Sale $sale)
    {
        $this->authorizeSale($sale);

        $sale->load([
            'client',
            'creator',
            'items.product',
            'items.lots.productLot',
            'payments',
        ]);

        return view('tenant.sales.show',compact('sale'));
    }

    /**
     * ANULAR VENDA
     */
    public function cancel(Sale $sale)
    {
        $this->authorizeSale($sale);

        if ($sale->status === 'cancelled') {
            return back()->withErrors([
                'sale' => 'Esta venda já foi anulada.',
            ]);
        }

        try {
            DB::transaction(function () use ($sale) {
                $sale->load([
                    'items.product',
                    'items.lots.productLot',
                ]);
                foreach ($sale->items as $item) {
                    /*
                     * Se teve lotes:
                     * devolvemos para os mesmos lotes.
                     */
                    if ($item->lots->count()) {

                        foreach ($item->lots as $itemLot) {
                            $lot = $itemLot->productLot;
                            $before =  (float) $lot->current_quantity;
                            $quantity =  (float) $itemLot->quantity;
                            $after = $before + $quantity;
                            $lot->current_quantity = $after;

                            $lot->save();

                            StockMovement::create([
                                'tenant_id' => $sale->tenant_id,
                                'product_id' => $item->product_id,
                                'product_lot_id' => $lot->id,
                                'type' => 'return_in',
                                'quantity' => $quantity,
                                'stock_before' => $before,
                                'stock_after' => $after,
                                'unit_cost' => $itemLot->unit_cost,
                                'total_cost' => $itemLot->total_cost,
                                'reference' => $sale->number,
                                'document_type' => 'sale_cancellation',
                                'document_number' => $sale->number,
                                'reason' => 'Anulação da venda',
                                'movement_date' => now(),
                                'created_by' => Auth::user()->id,
                            ]);
                        }

                    } else {

                        /*
                         * Produto sem lote.
                         */
                        $stock = $this->stockService->getProductStock($sale->tenant_id, $item->product_id);
                        $quantity = (float) $item->quantity;
                        $before = $stock;
                        $after = $before + $quantity;
                        $unitCost = (float) $item->product->cost_price;

                        StockMovement::create([
                            'tenant_id' => $sale->tenant_id,
                            'product_id' => $item->product_id,
                            'product_lot_id' => null,
                            'type' => 'return_in',
                            'quantity' => $quantity,
                            'stock_before' => $before,
                            'stock_after' => $after,
                            'unit_cost' => $unitCost,
                            'total_cost' => $quantity * $unitCost,
                            'reference' => $sale->number,
                            'document_type' => 'sale_cancellation',
                            'document_number' => $sale->number,
                            'reason' => 'Anulação da venda',
                            'movement_date' => now(),
                            'created_by' => Auth::user()->id,
                        ]);
                    }
                }
                $sale->update([
                    'status' => 'cancelled',
                    'cancelled_by' => Auth::user()->id,
                    'cancelled_at' => now(),
                ]);
            });

            return back()->with( 'success', 'Venda anulada e stock reposto com sucesso.');

        } catch (\Throwable $e) {
            return back()->withErrors([
                'sale' => 'Não foi possível anular a venda: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Número da venda.
     */
    protected function generateSaleNumber(int $tenantId): string 
    {
        $prefix = 'VEN-' . now()->format('Ym') . '-';

        $last = Sale::query()->where('tenant_id', $tenantId)->where('number', 'like', $prefix . '%')->orderByDesc('id')->lockForUpdate()->first();

        $sequence = 1;

        if ($last) {
            $lastNumber = (int) Str::after($last->number, $prefix);
            $sequence = $lastNumber + 1;
        }
        return $prefix . str_pad($sequence, 5, '0', STR_PAD_LEFT);
    }

    protected function authorizeSale( Sale $sale): void 
    {
        $tenantId = session('tenant_id');

        abort_if($sale->tenant_id !== $tenantId, 403);
    }
}
