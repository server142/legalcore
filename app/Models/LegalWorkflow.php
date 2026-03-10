<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegalWorkflow extends Model
{
    protected $fillable = ['name', 'description', 'materia', 'icon', 'steps', 'is_active'];

    protected $casts = [
        'steps' => 'array',
        'is_active' => 'boolean',
    ];
}
