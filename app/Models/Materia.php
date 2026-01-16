<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class Materia extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'nombre',
    ];
}
