<?php

namespace App\Livewire\Clientes;

use Livewire\Component;

use App\Models\Cliente;

class Create extends Component
{
    use \App\Traits\Auditable;
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

        $cliente = Cliente::create([
            'nombre' => $this->nombre,
            'tipo' => $this->tipo,
            'rfc' => $this->rfc,
            'email' => $this->email,
            'telefono' => $this->telefono,
            'direccion' => $this->direccion,
        ]);

        // Audit Log
        $this->logAudit('crear', 'Clientes', "Registró al cliente: {$this->nombre}", [
            'cliente_id' => $cliente->id,
            'rfc' => $this->rfc
        ]);

        session()->flash('message', 'Cliente creado exitosamente.');

        return redirect()->to('/clientes');
    }

    public function render()
    {
        return view('livewire.clientes.create');
    }
}
