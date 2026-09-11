<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductLot extends Model
{
    protected $fillable = [
        'tenant_id',
        'product_id',

        'lot_number',

        'manufactured_at',
        'expires_at',

        'initial_quantity',
        'current_quantity',
        'reserved_quantity',

        'cost_price',

        'is_active',

        'notes',

        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'manufactured_at' => 'date',
        'expires_at' => 'date',

        'initial_quantity' => 'decimal:3',
        'current_quantity' => 'decimal:3',
        'reserved_quantity' => 'decimal:3',

        'cost_price' => 'decimal:2',

        'is_active' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELACIONAMENTOS
    |--------------------------------------------------------------------------
    */

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function saleItemLots()
    {
        return $this->hasMany(SaleItemLot::class);
    }

    /*
    |--------------------------------------------------------------------------
    | ATRIBUTOS
    |--------------------------------------------------------------------------
    */

    public function getAvailableQuantityAttribute()
    {
        return max(
            0,
            (float) $this->current_quantity - (float) $this->reserved_quantity
        );
    }

    public function getIsExpiredAttribute(): bool
    {
        if (!$this->expires_at) {
            return false;
        }

        return $this->expires_at->isPast();
    }

    public function getDaysToExpireAttribute(): ?int
    {
        if (!$this->expires_at) {
            return null;
        }

        return now()->startOfDay()->diffInDays(
            $this->expires_at,
            false
        );
    }
}
