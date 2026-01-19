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
    }

    public function render()
    {
        return view('livewire.dashboards.abogado');
    }
}
