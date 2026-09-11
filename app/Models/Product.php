<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',

        'code',
        'barcode',
        'name',
        'short_name',

        'type',

        'category',
        'subcategory',
        'brand',
        'model',

        'unit',

        'description',

        'cost_price',
        'sale_price',
        'sale_price_with_tax',
        'margin_percent',

        'tax_type',
        'tax_rate',
        'tax_exemption_code',
        'tax_exemption_reason',

        'manage_stock',
        'allow_negative_stock',
        'minimum_stock',
        'maximum_stock',
        'stock_quantity',

        'manage_lots',
        'has_expiration',
        'expiration_alert_days',

        'supplier_id',

        'is_active',
        'is_sellable',
        'is_purchasable',

        'notes',

        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'cost_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'sale_price_with_tax' => 'decimal:2',
        'margin_percent' => 'decimal:2',

        'tax_rate' => 'decimal:2',

        'minimum_stock' => 'decimal:3',
        'maximum_stock' => 'decimal:3',
        'stock_quantity' => 'decimal:3',

        'manage_stock' => 'boolean',
        'allow_negative_stock' => 'boolean',

        'manage_lots' => 'boolean',
        'has_expiration' => 'boolean',

        'is_active' => 'boolean',
        'is_sellable' => 'boolean',
        'is_purchasable' => 'boolean',

        'expiration_alert_days' => 'integer',
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

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'supplier_id');
    }

    public function lots(): HasMany
    {
        return $this->hasMany(ProductLot::class);
    }

    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function stockBalance()
    {
        return $this->hasOne(StockBalance::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeProducts(Builder $query): Builder
    {
        return $query->where('type', 'product');
    }

    public function scopeServices(Builder $query): Builder
    {
        return $query->where('type', 'service');
    }

    public function scopeSellable(Builder $query): Builder
    {
        return $query->where('is_sellable', true);
    }

    public function scopePurchasable(Builder $query): Builder
    {
        return $query->where('is_purchasable', true);
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'product' => 'Produto',
            'service' => 'Serviço',
            default => ucfirst($this->type),
        };
    }

    public function getTaxTypeLabelAttribute(): string
    {
        return match ($this->tax_type) {
            'standard' => 'IVA normal',
            'exempt' => 'Isento',
            'zero' => 'Taxa 0%',
            default => ucfirst($this->tax_type),
        };
    }

    public function getUnitLabelAttribute(): string
    {
        return match ($this->unit) {
            'UN' => 'Unidade',
            'CX' => 'Caixa',
            'KG' => 'Quilograma',
            'G' => 'Grama',
            'L' => 'Litro',
            'ML' => 'Mililitro',
            'M' => 'Metro',
            'M2' => 'Metro quadrado',
            'M3' => 'Metro cúbico',
            default => $this->unit,
        };
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    public function getCalculatedTaxAttribute(): float
    {
        if ($this->tax_type !== 'standard') {
            return 0;
        }

        return round(((float) $this->sale_price * (float) $this->tax_rate) / 100, 2);
    }

    public function getStockQuantityAttribute()
    {
        if (!$this->manage_stock) {
            return null;
        }
        if ($this->manage_lots) {
            return $this->lots()->sum('current_quantity');
        }

        return $this->stockBalance?->quantity ?? 0;
    }


    public function getCalculatedSalePriceWithTaxAttribute(): float
    {
        return round((float) $this->sale_price + $this->calculated_tax, 2);
    }
}
