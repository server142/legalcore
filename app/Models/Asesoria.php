<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BelongsToTenant;

class Asesoria extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'cliente_id',
        'abogado_id',
        'factura_id',
        'expediente_id',
        'folio',
        'tipo',
        'estado',
        'nombre_prospecto',
        'telefono',
        'email',
        'asunto',
        'notas',
        'fecha_hora',
        'duracion_minutos',
        'motivo_cancelacion',
        'motivo_no_atencion',
        'resumen',
        'prospecto_acepto',
        'costo',
        'pagado',
        'fecha_pago',
        'link_videoconferencia',
    ];

    protected $casts = [
        'fecha_hora' => 'datetime',
        'fecha_pago' => 'datetime',
        'pagado' => 'boolean',
        'prospecto_acepto' => 'boolean',
        'costo' => 'decimal:2',
    ];

    // Boot para generar folio automático
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($asesoria) {
            if (!$asesoria->folio) {
                $count = static::where('tenant_id', $asesoria->tenant_id)->count() + 1;
                $asesoria->folio = 'ASE-' . str_pad($count, 5, '0', STR_PAD_LEFT);
            }
        });
    }

    // Relaciones
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function abogado()
    {
        return $this->belongsTo(User::class, 'abogado_id');
    }

    public function factura()
    {
        return $this->belongsTo(Factura::class); // Asumiendo que existe el modelo Factura
    }

    public function expediente()
    {
        return $this->belongsTo(Expediente::class);
    }

    public function evento()
    {
        return $this->hasOne(Evento::class);
    }

    // Scopes
    public function scopePendientes($query)
    {
        return $query->where('estado', 'agendada')
                     ->where('fecha_hora', '>=', now());
    }

    public function scopePorAtenderHoy($query)
    {
        return $query->where('estado', 'agendada')
                     ->whereDate('fecha_hora', today());
    }
}
