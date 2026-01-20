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
            if (session()->has('tenant_id')) {
                $builder->where('tenant_id', session()->get('tenant_id'));
                return;
            }

            // Prevent infinite recursion: do not apply this scope to the User model
            // because accessing auth()->user() triggers a User query, which triggers this scope.
            if ($builder->getModel() instanceof \App\Models\User) {
                return;
            }

            if (auth()->check()) {
                $user = auth()->user();
                // Super Admin sees everything. Check the 'role' column directly.
                if ($user->role === 'super_admin') {
                    return;
                }
                $builder->where('tenant_id', $user->tenant_id);
            }
        });
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
