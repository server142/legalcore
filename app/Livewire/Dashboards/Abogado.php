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

    public function mount()
    {
        $this->misExpedientesCount = Expediente::where('abogado_responsable_id', auth()->id())->count();
        $this->proximasAudienciasCount = Evento::where('user_id', auth()->id())
            ->where('tipo', 'audiencia')
            ->where('start_time', '>=', now())
            ->count();
        $this->misExpedientes = Expediente::where('abogado_responsable_id', auth()->id())
            ->with('cliente')
            ->latest()
            ->take(10)
            ->get();
    }

    public function render()
    {
        return view('livewire.dashboards.abogado');
    }
}
