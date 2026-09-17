<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceptionService extends Model
{
    use HasFactory;

    protected $fillable = [
        'reception_appointment_id',
        'product_id',
        'name',
        'quantity',
        'unit_price',
        'discount',
        'total',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    protected static function booted()
    {
        static::saving(function ($service) {
            $service->total = max(0, ($service->quantity * $service->unit_price) - $service->discount);
        });
    }

    public function appointment()
    {
        return $this->belongsTo(ReceptionAppointment::class, 'reception_appointment_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
