<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComentarioReaccion extends Model
{
    use HasFactory;

    protected $table = 'comentario_reacciones';

    protected $fillable = [
        'comentario_id',
        'user_id',
        'tipo',
    ];

    public function comentario()
    {
        return $this->belongsTo(Comentario::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
