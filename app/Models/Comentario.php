<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class Comentario extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'expediente_id',
        'user_id',
        'tenant_id',
        'contenido',
        'parent_id',
    ];

    public function expediente()
    {
        return $this->belongsTo(Expediente::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(Comentario::class, 'parent_id');
    }

    public function respuestas()
    {
        return $this->hasMany(Comentario::class, 'parent_id')->orderBy('created_at', 'asc');
    }

    public function reacciones()
    {
        return $this->hasMany(ComentarioReaccion::class);
    }

    public function miReaccion()
    {
        return $this->reacciones()->where('user_id', auth()->id())->first();
    }
}
