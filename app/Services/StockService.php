<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductLot;
use App\Models\Sale;
use App\Models\StockMovement;
use App\Models\SaleItem;
use App\Models\SaleItemLot;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class StockService
{
    /**
     * Retorna stock atual de um produto sem lote.
     */
    public function getProductStock( int $tenantId, int $productId): float 
    {
        $stock = 0;

        $movements = StockMovement::query()
            ->where('tenant_id', $tenantId)
            ->where('product_id', $productId)
            ->get();

        foreach ($movements as $movement) {
            if (in_array($movement->type, ['in','purchase','adjustment_in','return_in','transfer_in'])) {
                $stock += $movement->quantity;
            } else {
                // 'out','sale','adjustment_out','return_out','transfer_out'
                $stock -= $movement->quantity;
            }
        }

        return (float) $stock;
    }

    /**
     * Retorna stock total disponível dos lotes.
     */
    public function getLotStock(int $tenantId, int $productId): float 
    {
        return (float) ProductLot::query()
            ->where('tenant_id', $tenantId)
            ->where('product_id', $productId)
            ->where('is_active', true)
            ->sum(DB::raw('current_quantity - reserved_quantity'));
    }

    /**
     * Stock disponível.
     */
    public function getAvailableStock(Product $product): float 
    {
        if (!$product->manage_stock) {
            return PHP_FLOAT_MAX;
        }
        if ($product->manage_lots) {
            return $this->getLotStock($product->tenant_id, $product->id);
        }
        return $this->getProductStock($product->tenant_id, $product->id);
    }

    /**
     * Retira stock.
     *
     * Se o produto usa lotes:
     * FIFO.
     *
     * Se não usa lotes:
     * utiliza stock global através de movimentos.
     */
    public function removeStock( Product $product, float $quantity, SaleItem $saleItem, string $documentNumber): void 
    {
        if ($quantity <= 0) {
            throw new RuntimeException('A quantidade deve ser superior a zero.');
        }

        if (!$product->manage_stock) {
            return;
        }
        if ($product->manage_lots) {
            $this->removeStockFIFO( $product, $quantity, $saleItem, $documentNumber);
            return;
        }
        $this->removeWithoutLot($product, $quantity, $saleItem, $documentNumber);
    }

    /**
     * FIFO.
     */
    protected function removeStockFIFO(Product $product, float $quantity, SaleItem $saleItem, string $documentNumber): void 
    {
        $remaining = $quantity;
       
        $lots = ProductLot::query()
            ->where('tenant_id', $product->tenant_id)
            ->where('product_id', $product->id)
            ->where('is_active', true)
            ->whereRaw('(current_quantity - reserved_quantity) > 0')
            ->orderBy('created_at')
            ->orderBy('id')
            ->lockForUpdate()
            ->get();

        $available = $lots->sum(function ($lot) {
            return max(0, (float) $lot->current_quantity - (float) $lot->reserved_quantity);
        });

        if ($available < $quantity) {
            if (!$product->allow_negative_stock) {
                throw new RuntimeException("Stock insuficiente para o produto {$product->name}. " . "Disponível: {$available}. Solicitado: {$quantity}.");
            }
        }

        foreach ($lots as $lot) {
            if ($remaining <= 0) {
                break;
            }

            $availableLot = max( 0, (float) $lot->current_quantity - (float) $lot->reserved_quantity);

            if ($availableLot <= 0) {
                continue;
            }

            $take = min($remaining, $availableLot);
            $before = (float) $lot->current_quantity;
            $after = $before - $take;
            $lot->current_quantity = $after;
            $lot->save();

            /*
             * Guarda exatamente de qual lote saiu.
             */
            $unitCost = (float) ( $lot->cost_price ?? $product->cost_price ?? 0);

            SaleItemLot::create([
                'sale_item_id' => $saleItem->id,
                'product_id' => $product->id,
                'product_lot_id' => $lot->id,
                'quantity' => $take,
                'unit_cost' => $unitCost,
                'total_cost' => $take * $unitCost,
            ]);

            /*
             * Movimento de saída.
             */
            StockMovement::create([
                'tenant_id' => $product->tenant_id,
                'product_id' => $product->id,
                'product_lot_id' => $lot->id,
                'type' => 'sale',
                'quantity' => $take,
                'stock_before' => $before,
                'stock_after' => $after,
                'unit_cost' => $unitCost,
                'total_cost' => $take * $unitCost,
                'reference' => $documentNumber,
                'document_type' => 'sale',
                'document_number' => $documentNumber,
                'reason' => 'Venda POS',
                'movement_date' => now(),
                'created_by' => Auth::user()->id,
            ]);

            $remaining -= $take;
        }

        /*
         * Se allow_negative_stock estiver ativo,
         * podemos permitir o restante.
         *
         * Mas não criamos lote negativo artificialmente.
         */
        if ($remaining > 0 && !$product->allow_negative_stock) {

            throw new RuntimeException(
                "Não foi possível completar a saída FIFO do produto {$product->name}."
            );
        }
    }

    /**
     * Retirada sem lote.
     */
    protected function removeWithoutLot(Product $product, float $quantity, SaleItem $saleItem, string $documentNumber): void 
    {
        $stock = $this->getProductStock($product->tenant_id, $product->id);

        if ($stock < $quantity && !$product->allow_negative_stock) {
            throw new RuntimeException(
                "Stock insuficiente para {$product->name}. " .
                "Disponível: {$stock}. Solicitado: {$quantity}."
            );
        }

        $before = $stock;
        $after = $stock - $quantity;

        $unitCost = (float) $product->cost_price;

        StockMovement::create([
            'tenant_id' => $product->tenant_id,
            'product_id' => $product->id,
            'product_lot_id' => null,
            'type' => 'sale',
            'quantity' => $quantity,
            'stock_before' => $before,
            'stock_after' => $after,
            'unit_cost' => $unitCost,
            'total_cost' => $quantity * $unitCost,
            'reference' => $documentNumber,
            'document_type' => 'sale',
            'document_number' => $documentNumber,
            'reason' => 'Venda POS',
            'movement_date' => now(),
            'created_by' => Auth::user()->id,
        ]);
    }

    public function cancel(Sale $sale)
    {
        foreach ($sale->items as $item) {
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
                $stock = $this->getProductStock($sale->tenant_id, $item->product_id);
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
    }

    public function generateNumber( int $tenantId, ?string $series): string 
    {
        $series = $series ?: 'FT';
        $last = Sale::query()->where('tenant_id', $tenantId)->where('series', $series)->lockForUpdate()->orderByDesc('id')->first();
        $sequence = 1;

        if ($last) {
            $number = preg_replace( '/^' . preg_quote($series, '/') . '-/', '', $last->number);
            if (is_numeric($number)) {
                $sequence = ((int) $number) + 1;
            }
        }
        return $series . '-' . str_pad($sequence, 6, '0', STR_PAD_LEFT);
    }    
}
