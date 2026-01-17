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
        'is_active',
    ];

    protected $casts = [
        'features' => 'array',
        'is_active' => 'boolean',
        'price' => 'decimal:2',
        'max_admin_users' => 'integer',
        'max_lawyer_users' => 'integer',
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
}
