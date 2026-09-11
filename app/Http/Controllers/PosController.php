<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Product;
use App\Models\ProductLot;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class POSController extends Controller
{
    /**
     * Tela principal do POS
     */
    public function index()
    {
        $tenantId = session('tenant_id');

        $clients = Client::where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('tenant.sales.create', compact('clients'));
    }

    /**
     * Pesquisa produtos para o POS.
     *
     * Pesquisa por:
     * - código
     * - código de barras
     * - nome
     * - nome curto
     * - marca
     */
    public function searchProducts(Request $request)
    {
        $tenantId = session('tenant_id');

        $search = trim($request->get('search', ''));

        if ($search === '') {
            return response()->json([
                'success' => true,
                'products' => [],
            ]);
        }

        $products = Product::query()
            ->where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->where('is_sellable', true)
            ->where(function ($query) use ($search) {
                $query->where('code', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('short_name', 'like', "%{$search}%")
                    ->orWhere('brand', 'like', "%{$search}%")
                    ->orWhere('model', 'like', "%{$search}%");
            })
            ->with([
                'lots' => function ($query) {
                    $query->where('is_active', true)
                        ->where('current_quantity', '>', 0)
                        ->orderBy('expires_at')
                        ->orderBy('id');
                }
            ])
            ->limit(20)
            ->get();

        $result = $products->map(function ($product) {

            $stock = 0;

            if ($product->manage_stock) {
                $stock = $product->lots->sum(function ($lot) {
                    return max(
                        0,
                        (float) $lot->current_quantity -
                        (float) $lot->reserved_quantity
                    );
                });
            }

            return [
                'id' => $product->id,
                'code' => $product->code,
                'barcode' => $product->barcode,
                'name' => $product->name,
                'short_name' => $product->short_name,
                'brand' => $product->brand,
                'unit' => $product->unit,

                'sale_price' => (float) $product->sale_price,
                'sale_price_with_tax' => (float) $product->sale_price_with_tax,

                'tax_type' => $product->tax_type,
                'tax_rate' => (float) $product->tax_rate,

                'manage_stock' => (bool) $product->manage_stock,
                'manage_lots' => (bool) $product->manage_lots,
                'has_expiration' => (bool) $product->has_expiration,

                'stock' => $stock,
            ];
        });

        return response()->json([
            'success' => true,
            'products' => $result,
        ]);
    }

    /**
     * Obtém um produto específico.
     */
    public function product($id)
    {
        $tenantId = session('tenant_id');

        $product = Product::where('tenant_id', $tenantId)
            ->where('id', $id)
            ->where('is_active', true)
            ->where('is_sellable', true)
            ->with([
                'lots' => function ($query) {
                    $query->where('is_active', true)
                        ->where('current_quantity', '>', 0)
                        ->orderBy('expires_at')
                        ->orderBy('id');
                }
            ])
            ->firstOrFail();

        $stock = 0;

        if ($product->manage_stock) {
            $stock = $product->lots->sum(function ($lot) {
                return max(
                    0,
                    (float) $lot->current_quantity -
                    (float) $lot->reserved_quantity
                );
            });
        }

        return response()->json([
            'success' => true,

            'product' => [
                'id' => $product->id,
                'code' => $product->code,
                'barcode' => $product->barcode,
                'name' => $product->name,
                'short_name' => $product->short_name,
                'unit' => $product->unit,

                'sale_price' => (float) $product->sale_price,
                'sale_price_with_tax' => (float) $product->sale_price_with_tax,

                'tax_type' => $product->tax_type,
                'tax_rate' => (float) $product->tax_rate,

                'manage_stock' => (bool) $product->manage_stock,
                'manage_lots' => (bool) $product->manage_lots,
                'has_expiration' => (bool) $product->has_expiration,

                'stock' => $stock,
            ],
        ]);
    }

    /**
     * Finaliza a venda.
     *
     * A baixa do estoque é feita usando FIFO.
     */
    public function checkout(Request $request)
    {
        $tenantId = session('tenant_id');

        $validated = $request->validate([
            'client_id' => [
                'nullable',
                'integer',
                'exists:clients,id',
            ],

            'items' => [
                'required',
                'array',
                'min:1',
            ],

            'items.*.product_id' => [
                'required',
                'integer',
            ],

            'items.*.quantity' => [
                'required',
                'numeric',
                'gt:0',
            ],

            'items.*.unit_price' => [
                'required',
                'numeric',
                'gte:0',
            ],

            'payment_method' => [
                'required',
                'in:cash,multicaixa,transfer,card,mixed',
            ],

            'cash_amount' => [
                'nullable',
                'numeric',
                'gte:0',
            ],

            'multicaixa_amount' => [
                'nullable',
                'numeric',
                'gte:0',
            ],

            'transfer_amount' => [
                'nullable',
                'numeric',
                'gte:0',
            ],

            'card_amount' => [
                'nullable',
                'numeric',
                'gte:0',
            ],

            'discount' => [
                'nullable',
                'numeric',
                'gte:0',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ]);

        $sale = DB::transaction(function () use (
            $validated,
            $tenantId
        ) {

            /*
             * ==========================================================
             * VALIDAR CLIENTE
             * ==========================================================
             */

            $client = null;

            if (!empty($validated['client_id'])) {

                $client = Client::where('tenant_id', $tenantId)
                    ->where('id', $validated['client_id'])
                    ->where('is_active', true)
                    ->firstOrFail();
            }

            /*
             * ==========================================================
             * CALCULAR TOTAIS
             * ==========================================================
             */

            $subtotal = 0;
            $taxTotal = 0;

            foreach ($validated['items'] as $item) {

                $product = Product::where('tenant_id', $tenantId)
                    ->where('id', $item['product_id'])
                    ->where('is_active', true)
                    ->where('is_sellable', true)
                    ->firstOrFail();

                $quantity = (float) $item['quantity'];
                $unitPrice = (float) $item['unit_price'];

                $lineSubtotal = $quantity * $unitPrice;

                $lineTax = 0;

                if ($product->tax_type === 'standard') {

                    $lineTax =
                        $lineSubtotal *
                        ((float) $product->tax_rate / 100);
                }

                $subtotal += $lineSubtotal;
                $taxTotal += $lineTax;
            }

            $discount = (float) ($validated['discount'] ?? 0);

            $total = max(
                0,
                $subtotal + $taxTotal - $discount
            );

            /*
             * ==========================================================
             * PAGAMENTOS
             * ==========================================================
             */

            $cashAmount =
                (float) ($validated['cash_amount'] ?? 0);

            $multicaixaAmount =
                (float) ($validated['multicaixa_amount'] ?? 0);

            $transferAmount =
                (float) ($validated['transfer_amount'] ?? 0);

            $cardAmount =
                (float) ($validated['card_amount'] ?? 0);

            $paidAmount =
                $cashAmount +
                $multicaixaAmount +
                $transferAmount +
                $cardAmount;

            /*
             * Tolerância de 0.01 para arredondamentos.
             */

            if ($paidAmount + 0.01 < $total) {

                abort(
                    422,
                    'O valor pago é inferior ao total da venda.'
                );
            }

            $change = max(
                0,
                $paidAmount - $total
            );

            /*
             * ==========================================================
             * NÚMERO DA VENDA
             * ==========================================================
             */

            $saleNumber = $this->generateSaleNumber($tenantId);

            /*
             * ==========================================================
             * CRIAR VENDA
             * ==========================================================
             */

            $sale = Sale::create([

                'tenant_id' => $tenantId,

                'client_id' => $client?->id,

                'number' => $saleNumber,

                'status' => 'completed',

                'subtotal' => $subtotal,

                'discount' => $discount,

                'tax_total' => $taxTotal,

                'total' => $total,

                'paid_amount' => $paidAmount,

                'change_amount' => $change,

                'payment_method' =>
                    $validated['payment_method'],

                'cash_amount' =>
                    $cashAmount,

                'multicaixa_amount' =>
                    $multicaixaAmount,

                'transfer_amount' =>
                    $transferAmount,

                'card_amount' =>
                    $cardAmount,

                'notes' =>
                    $validated['notes'] ?? null,

                'sold_at' => now(),

                'created_by' =>
                    Auth::user()->id,

                'updated_by' =>
                    Auth::user()->id,
            ]);

            /*
             * ==========================================================
             * PROCESSAR PRODUTOS
             * ==========================================================
             */

            foreach ($validated['items'] as $item) {

                $product = Product::where('tenant_id', $tenantId)
                    ->where('id', $item['product_id'])
                    ->where('is_active', true)
                    ->where('is_sellable', true)
                    ->lockForUpdate()
                    ->firstOrFail();

                $quantity = (float) $item['quantity'];

                $unitPrice = (float) $item['unit_price'];

                $lineSubtotal =
                    $quantity * $unitPrice;

                $taxRate =
                    $product->tax_type === 'standard'
                        ? (float) $product->tax_rate
                        : 0;

                $lineTax =
                    $lineSubtotal *
                    ($taxRate / 100);

                /*
                 * ======================================================
                 * FIFO
                 * ======================================================
                 *
                 * Primeiro lote comprado =
                 * primeiro lote vendido.
                 *
                 * A ordenação usa:
                 *
                 * 1. expires_at
                 * 2. id
                 *
                 * IMPORTANTE:
                 *
                 * Para FIFO de custo real, o ideal é que os lotes
                 * sejam ordenados pela data de entrada do lote.
                 *
                 * Como product_lots atualmente não possui
                 * received_at/created_at pode ser usado como
                 * aproximação.
                 */

                $remaining = $quantity;

                $fifoLots = ProductLot::where(
                    'tenant_id',
                    $tenantId
                )
                    ->where('product_id', $product->id)
                    ->where('is_active', true)
                    ->whereRaw(
                        '(current_quantity - reserved_quantity) > 0'
                    )
                    ->orderBy('created_at')
                    ->orderBy('id')
                    ->lockForUpdate()
                    ->get();

                if (
                    $product->manage_stock &&
                    !$product->allow_negative_stock
                ) {

                    $available =
                        $fifoLots->sum(function ($lot) {
                            return max(
                                0,
                                (float) $lot->current_quantity -
                                (float) $lot->reserved_quantity
                            );
                        });

                    if ($available < $quantity) {

                        abort(
                            422,
                            "Estoque insuficiente para o produto {$product->name}."
                        );
                    }
                }

                /*
                 * ======================================================
                 * CRIAR ITEM DA VENDA
                 * ======================================================
                 */

                $saleItem = SaleItem::create([

                    'tenant_id' => $tenantId,

                    'sale_id' => $sale->id,

                    'product_id' => $product->id,

                    'quantity' => $quantity,

                    'unit_price' => $unitPrice,

                    'subtotal' => $lineSubtotal,

                    'tax_rate' => $taxRate,

                    'tax_amount' => $lineTax,

                    'total' =>
                        $lineSubtotal + $lineTax,

                    'created_by' =>
                        Auth::user()->id,
                ]);

                /*
                 * ======================================================
                 * BAIXA FIFO DOS LOTES
                 * ======================================================
                 */

                if ($product->manage_stock) {

                    foreach ($fifoLots as $lot) {

                        if ($remaining <= 0) {
                            break;
                        }

                        $available =
                            max(
                                0,
                                (float) $lot->current_quantity -
                                (float) $lot->reserved_quantity
                            );

                        if ($available <= 0) {
                            continue;
                        }

                        $take = min(
                            $remaining,
                            $available
                        );

                        $before =
                            (float) $lot->current_quantity;

                        $after =
                            $before - $take;

                        /*
                         * Atualiza lote
                         */

                        $lot->current_quantity =
                            max(0, $after);

                        $lot->save();

                        /*
                         * ==================================================
                         * MOVIMENTO DE ESTOQUE
                         * ==================================================
                         */

                        $stockBefore =
                            $this->getProductStock(
                                $tenantId,
                                $product->id
                            ) + $take;

                        $stockAfter =
                            $this->getProductStock(
                                $tenantId,
                                $product->id
                            );

                        \App\Models\StockMovement::create([

                            'tenant_id' =>
                                $tenantId,

                            'product_id' =>
                                $product->id,

                            'product_lot_id' =>
                                $lot->id,

                            'type' =>
                                'sale',

                            'quantity' =>
                                $take,

                            'stock_before' =>
                                $stockBefore,

                            'stock_after' =>
                                $stockAfter,

                            'unit_cost' =>
                                (float) (
                                    $lot->cost_price ??
                                    $product->cost_price
                                ),

                            'total_cost' =>
                                $take *
                                (float) (
                                    $lot->cost_price ??
                                    $product->cost_price
                                ),

                            'reference' =>
                                $sale->number,

                            'document_type' =>
                                'sale',

                            'document_number' =>
                                $sale->number,

                            'reason' =>
                                'Venda POS',

                            'notes' =>
                                'Saída automática por venda FIFO.',

                            'movement_date' =>
                                now(),

                            'created_by' =>
                                Auth::user()->id,
                        ]);

                        $remaining -= $take;
                    }

                    /*
                     * Se permitir estoque negativo,
                     * a quantidade que faltar fica registrada
                     * sem lote.
                     */

                    if (
                        $remaining > 0 &&
                        $product->allow_negative_stock
                    ) {

                        $stockBefore =
                            $this->getProductStock(
                                $tenantId,
                                $product->id
                            );

                        $stockAfter =
                            $stockBefore - $remaining;

                        \App\Models\StockMovement::create([

                            'tenant_id' =>
                                $tenantId,

                            'product_id' =>
                                $product->id,

                            'product_lot_id' =>
                                null,

                            'type' =>
                                'sale',

                            'quantity' =>
                                $remaining,

                            'stock_before' =>
                                $stockBefore,

                            'stock_after' =>
                                $stockAfter,

                            'unit_cost' =>
                                (float) $product->cost_price,

                            'total_cost' =>
                                $remaining *
                                (float) $product->cost_price,

                            'reference' =>
                                $sale->number,

                            'document_type' =>
                                'sale',

                            'document_number' =>
                                $sale->number,

                            'reason' =>
                                'Venda POS - estoque negativo',

                            'notes' =>
                                'Saída sem lote por permissão de estoque negativo.',

                            'movement_date' =>
                                now(),

                            'created_by' =>
                                Auth::user()->id,
                        ]);
                    }
                }
            }

            return $sale;
        });

        return response()->json([
            'success' => true,

            'message' =>
                'Venda realizada com sucesso.',

            'sale_id' =>
                $sale->id,

            'sale_number' =>
                $sale->number,

            'total' =>
                (float) $sale->total,

            'paid_amount' =>
                (float) $sale->paid_amount,

            'change' =>
                (float) $sale->change_amount,

            'redirect' =>
                route(
                    'tenant.sales.show',
                    $sale
                ),
        ]);
    }

    /**
     * Obtém estoque atual do produto.
     */
    private function getProductStock(
        int $tenantId,
        int $productId
    ): float {

        return (float) ProductLot::where(
            'tenant_id',
            $tenantId
        )
            ->where('product_id', $productId)
            ->where('is_active', true)
            ->sum(
                DB::raw(
                    'current_quantity - reserved_quantity'
                )
            );
    }

    /**
     * Gera número da venda.
     */
    private function generateSaleNumber(int $tenantId): string {

        $prefix = 'VD-' . now()->format('Ymd');

        $lastSale = Sale::where(
            'tenant_id',
            $tenantId
        )
            ->where(
                'number',
                'like',
                $prefix . '-%'
            )
            ->orderByDesc('id')
            ->lockForUpdate()
            ->first();

        if (!$lastSale) {
            $sequence = 1;
        } else {

            $parts = explode(
                '-',
                $lastSale->number
            );

            $sequence =
                ((int) end($parts)) + 1;
        }

        return sprintf(
            '%s-%04d',
            $prefix,
            $sequence
        );
    }
}
