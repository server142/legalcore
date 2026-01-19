<?php

namespace App\Livewire\Expedientes;

use Livewire\Component;

use App\Models\Expediente;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function cerrar($id)
    {
        $expediente = Expediente::findOrFail($id);
        $expediente->update(['estado_procesal' => 'Cerrado']);
        $this->dispatch('notify', 'Expediente cerrado exitosamente');
    }

    public function render()
    {
        $user = auth()->user();
        $query = Expediente::query();

        if ($user->hasRole('abogado') && !$user->can('view all expedientes')) {
            $query->where(function($q) use ($user) {
                $q->where('abogado_responsable_id', $user->id)
                  ->orWhereHas('assignedUsers', function($q2) use ($user) {
                      $q2->where('users.id', $user->id);
                  });
            });
        }

        $expedientes = $query->where(function($q) {
                $q->where('numero', 'like', '%' . $this->search . '%')
                  ->orWhere('titulo', 'like', '%' . $this->search . '%');
            })
            ->with(['cliente', 'abogado'])
            ->latest()
            ->paginate(10);

        return view('livewire.expedientes.index', [
            'expedientes' => $expedientes
        ]);
    }
}
