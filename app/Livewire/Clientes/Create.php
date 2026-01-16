<?php

namespace App\Livewire\Clientes;

use Livewire\Component;

use App\Models\Cliente;

class Create extends Component
{
    public $nombre;
    public $tipo = 'persona_fisica';
    public $rfc;
    public $email;
    public $telefono;
    public $direccion;

    protected $rules = [
        'nombre' => 'required|string|max:255',
        'tipo' => 'required|in:persona_fisica,persona_moral',
        'rfc' => 'nullable|string|max:13',
        'email' => 'nullable|email|max:255',
    ];

    public function save()
    {
        $this->validate();

        Cliente::create([
            'nombre' => $this->nombre,
            'tipo' => $this->tipo,
            'rfc' => $this->rfc,
            'email' => $this->email,
            'telefono' => $this->telefono,
            'direccion' => $this->direccion,
        ]);

        session()->flash('message', 'Cliente creado exitosamente.');

        return redirect()->to('/clientes');
    }

    public function render()
    {
        return view('livewire.clientes.create');
    }
}
