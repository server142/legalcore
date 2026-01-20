<?php

namespace App\Livewire\Dashboards;

use Livewire\Component;

use App\Models\Tenant;
use App\Models\User;

class SuperAdmin extends Component
{
    public $totalTenants;
    public $activeTenants;
    public $totalUsers;
    public $tenants;
    public $monthlyIncome;

    public function mount()
    {
        $this->totalTenants = Tenant::count();
        $this->activeTenants = Tenant::where('status', 'active')->count();
        $this->totalUsers = User::count();
        $this->tenants = Tenant::latest()->take(10)->get();
        
        // Calcular ingresos del mes actual
        $this->monthlyIncome = \App\Models\Payment::whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year)
            ->where('status', 'completed')
            ->sum('amount');
    }

    public function render()
    {
        return view('livewire.dashboards.super-admin');
    }
}
