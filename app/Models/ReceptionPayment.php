<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceptionPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'reception_appointment_id',
        'sale_id',
        'subtotal',
        'discount',
        'total',
        'amount_paid',
        'amount_due',
        'change_amount',
        'payment_method',
        'reference',
        'status',
        'notes',
        'received_by',
        'paid_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'amount_due' => 'decimal:2',
        'change_amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function appointment()
    {
        return $this->belongsTo(ReceptionAppointment::class, 'reception_appointment_id');
    }

    public function receivedBy()
    {
        return $this->belongsTo(User::class, 'received_by');
    }


    public function getPaymentMethodLabelAttribute()
    {
        return match ($this->payment_method) {
            'cash' => 'Numerário',
            'transfer' => 'Transferência bancária',
            'multicaixa' => 'Multicaixa Express',
            'tpa' => 'TPA',
            'reference' => 'Referência',
            'other' => 'Outro',
            default => 'Não definido',
        };
    }


    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'pending' => 'Pendente',
            'partial' => 'Pagamento parcial',
            'paid' => 'Pago',
            'cancelled' => 'Cancelado',
            default => $this->status,
        };
    }
}
