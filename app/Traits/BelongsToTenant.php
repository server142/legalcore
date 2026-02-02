<?php

namespace App\Traits;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Builder;

trait BelongsToTenant
{
    protected static function bootBelongsToTenant()
    {
        static::creating(function ($model) {
            if (empty($model->tenant_id)) {
                if (session()->has('tenant_id')) {
                    $model->tenant_id = session()->get('tenant_id');
                } elseif (auth()->check() && auth()->user()->tenant_id) {
                    $model->tenant_id = auth()->user()->tenant_id;
                }
            }
        });

        static::addGlobalScope('tenant', function (Builder $builder) {
            // Priority: Session exists (for SuperAdmin/Support impersonation or forced context)
            if (session()->has('tenant_id')) {
                $builder->where($builder->getQuery()->from . '.tenant_id', session()->get('tenant_id'));
                return;
            }

            // Apply to all models except when we don't have an auth context yet 
            // and specifically to prevent recursion during the authentication process itself
            if (auth()->check()) {
                $user = auth()->user();
                
                // SuperAdmin can see everything
                if ($user->hasRole('super_admin') || $user->role === 'super_admin') {
                    return;
                }

                $builder->where($builder->getQuery()->from . '.tenant_id', $user->tenant_id);
            }
        });
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
