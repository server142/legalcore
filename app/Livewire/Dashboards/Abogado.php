<?php

namespace App\Livewire\Dashboards;

use Livewire\Component;

use App\Models\Expediente;
use App\Models\Evento;

class Abogado extends Component
{
    public $misExpedientesCount;
    public $proximasAudienciasCount;
    public $misExpedientes;
    public $urgentTerminos;
    public $eventos;

    public function mount()
    {
        $userId = auth()->id();

        $expedienteQuery = Expediente::where(function($q) use ($userId) {
            $q->where('abogado_responsable_id', $userId)
              ->orWhereHas('assignedUsers', function($query) use ($userId) {
                  $query->where('user_id', $userId);
              });
        });

        $this->misExpedientesCount = (clone $expedienteQuery)->count();
        
        $this->proximasAudienciasCount = Evento::where('user_id', $userId)
            ->where('tipo', 'audiencia')
            ->where('start_time', '>=', now())
            ->count();
            
        $this->misExpedientes = (clone $expedienteQuery)
            ->with('cliente')
            ->latest()
            ->take(10)
            ->get();

        $this->urgentTerminos = \App\Models\Actuacion::where('es_plazo', true)
            ->where('estado', 'pendiente')
            ->whereHas('expediente', function($q) use ($userId) {
                $q->where('abogado_responsable_id', $userId)
                  ->orWhereHas('assignedUsers', function($query) use ($userId) {
                      $query->where('user_id', $userId);
                  });
            })
            ->orderBy('fecha_vencimiento', 'asc')
            ->take(5)
            ->get();

        // Logic for "Próximos 7 días" (Agenda)
        $agendaQuery = Evento::with('user');
        if (auth()->user()->hasRole('abogado') && !auth()->user()->can('view all expedientes')) {
            $agendaQuery->where(function($q) use ($userId) {
                $q->where('user_id', $userId)
                  ->orWhereHas('expediente', function($qe) use ($userId) {
                      $qe->where('abogado_responsable_id', $userId)
                         ->orWhereHas('assignedUsers', function($qu) use ($userId) {
                             $qu->where('users.id', $userId);
                         });
                  })
                  ->orWhereHas('invitedUsers', function($qi) use ($userId) {
                      $qi->where('users.id', $userId);
                  });
            });
        }
        $this->eventos = (clone $agendaQuery)->where('start_time', '>=', now()->startOfDay())
            ->where('start_time', '<=', now()->addDays(7)->endOfDay())
            ->orderBy('start_time')
            ->take(10)
            ->get();
    }

    public function render()
    {
        return view('livewire.dashboards.abogado');
    }
}
