<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ReceptionAppointment;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SalePayment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class SalesService
{
    public function __construct(protected StockService $stockService) {}

    public function createFromReception(ReceptionAppointment $appointment, $receptionPayment): Sale 
    {
        $tenantId = $appointment->tenant_id;

        $appointment->load([
            'client',
            'services.product',
        ]);

        if ($appointment->services->isEmpty()) {
            throw new RuntimeException('Não é possível emitir a factura sem serviços.');
        }

        $number = $this->stockService->generateNumber(
            $tenantId,
            'FR'
        );

        $subtotal = 0;
        $taxAmount = 0;
        $discount = 0;

        /*
         * VENDA
         */
        $sale = Sale::create([
            'tenant_id' => $tenantId,
            'client_id' => $appointment->client_id,
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
            'notes' => $receptionPayment->notes,
            'created_by' => Auth::user()->id,
        ]);

        /*
         * ITENS
         */
        foreach ($appointment->services as $receptionService) {

            $product = Product::query()
                ->where('tenant_id', $tenantId)
                ->where('id', $receptionService->product_id)
                ->where('is_active', true)
                ->where('is_sellable', true)
                ->lockForUpdate()
                ->first();

            if (!$product) {
                throw new RuntimeException(
                    "Produto/serviço inválido: {$receptionService->name}."
                );
            }

            $quantity = (float) $receptionService->quantity;
            $unitPrice = (float) $receptionService->unit_price;
            $itemDiscount = (float) $receptionService->discount;

            $lineSubtotal = ($quantity * $unitPrice) - $itemDiscount;

            if ($lineSubtotal < 0) {
                throw new RuntimeException(
                    "Desconto inválido para {$product->name}."
                );
            }

            /*
             * IVA
             */
            $taxRate = 0;

            if ($product->tax_type === 'standard') {
                $taxRate = (float) $product->tax_rate;
            }

            if ($taxRate > 0) {
                $lineTax = $lineSubtotal
                    - ($lineSubtotal / (1 + ($taxRate / 100)));
            } else {
                $lineTax = 0;
            }

            $lineTotal = $lineSubtotal;

            $subtotal += $lineSubtotal;
            $discount += $itemDiscount;
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
             * STOCK
             *
             * Se o produto/serviço não controlar stock,
             * o StockService deve tratar disso internamente.
             */
            if ($product->manage_stock) {
                $this->stockService->removeStock(
                    $product,
                    $quantity,
                    $saleItem,
                    $number
                );
            }
        }

        $total = $subtotal;

        /*
         * PAGAMENTO
         */
        $paidAmount = (float) $receptionPayment->amount_paid;
        $change = (float) $receptionPayment->change_amount;

        $method = $this->mapPaymentMethod(
            $receptionPayment->payment_method
        );

        SalePayment::create([
            'sale_id' => $sale->id,
            'method' => $method,
            'amount' => $paidAmount,
            'reference' => $receptionPayment->reference,
            'notes' => $receptionPayment->notes,
        ]);

        /*
         * ACTUALIZAR VENDA
         */
        $sale->update([
            'subtotal' => $subtotal,
            'discount' => $discount,
            'tax_amount' => $taxAmount,
            'total' => $total,
            'paid_amount' => $paidAmount,
            'change_amount' => $change,
            'payment_method' => $method,
            'series' => 'FR',
            'issued_at' => now(),
        ]);

        return $sale->fresh();
    }

    protected function mapPaymentMethod(?string $method): string
    {
        return match ($method) {
            'cash' => 'cash',
            'multicaixa' => 'multicaixa',
            'transfer' => 'transfer',
            'tpa' => 'tpa',

            // Ajustar conforme a regra do teu sistema
            'reference' => 'transfer',
            'other' => 'cash',

            default => throw new RuntimeException(
                'Método de pagamento inválido para emissão da factura.'
            ),
        };
    }
}