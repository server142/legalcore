<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantBillingDetail extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tenant_id',
        'razon_social',
        'rfc',
        'regimen_fiscal',
        'codigo_postal',
        'direccion_fiscal',
        'uso_cfdi',
        'email_facturacion',
        'verified',
    ];

    /**
     * Get the tenant that owns the billing details.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
