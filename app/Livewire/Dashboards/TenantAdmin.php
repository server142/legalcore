<?php

namespace App\Livewire\Dashboards;

use Livewire\Component;

use App\Models\Expediente;
use App\Models\Cliente;
use App\Models\Actuacion;

class TenantAdmin extends Component
{
    public $activeExpedientes;
    public $upcomingDeadlines;
    public $totalClientes;
    public $recentExpedientes;
    public $urgentTerminos;
    public $totalCobrado;
    public $pendienteCobro;
    public $facturasMes;

    public function mount()
    {
        $user = auth()->user();
        $isAbogado = $user->hasRole('abogado') && !$user->can('view all expedientes');

        $expedienteQuery = Expediente::query();
        $actuacionQuery = Actuacion::where('es_plazo', true)->where('estado', 'pendiente');

        if ($isAbogado) {
            $expedienteQuery->where('abogado_responsable_id', $user->id);
        }

        // For terminos, check the specific permission
        if ($user->hasRole('abogado') && !$user->can('view all terminos')) {
            $actuacionQuery->whereHas('expediente', function($q) use ($user) {
                $q->where('abogado_responsable_id', $user->id);
            });
        }

        $this->activeExpedientes = (clone $expedienteQuery)->where('estado_procesal', '!=', 'cerrado')->count();
        
        $this->upcomingDeadlines = (clone $actuacionQuery)
            ->where('fecha_vencimiento', '>=', now())
            ->where('fecha_vencimiento', '<=', now()->addDays(7))
            ->count();

        $this->totalClientes = Cliente::count(); // Clientes are usually shared, but could be filtered too if needed
        $this->recentExpedientes = $expedienteQuery->latest()->take(5)->get();
        
        $this->urgentTerminos = (clone $actuacionQuery)
            ->orderBy('fecha_vencimiento', 'asc')
            ->take(5)
            ->get();
        
        // Financial Stats - Only for those who can manage billing
        if ($user->can('manage billing')) {
            $this->totalCobrado = \App\Models\Factura::where('estado', 'pagada')->sum('total');
            $this->pendienteCobro = \App\Models\Factura::where('estado', 'pendiente')->sum('total');
            $this->facturasMes = \App\Models\Factura::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count();
        } else {
            $this->totalCobrado = 0;
            $this->pendienteCobro = 0;
            $this->facturasMes = 0;
        }
    }

    public function render()
    {
        return view('livewire.dashboards.tenant-admin');
    }
}
