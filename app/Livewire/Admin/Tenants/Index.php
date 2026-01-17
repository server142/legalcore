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
    public $confirmingPlanChange = false;
    public $selectedTenantId;
    public $selectedPlanId;

    public function mount()
    {
        //
    }

    public function openPlanChangeModal($tenantId)
    {
        $this->selectedTenantId = $tenantId;
        $tenant = Tenant::find($tenantId);
        $this->selectedPlanId = $tenant->plan_id;
        $this->confirmingPlanChange = true;
    }

    public function changePlan()
    {
        $tenant = Tenant::find($this->selectedTenantId);
        $plan = Plan::find($this->selectedPlanId);
        
        if (!$tenant || !$plan) {
            return;
        }

        $tenant->update([
            'plan' => $plan->slug,
            'plan_id' => $plan->id,
            // Si pasamos de trial a pagado, actualizamos fechas
            'subscription_ends_at' => now()->addDays($plan->duration_in_days),
            'is_active' => true,
        ]);

        $this->confirmingPlanChange = false;
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
