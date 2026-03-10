<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BelongsToTenant;

class Cliente extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'nombre',
        'tipo',
        'rfc',
        'email',
        'telefono',
        'direccion',
        'datos_fiscales',
    ];

    protected $casts = [
        'datos_fiscales' => 'array',
    ];

    public function expedientes()
    {
        return $this->hasMany(Expediente::class);
    }
}
