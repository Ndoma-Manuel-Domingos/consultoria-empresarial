<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'product_id',
        'product_lot_id',

        'type',

        'quantity',

        'stock_before',
        'stock_after',

        'unit_cost',
        'total_cost',

        'reference',

        'document_type',
        'document_number',

        'reason',
        'notes',

        'movement_date',

        'created_by',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',

        'stock_before' => 'decimal:3',
        'stock_after' => 'decimal:3',

        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',

        'movement_date' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function productLot(): BelongsTo
    {
        return $this->belongsTo(ProductLot::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    public function isEntry(): bool
    {
        return $this->type === 'entry';
    }

    public function isExit(): bool
    {
        return $this->type === 'exit';
    }

    public function isAdjustment(): bool
    {
        return $this->type === 'adjustment';
    }

    public function isTransfer(): bool
    {
        return $this->type === 'transfer';
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'entry' => 'Entrada',
            'exit' => 'Saída',
            'adjustment' => 'Ajuste',
            'transfer' => 'Transferência',
            default => ucfirst($this->type),
        };
    }
}
