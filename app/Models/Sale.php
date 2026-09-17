<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model
{
    protected $fillable = [
        'tenant_id',
        'client_id',
        'number',
        'status',
        'sale_date',
        'subtotal',
        'discount',
        'tax_amount',
        'total',
        'paid_amount',
        'change_amount',
        'payment_method',
        'notes',
        'created_by',
        'cancelled_by',
        'cancelled_at',

        'document_type',
        'series',
        'fiscal_number',
        'issued_at',
        'currency',
        'taxable_amount',
        'exempt_amount',
        'balance_due',
    ];

    protected $casts = [
        'sale_date' => 'datetime',
        'cancelled_at' => 'datetime',

        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'change_amount' => 'decimal:2',

        'taxable_amount' => 'decimal:2',
        'exempt_amount' => 'decimal:2',
        'balance_due' => 'decimal:2',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(SalePayment::class);
    }

    public function receptionPayment()
    {
        return $this->hasOne(ReceptionPayment::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function cancelledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }
}
