<?php

namespace App\Livewire\Terminos;

use Livewire\Component;
use App\Models\Actuacion;
use App\Models\Expediente;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $filtro_estado = 'pendiente'; // pendiente, cumplido, vencido

    public function render()
    {
        if (!auth()->user()->can('view terminos')) {
            abort(403);
        }

        $query = Actuacion::where('es_plazo', true)
            ->with('expediente.cliente')
            ->when(auth()->user()->hasRole('abogado') && !auth()->user()->can('view all terminos'), function($q) {
                $q->whereHas('expediente', function($qe) {
                    $qe->where('abogado_responsable_id', auth()->id());
                });
            })
            ->when($this->search, function($q) {
                $q->where('titulo', 'like', '%' . $this->search . '%')
                  ->orWhereHas('expediente', function($qe) {
                      $qe->where('numero', 'like', '%' . $this->search . '%')
                         ->orWhere('titulo', 'like', '%' . $this->search . '%');
                  });
            })
            ->when($this->filtro_estado, function($q) {
                if ($this->filtro_estado === 'vencido') {
                    $q->where('estado', 'pendiente')->where('fecha_vencimiento', '<', now()->toDateString());
                } else {
                    $q->where('estado', $this->filtro_estado);
                }
            })
            ->orderBy('fecha_vencimiento', 'asc');

        return view('livewire.terminos.index', [
            'terminos' => $query->paginate(10)
        ]);
    }

    public function marcarComoCumplido($id)
    {
        $actuacion = Actuacion::findOrFail($id);
        $actuacion->update(['estado' => 'cumplido']);
        $this->dispatch('notify', 'Término marcado como cumplido');
    }
}
