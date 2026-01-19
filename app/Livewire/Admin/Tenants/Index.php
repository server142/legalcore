<?php

namespace App\Livewire\Admin\Tenants;

use Livewire\Component;
use App\Models\Tenant;
use App\Models\Plan;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $filterStatus = 'all'; // all, trial, active, expired, cancelled
    
    // Modal handling
    public $selectedTenantId;
    public $selectedPlanId;
    
    // Edit fields
    public $editName;
    public $editSlug;
    public $editIsActive;

    public $confirmingTenantEdit = false;
    public $confirmingPlanChange = false;

    public function mount()
    {
        //
    }

    public function openPlanChangeModal($tenantId)
    {
        $this->selectedTenantId = $tenantId;
        $tenant = Tenant::find($tenantId);
        $this->selectedPlanId = $tenant->plan_id;
        $this->dispatch('open-modal', 'change-plan-modal');
    }

    public function openEditModal($tenantId)
    {
        $this->selectedTenantId = $tenantId;
        $tenant = Tenant::find($tenantId);
        $this->editName = $tenant->name;
        $this->editSlug = $tenant->slug;
        $this->editIsActive = $tenant->is_active;
        $this->selectedPlanId = $tenant->plan_id;
        $this->dispatch('open-modal', 'edit-tenant-modal');
    }
    public function updateTenant()
    {
        $this->validate([
            'editName' => 'required|string|max:255',
            'editSlug' => 'required|string|max:255|unique:tenants,slug,' . $this->selectedTenantId,
            'selectedPlanId' => 'required|exists:plans,id',
        ]);

        $tenant = Tenant::find($this->selectedTenantId);
        $plan = Plan::find($this->selectedPlanId);

        $tenant->name = $this->editName;
        $tenant->slug = $this->editSlug;
        $tenant->is_active = $this->editIsActive;

        // Si el plan cambió, aplicar la nueva configuración de plan
        if ($tenant->plan_id !== $plan->id) {
            $this->applyPlanToTenant($tenant, $plan);
        } else {
            // Si el plan es el mismo pero se reactivó, asegurar status
            if ($tenant->is_active && $tenant->subscription_status === 'cancelled') {
                $tenant->subscription_status = $tenant->plan === 'trial' ? 'trial' : 'active';
            } elseif (!$tenant->is_active) {
                $tenant->subscription_status = 'cancelled';
            }
            $tenant->save();
        }

        $this->dispatch('close-modal', 'edit-tenant-modal');
        session()->flash('message', "Tenant '{$tenant->name}' actualizado correctamente.");
    }

    private function applyPlanToTenant(Tenant $tenant, Plan $plan)
    {
        $tenant->plan = $plan->slug;
        $tenant->plan_id = $plan->id;
        $tenant->is_active = true;
        $tenant->subscription_status = $plan->slug === 'trial' ? 'trial' : 'active';

        if ($plan->slug === 'trial') {
            $tenant->trial_ends_at = now()->addDays($plan->duration_in_days);
            $tenant->subscription_ends_at = null;
        } else {
            $tenant->subscription_ends_at = now()->addDays($plan->duration_in_days);
            $tenant->trial_ends_at = null;
        }
        
        $tenant->grace_period_ends_at = null;
        $tenant->save();
    }

    public function changePlan()
    {
        $tenant = Tenant::find($this->selectedTenantId);
        $plan = Plan::find($this->selectedPlanId);
        
        if (!$tenant || !$plan) {
            return;
        }

        $this->applyPlanToTenant($tenant, $plan);

        $this->dispatch('close-modal', 'change-plan-modal');
        session()->flash('message', "Plan actualizado a {$plan->name} correctamente.");
    }

    public function extendTrial($tenantId, $days = 30)
    {
        $tenant = Tenant::find($tenantId);
        $newEndDate = $tenant->trial_ends_at ? $tenant->trial_ends_at->addDays($days) : now()->addDays($days);
        
        $tenant->update([
            'trial_ends_at' => $newEndDate,
        ]);

        session()->flash('message', "Trial extendido por {$days} días.");
    }

    public function toggleStatus($tenantId)
    {
        $tenant = Tenant::find($tenantId);
        $tenant->is_active = !$tenant->is_active;
        $tenant->save();

        session()->flash('message', 'Estado del tenant actualizado.');
    }

    public function render()
    {
        $query = Tenant::query()->with(['users', 'planRelation']);

        if ($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('domain', 'like', '%' . $this->search . '%')
                  ->orWhere('slug', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filterStatus === 'trial') {
            $query->where('plan', 'trial');
        } elseif ($this->filterStatus === 'active') {
            $query->where('is_active', true)->where('plan', '!=', 'trial');
        } elseif ($this->filterStatus === 'expired') {
            $query->where('trial_ends_at', '<', now())->where('plan', 'trial');
        } elseif ($this->filterStatus === 'cancelled') {
            $query->where('is_active', false);
        }

        $tenants = $query->latest()->paginate(20);
        $plans = Plan::where('is_active', true)->get();

        return view('livewire.admin.tenants.index', [
            'tenants' => $tenants,
            'plans' => $plans
        ])->layout('layouts.app');
    }
}
