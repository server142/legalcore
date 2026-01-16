<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Tenant;
use Livewire\WithPagination;

class TrialManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $filterStatus = 'all'; // all, trial, active, expired

    public function convertToPaid($tenantId, $plan)
    {
        $tenant = Tenant::find($tenantId);
        $tenant->update([
            'plan' => $plan,
            'subscription_ends_at' => now()->addMonth(),
            'is_active' => true,
        ]);

        session()->flash('message', 'Tenant convertido a plan pagado exitosamente.');
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

    public function deactivateTenant($tenantId)
    {
        $tenant = Tenant::find($tenantId);
        $tenant->update(['is_active' => false]);

        session()->flash('message', 'Tenant desactivado.');
    }

    public function render()
    {
        $query = Tenant::query()->with('users');

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        if ($this->filterStatus === 'trial') {
            $query->where('plan', 'trial');
        } elseif ($this->filterStatus === 'active') {
            $query->where('is_active', true)->where('plan', '!=', 'trial');
        } elseif ($this->filterStatus === 'expired') {
            $query->where('trial_ends_at', '<', now())->where('plan', 'trial');
        }

        $tenants = $query->latest()->paginate(20);

        return view('livewire.admin.trial-management', [
            'tenants' => $tenants
        ]);
    }
}
