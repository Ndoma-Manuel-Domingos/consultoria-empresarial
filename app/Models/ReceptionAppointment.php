<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class ReceptionAppointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'client_id',
        'code',
        'appointment_date',
        'appointment_time',
        'status',
        'priority',
        'service_type',
        'reason',
        'reception_notes',
        'assigned_user_id',
        'started_at',
        'completed_at',
        'referred_at',
        'referred_by',
        'consultation_status',
        'referral_notes',
    ];


    protected $casts = [
        'appointment_date' => 'date',
        'appointment_time' => 'datetime:H:i',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'referred_at' => 'datetime',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }


    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }
    
    public function services()
    {
        return $this->hasMany(ReceptionService::class, 'reception_appointment_id');
    }
    
    public function payment()
    {
        return $this->hasOne(ReceptionPayment::class, 'reception_appointment_id');
    }

    public function referredBy()
    {
        return $this->belongsTo(User::class, 'referred_by');
    }

    public function triage()
    {
        return $this->hasOne(ReceptionTriage::class, 'reception_appointment_id');
    }

    public function getTotalAttribute()
    {
        return $this->services->sum('total');
    }


    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'waiting' => 'Aguardando',
            'triage' => 'Triagem',
            'awaiting_payment' => 'Aguardando pagamento',
            'paid' => 'Pago',
            'in_progress' => 'Em atendimento',
            'completed' => 'Concluído',
            'cancelled' => 'Cancelado',
            default => $this->status,
        };
    }

    public function getPriorityLabelAttribute()
    {
        return match ($this->priority) {
            'low' => 'Baixa',
            'normal' => 'Normal',
            'high' => 'Alta',
            'urgent' => 'Urgente',
            default => $this->priority,
        };
    }
}
