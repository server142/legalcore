<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BelongsToTenant;
use App\Models\EstadoProcesal;

class Expediente extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'numero',
        'titulo',
        'materia',
        'juzgado',
        'nombre_juez',
        'estado_procesal',
        'estado_procesal_id',
        'cliente_id',
        'abogado_responsable_id',
        'descripcion',
        'fecha_inicio',
        'fecha_cierre',
        'costo_total',
        'anticipo',
        'saldo_pendiente',
        'estado_cobro',
    ];

    protected function casts(): array
    {
        return [
            'fecha_inicio' => 'date',
            'fecha_cierre' => 'date',
            'costo_total' => 'decimal:2',
            'anticipo' => 'decimal:2',
            'saldo_pendiente' => 'decimal:2',
        ];
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function abogado()
    {
        return $this->belongsTo(User::class, 'abogado_responsable_id');
    }

    public function estadoProcesal()
    {
        return $this->belongsTo(EstadoProcesal::class, 'estado_procesal_id');
    }

    public function actuaciones()
    {
        return $this->hasMany(Actuacion::class);
    }

    public function documentos()
    {
        return $this->hasMany(Documento::class);
    }

    public function eventos()
    {
        return $this->hasMany(Evento::class);
    }

    public function assignedUsers()
    {
        return $this->belongsToMany(User::class, 'expediente_user');
    }

    public function comentarios()
    {
        return $this->hasMany(Comentario::class)->orderBy('created_at', 'desc');
    }

    public function pagos()
    {
        return $this->hasMany(ExpedientePago::class);
    }

    public function getTotalPagadoAttribute()
    {
        return $this->pagos()->sum('monto');
    }
}
