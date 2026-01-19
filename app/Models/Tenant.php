<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Cashier\Billable;

class Tenant extends Model
{
    use HasFactory, SoftDeletes, Billable;

    protected $fillable = [
        'name',
        'slug',
        'domain',
        'status',
        'plan',
        'plan_id',
        'trial_ends_at',
        'subscription_ends_at',
        'grace_period_ends_at',
        'subscription_status',
        'stripe_customer_id',
        'pm_type',
        'pm_last_four',
        'is_active',
        'settings',
    ];

    protected $casts = [
        'settings' => 'array',
        'trial_ends_at' => 'datetime',
        'subscription_ends_at' => 'datetime',
        'grace_period_ends_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function planRelation()
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }

    public function isOnTrial()
    {
        return $this->plan === 'trial' && $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    public function trialExpired()
    {
        return $this->plan === 'trial' && $this->trial_ends_at && $this->trial_ends_at->isPast();
    }

    public function daysLeftInTrial()
    {
        if (!$this->isOnTrial()) return 0;
        return now()->diffInDays($this->trial_ends_at, false);
    }

    public function isSubscriptionActive()
    {
        return $this->subscription_status === 'active' && $this->subscription_ends_at && $this->subscription_ends_at->isFuture();
    }

    public function isOnGracePeriod()
    {
        return $this->subscription_status === 'grace_period' && $this->grace_period_ends_at && $this->grace_period_ends_at->isFuture();
    }

    /**
     * Verificar si el tenant puede agregar un usuario admin
     */
    public function canAddAdminUser(): bool
    {
        if (!$this->planRelation) {
            return false;
        }

        return $this->planRelation->canAddAdminUser($this);
    }

    /**
     * Verificar si el tenant puede agregar un usuario abogado
     */
    public function canAddLawyerUser(): bool
    {
        if (!$this->planRelation) {
            return false;
        }

        return $this->planRelation->canAddLawyerUser($this);
    }

    /**
     * Obtener el conteo actual de usuarios por rol
     */
    public function getUserCountByRole(string $role): int
    {
        return $this->users()->whereHas('roles', function ($query) use ($role) {
            $query->where('name', $role);
        })->count();
    }
}
