<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BelongsToTenant;

class Factura extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'cliente_id',
        'subtotal',
        'iva',
        'total',
        'moneda',
        'estado',
        'uuid_fiscal',
        'conceptos',
        'fecha_emision',
        'fecha_vencimiento',
        'fecha_pago',
    ];

    protected $casts = [
        'conceptos' => 'array',
        'fecha_emision' => 'datetime',
        'fecha_vencimiento' => 'datetime',
        'fecha_pago' => 'datetime',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
}
