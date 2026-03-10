<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadoProcesal extends Model
{
    protected $table = 'estados_procesales';

    protected $fillable = [
        'nombre',
        'descripcion',
    ];
}
