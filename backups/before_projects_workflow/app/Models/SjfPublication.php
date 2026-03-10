<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SjfPublication extends Model
{
    protected $fillable = [
        'reg_digital',
        'rubro',
        'texto',
        'precedentes',
        'localizacion',
        'fecha_publicacion',
        'tipo_tesis',
        'instancia',
        'materia',
        'embedding_data',
    ];

    protected $casts = [
        'fecha_publicacion' => 'date',
        'embedding_data' => 'array',
    ];
}
