<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BelongsToTenant;

class ExpedientePago extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'expediente_id',
        'factura_id',
        'monto',
        'tipo_pago',
        'fecha_pago',
        'metodo_pago',
        'referencia',
        'notas',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'fecha_pago' => 'date',
    ];

    public function expediente()
    {
        return $this->belongsTo(Expediente::class);
    }

    public function factura()
    {
        return $this->belongsTo(Factura::class);
    }

    public function scopeAnticipos($query)
    {
        return $query->where('tipo_pago', 'anticipo');
    }

    public function scopeParciales($query)
    {
        return $query->where('tipo_pago', 'parcial');
    }

    public function scopeLiquidaciones($query)
    {
        return $query->where('tipo_pago', 'liquidacion');
    }
}
