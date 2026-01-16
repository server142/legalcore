<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BelongsToTenant;

class Actuacion extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $table = 'actuaciones';

    protected $fillable = [
        'tenant_id',
        'expediente_id',
        'fecha',
        'titulo',
        'descripcion',
        'fecha_vencimiento',
        'es_plazo',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'date',
            'fecha_vencimiento' => 'date',
            'es_plazo' => 'boolean',
        ];
    }

    public function expediente()
    {
        return $this->belongsTo(Expediente::class);
    }
}
