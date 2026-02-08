<?php

namespace App\Livewire\Clientes;

use Livewire\Component;

use App\Models\Cliente;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination, \App\Traits\Auditable;

    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function delete($id)
    {
        $cliente = Cliente::where('tenant_id', auth()->user()->tenant_id)->findOrFail($id);
        
        $nombre = $cliente->nombre;
        $cliente->delete();

        $this->logAudit('eliminar', 'Clientes', "Eliminó al cliente: {$nombre}", ['cliente_id' => $id]);

        $this->dispatch('notify', 'Cliente eliminado correctamente.');
    }

    public function render()
    {
        $clientes = Cliente::where('tenant_id', auth()->user()->tenant_id)
            ->where(function($q) {
                $q->where('nombre', 'like', '%' . $this->search . '%')
                  ->orWhere('rfc', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(10);

        return view('livewire.clientes.index', [
            'clientes' => $clientes
        ]);
    }
}
