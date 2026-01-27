<?php

namespace App\Livewire\Asesorias;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Asesoria;
use Carbon\Carbon;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $filtroEstado = '';
    public $filtroTipo = '';
    public $filtroFecha = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $user = auth()->user();
        
        $query = Asesoria::query()
            ->with(['cliente', 'abogado']);

        // Filtros de búsqueda
        if ($this->search) {
            $query->where(function($q) {
                $q->where('nombre_prospecto', 'like', '%' . $this->search . '%')
                  ->orWhere('folio', 'like', '%' . $this->search . '%')
                  ->orWhere('asunto', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filtroEstado) {
            $query->where('estado', $this->filtroEstado);
        }

        if ($this->filtroTipo) {
            $query->where('tipo', $this->filtroTipo);
        }

        if ($this->filtroFecha) {
            if ($this->filtroFecha == 'hoy') {
                $query->whereDate('fecha_hora', Carbon::today());
            } elseif ($this->filtroFecha == 'semana') {
                $query->whereBetween('fecha_hora', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
            } elseif ($this->filtroFecha == 'mes') {
                $query->whereMonth('fecha_hora', Carbon::now()->month);
            }
        }

        // Permisos: Abogados solo ven las suyas a menos que tengan permiso especial
        if ($user->hasRole('abogado') && !$user->can('view all asesorias')) {
            $query->where('abogado_id', $user->id);
        }

        $asesorias = $query->orderBy('fecha_hora', 'desc')->paginate(10);

        // Estadísticas para tarjetas superiores
        $stats = [
            'hoy' => Asesoria::whereDate('fecha_hora', Carbon::today())->where('estado', 'agendada')->count(),
            'pendientes' => Asesoria::where('estado', 'agendada')->count(),
            'realizadas_mes' => Asesoria::where('estado', 'realizada')->whereMonth('fecha_hora', Carbon::now()->month)->count(),
        ];

        return view('livewire.asesorias.index', [
            'asesorias' => $asesorias,
            'stats' => $stats
        ]);
    }
}
