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
    ];

    public function expediente()
    {
        return $this->belongsTo(Expediente::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
