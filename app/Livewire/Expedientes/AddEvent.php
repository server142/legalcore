<?php

namespace App\Livewire\Expedientes;

use Livewire\Component;
use App\Models\Evento;
use App\Models\Expediente;

class AddEvent extends Component
{
    public Expediente $expediente;
    public $titulo;
    public $descripcion;
    public $start_time;
    public $end_time;
    public $tipo = 'audiencia';

    public function mount(Expediente $expediente)
    {
        $this->expediente = $expediente;
        $this->start_time = now()->format('Y-m-d\TH:i');
        $this->end_time = now()->addHour()->format('Y-m-d\TH:i');
    }

    protected $rules = [
        'titulo' => 'required|string|max:255',
        'start_time' => 'required|date',
        'end_time' => 'required|date|after:start_time',
        'tipo' => 'required|in:audiencia,cita,termino,otro',
    ];

    public function save()
    {
        $this->validate();

        Evento::create([
            'tenant_id' => auth()->user()->tenant_id,
            'expediente_id' => $this->expediente->id,
            'user_id' => auth()->id(),
            'titulo' => $this->titulo,
            'descripcion' => $this->descripcion,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'tipo' => $this->tipo,
        ]);

        \App\Models\AuditLog::create([
            'user_id' => auth()->id(),
            'accion' => 'create',
            'modulo' => 'agenda',
            'descripcion' => "Agendó evento: {$this->titulo}",
            'metadatos' => ['expediente_id' => $this->expediente->id, 'tipo' => $this->tipo],
            'ip_address' => request()->ip(),
        ]);

        $this->dispatch('event-added');
        $this->reset(['titulo', 'descripcion', 'tipo']);
        $this->start_time = now()->format('Y-m-d\TH:i');
        $this->end_time = now()->addHour()->format('Y-m-d\TH:i');
    }

    public function render()
    {
        return view('livewire.expedientes.add-event');
    }
}
