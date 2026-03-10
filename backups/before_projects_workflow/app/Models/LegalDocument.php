<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BelongsToTenant;

class LegalDocument extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'nombre',
        'tipo',
        'texto',
        'version',
        'activo',
        'fecha_publicacion',
        'requiere_aceptacion',
        'visible_en',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'requiere_aceptacion' => 'boolean',
        'fecha_publicacion' => 'datetime',
        'visible_en' => 'array',
    ];

    public function acceptances()
    {
        return $this->hasMany(LegalAcceptance::class);
    }

    public function scopeForTenant($query, $tenantId = null)
    {
        return $query->where(function ($q) use ($tenantId) {
            $q->whereNull('tenant_id');
            if ($tenantId) {
                $q->orWhere('tenant_id', $tenantId);
            }
        });
    }
}
