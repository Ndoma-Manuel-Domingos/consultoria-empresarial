<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Client extends Model
{
    protected $fillable = [
        'tenant_id',
        'type',

        'name',
        'company_name',
        'commercial_name',

        'nif',
        'nif_type',

        'email',
        'phone',
        'phone_secondary',
        'website',

        'address',
        'city',
        'province',
        'municipality',
        'postal_code',
        'country',

        'contact_person',
        'contact_person_phone',
        'contact_person_email',

        'tax_regime',
        'vat_payer',

        'credit_limit',
        'payment_terms',

        'notes',

        'is_active',

        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'vat_payer' => 'boolean',
        'is_active' => 'boolean',
        'credit_limit' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELACIONAMENTOS
    |--------------------------------------------------------------------------
    */

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
