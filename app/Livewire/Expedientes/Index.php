<?php

namespace App\Livewire\Expedientes;

use Livewire\Component;

use App\Models\Expediente;
use App\Models\EstadoProcesal;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $viewMode = 'list'; // 'list' or 'kanban'
    public $search = ''; // Search term for expedientes

    public function mount()
    {
        // Restore view mode from session if available
        $this->viewMode = session()->get('expedientes_view_mode', 'list');
    }

    public function toggleViewMode($mode)
    {
        if (in_array($mode, ['list', 'kanban'])) {
            $this->viewMode = $mode;
            session()->put('expedientes_view_mode', $mode);
        }
    }

    public function updateStatus($expedienteId, $newStatusId)
    {
        $expediente = Expediente::findOrFail($expedienteId);
        
        // Security check: ensure user has access to this expediente
        $user = auth()->user();
        
        // Super admins and users with 'view all' can move anything
        if (!$user->hasRole('super_admin') && !$user->can('view all expedientes')) {
            if ($expediente->abogado_responsable_id !== $user->id && 
                !$expediente->assignedUsers()->where('users.id', $user->id)->exists()) {
                
                $this->dispatch('notify', 'No tienes permiso para mover este expediente.');
                return;
            }
        }

        // Handle move to "Sin Clasificar" (null)
        if ($newStatusId === null || $newStatusId === 'null' || $newStatusId === '') {
            $expediente->update([
                'estado_procesal_id' => null,
                'estado_procesal' => 'Sin Clasificar'
            ]);
            $this->dispatch('notify', "Expediente movido a Sin Clasificar");
            return;
        }

        $status = EstadoProcesal::find($newStatusId);
        if ($status) {
            $expediente->update([
                'estado_procesal_id' => $status->id,
                'estado_procesal' => $status->nombre // Legacy field sync
            ]);
            $this->dispatch('notify', "Expediente movido a {$status->nombre}");
        }
    }

    public function render()
    {
        $user = auth()->user();
        
        // Base Query Builder
        $queryBuilder = function() use ($user) {
            $q = Expediente::query();
            
            if ($user->hasRole('super_admin')) {
                // Super admin sees everything
            } elseif ($user->hasRole('abogado') && !$user->can('view all expedientes')) {
                $q->where(function($sq) use ($user) {
                    $sq->where('abogado_responsable_id', $user->id)
                      ->orWhereHas('assignedUsers', function($q2) use ($user) {
                          $q2->where('users.id', $user->id);
                      });
                });
            }
            
            if ($this->search) {
                $q->where(function($sq) {
                    $sq->where('numero', 'like', '%' . $this->search . '%')
                       ->orWhere('titulo', 'like', '%' . $this->search . '%');
                });
            }
            return $q;
        };

        if ($this->viewMode === 'kanban') {
            $estados = EstadoProcesal::orderBy('orden', 'asc')->orderBy('id', 'asc')->get();
            
            // Populate groups
            // Optimization: Fetch all matching expedientes and grouping in PHP to avoid N+1 queries
            $allExpedientes = $queryBuilder()
                ->with(['cliente', 'abogado'])
                ->get();
            
            $kanbanData = [];
            foreach ($estados as $estado) {
                $kanbanData[] = [
                    'estado' => $estado,
                    'expedientes' => $allExpedientes->where('estado_procesal_id', $estado->id)
                ];
            }
            
            // Handle expedientes with no status or invalid status ID
            $orphans = $allExpedientes->whereNull('estado_procesal_id');
            if ($orphans->count() > 0) {
                 $kanbanData[] = [
                    'estado' => (object)['id' => null, 'nombre' => 'Sin Clasificar', 'color' => 'gray'],
                    'expedientes' => $orphans
                ];
            }

            return view('livewire.expedientes.index', [
                'kanbanData' => $kanbanData
            ]);

        } else {
            // LIST MODE
            $expedientes = $queryBuilder()
                ->with(['cliente', 'abogado', 'estadoProcesal'])
                ->withCount(['actuaciones', 'documentos', 'eventos', 'comentarios'])
                ->orderByDesc('id')
                ->paginate(10);

            return view('livewire.expedientes.index', [
                'expedientes' => $expedientes
            ]);
        }
    }
}
