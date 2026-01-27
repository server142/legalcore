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
    public $eventos;

    public function mount()
    {
        $user = auth()->user();
        $isAbogado = $user->hasRole('abogado') && !$user->can('view all expedientes');

        $expedienteQuery = Expediente::query();
        $actuacionQuery = Actuacion::where('es_plazo', true)->where('estado', 'pendiente');

        if ($isAbogado) {
            $expedienteQuery->where(function($q) use ($user) {
                $q->where('abogado_responsable_id', $user->id)
                  ->orWhereHas('assignedUsers', function($q2) use ($user) {
                      $q2->where('users.id', $user->id);
                  });
            });
        }

        // For terminos, check the specific permission
        if ($user->hasRole('abogado') && !$user->can('view all terminos')) {
            $actuacionQuery->whereHas('expediente', function($q) use ($user) {
                $q->where('abogado_responsable_id', $user->id)
                  ->orWhereHas('assignedUsers', function($q2) use ($user) {
                      $q2->where('users.id', $user->id);
                  });
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

        // Logic for "Próximos 7 días" (Agenda)
        $agendaQuery = \App\Models\Evento::with('user');
        if ($user->hasRole('abogado') && !$user->can('view all expedientes')) {
            $agendaQuery->where(function($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhereHas('expediente', function($qe) use ($user) {
                      $qe->where('abogado_responsable_id', $user->id)
                         ->orWhereHas('assignedUsers', function($qu) use ($user) {
                             $qu->where('users.id', $user->id);
                         });
                  })
                  ->orWhereHas('invitedUsers', function($qi) use ($user) {
                      $qi->where('users.id', $user->id);
                  });
            });
        }
        $this->eventos = (clone $agendaQuery)->where('start_time', '>=', now()->startOfDay())
            ->where('start_time', '<=', now()->addDays(7)->endOfDay())
            ->orderBy('start_time')
            ->take(10)
            ->get();
        
        // Financial Stats - Only for those who can manage billing
        if ($user->can('manage billing')) {
            try {
                $this->totalCobrado = \App\Models\Factura::where('estado', 'pagada')->sum('total');
                $this->pendienteCobro = \App\Models\Factura::where('estado', 'pendiente')->sum('total');
                $this->facturasMes = \App\Models\Factura::whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->count();
            } catch (\Throwable $e) {
                $this->totalCobrado = 0;
                $this->pendienteCobro = 0;
                $this->facturasMes = 0;
                \Illuminate\Support\Facades\Log::warning('TenantAdmin Dashboard: Error al calcular estadísticas financieras. ' . $e->getMessage());
            }
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
