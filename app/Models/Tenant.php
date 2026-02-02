<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Cashier\Billable;

class Tenant extends Model
{
    use HasFactory, SoftDeletes, Billable;
    
    /**
     * Get the name of the Stripe ID column.
     *
     * @return string
     */
    public function stripeId()
    {
        return $this->stripe_customer_id;
    }

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

    public function getPlanModelAttribute()
    {
        if ($this->plan_id) {
            return $this->planRelation;
        }
        return Plan::where('slug', $this->plan)->first();
    }

    public function isOnTrial()
    {
        // Soporte para datos legados: Si es trial y no tiene fecha, permitir acceso si está activo
        if ($this->is_active && $this->plan === 'trial' && $this->trial_ends_at === null) {
            return true;
        }

        return $this->is_active && $this->plan === 'trial' && $this->trial_ends_at && $this->trial_ends_at->isFuture();
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
        // Soporte para datos legados: Si no es trial y no tiene fecha, permitir acceso si está activo
        if ($this->is_active && $this->plan !== 'trial' && $this->subscription_ends_at === null) {
            return true;
        }

        // Si no es trial y tiene fecha futura, es una suscripción válida
        return $this->is_active && 
               $this->plan !== 'trial' && 
               $this->subscription_ends_at && 
               $this->subscription_ends_at->isFuture();
    }

    public function isOnGracePeriod()
    {
        return $this->is_active && $this->subscription_status === 'grace_period' && $this->grace_period_ends_at && $this->grace_period_ends_at->isFuture();
    }

    public function getExpirationDateAttribute()
    {
        return $this->plan === 'trial' ? $this->trial_ends_at : $this->subscription_ends_at;
    }

    public function getIsExpiredAttribute()
    {
        $date = $this->expiration_date;
        return $date ? $date->isPast() : true;
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
        return $this->users()->withoutGlobalScopes()->whereHas('roles', function ($query) use ($role) {
            $query->where('name', $role);
        })->count();
    }

    /**
     * Obtener el conteo total de usuarios (para el SuperAdmin)
     */
    public function getUsersCountAttribute()
    {
        return \App\Models\User::withoutGlobalScopes()->where('tenant_id', $this->id)->count();
    }

    /**
     * Obtener el conteo de expedientes
     */
    public function getExpedientesCountAttribute()
    {
        return \App\Models\Expediente::withoutGlobalScopes()->where('tenant_id', $this->id)->count();
    }

    /**
     * Obtener la fecha de última actividad (desde logs)
     */
    public function getLastActivityAttribute()
    {
        return \App\Models\AuditLog::withoutGlobalScopes()
            ->where('tenant_id', $this->id)
            ->latest()
            ->value('created_at');
    }

    /**
     * Obtener el almacenamiento usado en bytes
     */
    public function getStorageUsedBytesAttribute()
    {
        return \App\Models\Documento::withoutGlobalScopes()->where('tenant_id', $this->id)->sum('size');
    }

    /**
     * Obtener el almacenamiento formateado (MB/GB)
     */
    public function getStorageUsageFormattedAttribute()
    {
        $bytes = $this->storage_used_bytes;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) $bytes /= 1024;
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
