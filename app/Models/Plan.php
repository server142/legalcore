<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'stripe_price_id',
        'price',
        'duration_in_days',
        'features',
        'max_admin_users',
        'max_lawyer_users',
        'max_expedientes',
        'storage_limit_gb',
        'is_active',
    ];

    protected $casts = [
        'features' => 'array',
        'is_active' => 'boolean',
        'price' => 'decimal:2',
        'max_admin_users' => 'integer',
        'max_lawyer_users' => 'integer',
        'max_expedientes' => 'integer',
        'storage_limit_gb' => 'integer',
    ];

    public function tenants()
    {
        return $this->hasMany(Tenant::class);
    }

    /**
     * Verificar si el plan tiene límite de abogados
     */
    public function hasLawyerLimit(): bool
    {
        return $this->max_lawyer_users !== null;
    }

    /**
     * Verificar si un tenant puede agregar más usuarios admin
     */
    public function canAddAdminUser(Tenant $tenant): bool
    {
        $currentAdminCount = $tenant->users()->whereHas('roles', function ($query) {
            $query->where('name', 'admin');
        })->count();

        return $currentAdminCount < $this->max_admin_users;
    }

    /**
     * Verificar si un tenant puede agregar más usuarios abogados
     */
    public function canAddLawyerUser(Tenant $tenant): bool
    {
        // Si no hay límite, siempre puede agregar
        if (!$this->hasLawyerLimit()) {
            return true;
        }

        $currentLawyerCount = $tenant->users()->whereHas('roles', function ($query) {
            $query->where('name', 'abogado');
        })->count();

        return $currentLawyerCount < $this->max_lawyer_users;
    }

    /**
     * Obtener el número de abogados disponibles para agregar
     */
    public function getRemainingLawyerSlots(Tenant $tenant): ?int
    {
        if (!$this->hasLawyerLimit()) {
            return null; // Ilimitado
        }

        $currentLawyerCount = $tenant->users()->whereHas('roles', function ($query) {
            $query->where('name', 'abogado');
        })->count();

        return max(0, $this->max_lawyer_users - $currentLawyerCount);
    }

    /**
     * Verificar si un tenant tiene espacio disponible
     */
    public function hasStorageAvailable(Tenant $tenant, $bytesToAdd = 0): bool
    {
        $limitBytes = $this->storage_limit_gb * 1024 * 1024 * 1024;
        $usedBytes = Documento::where('tenant_id', $tenant->id)->sum('size');

        return ($usedBytes + $bytesToAdd) <= $limitBytes;
    }

    /**
     * Obtener el porcentaje de almacenamiento usado
     */
    public function getStorageUsagePercentage(Tenant $tenant): float
    {
        $limitBytes = $this->storage_limit_gb * 1024 * 1024 * 1024;
        if ($limitBytes <= 0) return 0;
        
        $usedBytes = Documento::where('tenant_id', $tenant->id)->sum('size');

        return round(($usedBytes / $limitBytes) * 100, 2);
    }

    /**
     * Verificar si un tenant puede crear más expedientes
     */
    public function canAddExpediente(Tenant $tenant): bool
    {
        // 0 = ilimitado
        if ($this->max_expedientes == 0) {
            return true;
        }

        $currentCount = \App\Models\Expediente::where('tenant_id', $tenant->id)->count();
        return $currentCount < $this->max_expedientes;
    }

    /**
     * Obtener el número de expedientes disponibles para crear
     */
    public function getRemainingExpedienteSlots(Tenant $tenant): ?int
    {
        if ($this->max_expedientes == 0) {
            return null; // Ilimitado
        }

        $currentCount = \App\Models\Expediente::where('tenant_id', $tenant->id)->count();
        return max(0, $this->max_expedientes - $currentCount);
    }

    /**
     * Obtener el porcentaje de expedientes usados
     */
    public function getExpedienteUsagePercentage(Tenant $tenant): float
    {
        if ($this->max_expedientes == 0) {
            return 0; // Ilimitado
        }

        $currentCount = \App\Models\Expediente::where('tenant_id', $tenant->id)->count();
        return round(($currentCount / $this->max_expedientes) * 100, 2);
    }
}
