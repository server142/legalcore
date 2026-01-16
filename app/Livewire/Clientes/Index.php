<?php

namespace App\Livewire\Clientes;

use Livewire\Component;

use App\Models\Cliente;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $clientes = Cliente::where('nombre', 'like', '%' . $this->search . '%')
            ->orWhere('rfc', 'like', '%' . $this->search . '%')
            ->latest()
            ->paginate(10);

        return view('livewire.clientes.index', [
            'clientes' => $clientes
        ]);
    }
}
