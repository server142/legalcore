<?php

namespace App\Livewire\Clientes;

use Livewire\Component;
use App\Models\Cliente;
use Illuminate\Validation\Rule;

class Form extends Component
{
    use \App\Traits\Auditable;

    public ?Cliente $cliente = null;
    public $modoEdicion = false;

    // Form fields
    public $nombre;
    public $tipo = 'persona_fisica';
    public $rfc;
    public $email;
    public $telefono;
    public $direccion;

    protected function rules()
    {
        return [
            'nombre' => 'required|string|max:255',
            'tipo' => 'required|in:persona_fisica,persona_moral',
            'rfc' => 'nullable|string|max:13',
            'email' => 'nullable|email|max:255',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string',
        ];
    }

    public function mount(Cliente $cliente = null)
    {
        if ($cliente && $cliente->exists) {
            $this->cliente = $cliente;
            $this->modoEdicion = true;
            
            $this->nombre = $cliente->nombre;
            $this->tipo = $cliente->tipo;
            $this->rfc = $cliente->rfc;
            $this->email = $cliente->email;
            $this->telefono = $cliente->telefono;
            $this->direccion = $cliente->direccion;
        }
    }

    public function save()
    {
        $this->validate();

        $data = [
            'nombre' => $this->nombre,
            'tipo' => $this->tipo,
            'rfc' => $this->rfc,
            'email' => $this->email,
            'telefono' => $this->telefono,
            'direccion' => $this->direccion,
        ];

        if ($this->modoEdicion) {
            $this->cliente->update($data);
            $this->logAudit('editar', 'Clientes', "Actualizó al cliente: {$this->nombre}", [
                'cliente_id' => $this->cliente->id,
                'cambios' => $data
            ]);
            session()->flash('message', 'Cliente actualizado exitosamente.');
        } else {
            $cliente = Cliente::create(array_merge($data, [
                'tenant_id' => auth()->user()->tenant_id
            ]));
            $this->logAudit('crear', 'Clientes', "Registró al cliente: {$this->nombre}", [
                'cliente_id' => $cliente->id,
                'rfc' => $this->rfc
            ]);
            session()->flash('message', 'Cliente creado exitosamente.');
        }

        return redirect()->route('clientes.index');
    }

    public function render()
    {
        return view('livewire.clientes.form');
    }
}
