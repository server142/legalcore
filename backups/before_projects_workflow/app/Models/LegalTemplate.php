<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BelongsToTenant;

class LegalTemplate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'name',
        'description',
        'category',
        'materia',
        'file_path',
        'extension',
        'is_global',
        'extracted_text',
        'placeholders',
        'embedding_data',
        'downloads_count',
    ];

    protected $casts = [
        'placeholders' => 'array',
        'embedding_data' => 'array',
        'is_global' => 'boolean',
    ];

    /**
     * Scope for filtering global and tenant templates.
     */
    public function scopeForTenant($query, $tenantId)
    {
        return $query->where(function($q) use ($tenantId) {
            $q->where('tenant_id', $tenantId)
              ->orWhere('is_global', true);
        });
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
