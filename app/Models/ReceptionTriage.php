<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceptionTriage extends Model
{
    use HasFactory;

    protected $fillable = [
        'reception_appointment_id',
        'business_area',
        'company_size',
        'need_type',
        'urgency',
        'business_situation',
        'presented_problem',
        'requested_solution',
        'documents_presented',
        'documents_pending',
        'notes',
    ];

    public function appointment()
    {
        return $this->belongsTo(ReceptionAppointment::class, 'reception_appointment_id');
    }
}
