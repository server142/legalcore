<?php

namespace App\Livewire\Dashboards;

use Livewire\Component;

use App\Models\User;
use App\Models\Tenant;
use App\Models\Factura;
use App\Models\ExpedientePago;

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
        
        // Calcular ingresos del mes actual (todos los tenants)
        try {
            // Ingresos de suscripciones (Payments)
            $incomeSuscripciones = \App\Models\Payment::whereMonth('payment_date', now()->month)
                ->whereYear('payment_date', now()->year)
                ->where('status', 'completed')
                ->sum('amount');
            
            // Ingresos de facturas pagadas (todos los tenants)
            $incomeFacturas = Factura::where('estado', 'pagada')
                ->whereMonth('fecha_pago', now()->month)
                ->whereYear('fecha_pago', now()->year)
                ->sum('total');
            
            // Ingresos de anticipos (todos los tenants)
            $incomeAnticipos = ExpedientePago::where('tipo_pago', 'anticipo')
                ->whereMonth('fecha_pago', now()->month)
                ->whereYear('fecha_pago', now()->year)
                ->sum('monto');
            
            // Sumar todos los ingresos
            $this->monthlyIncome = $incomeSuscripciones + $incomeFacturas + $incomeAnticipos;
        } catch (\Throwable $e) {
            $this->monthlyIncome = 0;
            \Illuminate\Support\Facades\Log::warning('SuperAdmin Dashboard: Error al calcular ingresos. ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.dashboards.super-admin');
    }
}
