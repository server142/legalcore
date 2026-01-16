<?php

namespace App\Livewire\Expedientes;

use Livewire\Component;

use App\Models\Actuacion;
use App\Models\Expediente;

class AddActuacion extends Component
{
    public Expediente $expediente;
    public $titulo;
    public $descripcion;
    public $fecha;
    public $fecha_vencimiento;
    public $es_plazo = false;

    public function mount(Expediente $expediente)
    {
        $this->expediente = $expediente;
        $this->fecha = now()->format('Y-m-d');
    }

    protected $rules = [
        'titulo' => 'required|string|max:255',
        'fecha' => 'required|date',
        'descripcion' => 'nullable|string',
    ];

    public function save()
    {
        $this->validate();

        Actuacion::create([
            'expediente_id' => $this->expediente->id,
            'titulo' => $this->titulo,
            'descripcion' => $this->descripcion,
            'fecha' => $this->fecha,
            'fecha_vencimiento' => $this->fecha_vencimiento,
            'es_plazo' => $this->es_plazo,
            'estado' => 'pendiente',
        ]);

        $this->dispatch('actuacion-added');
        $this->reset(['titulo', 'descripcion', 'fecha_vencimiento', 'es_plazo']);
        $this->fecha = now()->format('Y-m-d');
    }

    public function render()
    {
        return view('livewire.expedientes.add-actuacion');
    }
}
