<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ReceptionAppointment;
use App\Models\ReceptionPayment;
use App\Models\ReceptionService;
use App\Models\ReceptionTriage;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReceptionServices
{
    public function __construct(protected StockService $stockService, protected SalesService $salesService) {}

    public function createAppointment(int $tenantId,array $data): ReceptionAppointment {

        return DB::transaction(function () use ( $tenantId, $data) 
        {
            $appointment = ReceptionAppointment::create([
                'tenant_id' => $tenantId,
                'client_id' => $data['client_id'],
                'code' => $this->generateCode($tenantId),
                'appointment_date' => $data['appointment_date'],
                'appointment_time' => $data['appointment_time'] ?? null,
                'status' => 'waiting',
                'priority' => $data['priority'] ?? 'normal',
                'service_type' => $data['service_type'] ?? null,
                'reason' => $data['reason'] ?? null,
                'reception_notes' => $data['reception_notes'] ?? null,
                'consultation_status' => 'pending',
            ]);
            return $appointment;
        });
    }

    public function saveTriage( ReceptionAppointment $appointment, array $data): ReceptionTriage 
    {
        return DB::transaction(function () use ( $appointment, $data) 
        {
            $triage = ReceptionTriage::updateOrCreate(['reception_appointment_id' => $appointment->id,], $data);

            $appointment->update([
                //'status' => 'triage',
                'status' => 'awaiting_payment',
            ]);

            return $triage;
        });
    }

    public function saveServices( ReceptionAppointment $appointment, array $services, bool $insert = false): void 
    {
        DB::transaction(function () use ( $appointment, $services, $insert) 
        {
            $appointment->services()->delete();

            foreach ($services as $item) {

                $product = Product::query()
                    ->where('tenant_id', $appointment->tenant_id)
                    ->where('id', $item['product_id'])
                    ->where('is_active', true)
                    ->where('is_sellable', true)
                    ->first();

                if (!$product) {
                    throw ValidationException::withMessages([
                        'services' => 'Um dos serviços selecionados não está disponível.',
                    ]);
                }

                $quantity = (float) $item['quantity'];

                $unitPrice = (float) $product->sale_price;

                $discount = (float) ($item['discount'] ?? 0);

                $subtotal = $quantity * $unitPrice;

                $total = max( 0, $subtotal - $discount);

                ReceptionService::create([
                    'reception_appointment_id' => $appointment->id,
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'discount' => $discount,
                    'total' => $total,
                    'notes' => $item['notes'] ?? null,
                ]);
            }
            if($insert){
                $this->recalculatePayment($appointment);
            }
        });
    }

    public function recalculatePayment(ReceptionAppointment $appointment): ReceptionPayment 
    {
        $appointment->load('services');
        $subtotal = $appointment->services->sum(fn ($service) => (float) $service->quantity * (float) $service->unit_price);
        $discount = $appointment->services->sum(fn ($service) => (float) $service->discount);
        $total = $appointment->services->sum(fn ($service) => (float) $service->total);
        $payment = $appointment->payment;

        if (!$payment) {
            $payment = ReceptionPayment::create([
                'tenant_id' => $appointment->tenant_id,
                'reception_appointment_id' => $appointment->id,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'total' => $total,
                'amount_paid' => 0,
                'amount_due' => $total,
                'change_amount' => 0,
                'status' => 'pending',
            ]);
        } else {
            $amountPaid = (float) $payment->amount_paid;
            $payment->update([
                'subtotal' => $subtotal,
                'discount' => $discount,
                'total' => $total,
                'amount_due' => max( 0, $total - $amountPaid),
            ]);
        }
        return $payment->fresh();
    }

    public function registerPayment( ReceptionAppointment $appointment, array $data, int $userId): ReceptionPayment 
    {
        return DB::transaction(function () use ( $appointment, $data, $userId) 
        {

            if (isset($data['services']) && is_array($data['services'])) {
                $this->saveServices($appointment, $data['services']);
            }

            $payment = $this->recalculatePayment($appointment);

            $total = (float) $payment->total;
            $amountPaid = (float) $data['amount_paid'];

            if ($amountPaid <= 0) {
                throw ValidationException::withMessages([
                    'amount_paid' => 'Informe um valor de pagamento válido.',
                ]);
            }

            $newPaid = $amountPaid;
            $change = max( 0, $newPaid - $total);
            $due = max( 0, $total - $newPaid);

            if ($due <= 0) {
                $status = 'paid';
                $appointmentStatus = 'paid';
            } else {
                $status = 'partial';
                $appointmentStatus = 'awaiting_payment';
            }

            $payment->update([
                'amount_paid' => $newPaid,
                'amount_due' => $due,
                'change_amount' => $change,
                'payment_method' => $data['payment_method'],
                'reference' => $data['reference'] ?? null,
                'status' => $status,
                'notes' => $data['notes'] ?? null,
                'received_by' => $userId,
                'paid_at' => $status === 'paid' ? now() : null,
            ]);

            // Só emitir factura quando estiver pago

            if ($status === 'paid') {

                $sale = $this->salesService->createFromReception(
                    $appointment,
                    $payment->fresh()
                );

                $payment->update([
                    'sale_id' => $sale->id,
                ]);
            }

            $appointment->update([
                'status' => $appointmentStatus,
            ]);

            return $payment->fresh();
        });
    }

    public function referToConsultation( ReceptionAppointment $appointment, int $userId, ?string $notes = null): ReceptionAppointment 
    {

        if ($appointment->status !== 'paid') {
            throw ValidationException::withMessages([
                'appointment' => 'O atendimento precisa estar pago antes do encaminhamento.',
            ]);
        }

        $appointment->update([
            'status' => 'in_progress',
            'consultation_status' => 'ready',
            'referred_at' => now(),
            'referred_by' => $userId,
            'referral_notes' => $notes,
        ]);

        return $appointment->fresh();
    }

    public function start(ReceptionAppointment $appointment): ReceptionAppointment 
    {
        $appointment->update([
            'status' => 'in_progress',
            'started_at' => $appointment->started_at ?? now(),
            'consultation_status' => 'in_consultation',
        ]);

        return $appointment->fresh();
    }

    public function cancel( ReceptionAppointment $appointment, ?string $reason = null): ReceptionAppointment 
    {
        $appointment->update([
            'status' => 'cancelled',
            'reception_notes' => $reason ? trim(($appointment->reception_notes ?? '') . PHP_EOL . 'Cancelamento: ' . $reason) : $appointment->reception_notes,
        ]);

        return $appointment->fresh();
    }

    protected function generateCode(int $tenantId): string
    {
        do {
            $code = 'REC-' . now()->format('Ymd') . '-' . strtoupper(substr( bin2hex(random_bytes(3)), 0, 6));
        } while (ReceptionAppointment::where('tenant_id', $tenantId)->where('code', $code)->exists());

        return $code;
    }
}

