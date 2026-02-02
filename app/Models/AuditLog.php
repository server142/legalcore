<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class AuditLog extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'user_id',
        'accion',
        'modulo',
        'descripcion',
        'metadatos',
        'ip_address',
        'user_agent',
        'severity',
        'browser',
        'os',
        'device',
        'session_id',
    ];

    protected $casts = [
        'metadatos' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
