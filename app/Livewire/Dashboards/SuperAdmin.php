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

    public function mount()
    {
        $this->totalTenants = Tenant::count();
        $this->activeTenants = Tenant::where('status', 'active')->count();
        $this->totalUsers = User::count();
        $this->tenants = Tenant::latest()->take(10)->get();
    }

    public function render()
    {
        return view('livewire.dashboards.super-admin');
    }
}
