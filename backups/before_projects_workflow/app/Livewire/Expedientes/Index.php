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
    public $showTrash = false; // Toggle for Trash View

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

    public function updateOrder($statusId, $orderedIds)
    {
        // Security check
        if (!auth()->user()->hasRole(['super_admin', 'admin']) && !auth()->user()->can('manage expedientes')) {
             return;
        }

        // Handle "Sin Clasificar"
        if ($statusId === 'null' || $statusId === '') $statusId = null;

        foreach ($orderedIds as $index => $id) {
            if ($id) {
                Expediente::where('id', $id)->update([
                    'orden' => $index,
                    'estado_procesal_id' => $statusId
                ]);
            }
        }
    }

    public function delete($id)
    {
        $expediente = Expediente::findOrFail($id);
        
        // Permission Check
        if (!auth()->user()->hasRole('super_admin') && !auth()->user()->hasRole('admin') && !auth()->user()->can('manage expedientes')) {
             $this->dispatch('notify', ['type' => 'error', 'message' => 'No tienes permiso para eliminar expedientes.']);
             return;
        }

        // Safety Check 1: Paid Invoices
        if ($expediente->facturas()->where('estado', 'pagada')->exists()) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'No se puede eliminar: El expediente tiene facturas pagadas asociadas.']);
            return;
        }

        // Perform Soft Delete
        $expediente->delete();
        
        $this->dispatch('notify', 'Expediente eliminado (enviado a papelera) exitosamente.');
    }

    public function cerrar($id)
    {
        $expediente = Expediente::findOrFail($id);
        $expediente->update(['fecha_cierre' => now()]);
        $this->dispatch('notify', 'Expediente marcado como cerrado.');
    }

    public function toggleTrash()
    {
        $this->showTrash = !$this->showTrash;
        $this->resetPage();
    }

    public function restore($id)
    {
        if (!auth()->user()->can('manage expedientes') && !auth()->user()->hasRole(['super_admin', 'admin'])) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'No tienes permiso para restaurar expedientes.']);
            return;
        }

        $expediente = Expediente::onlyTrashed()->findOrFail($id);
        $expediente->restore();

        $this->dispatch('notify', 'Expediente restaurado exitosamente.');
    }

    public function forceDelete($id)
    {
        if (!auth()->user()->hasRole('super_admin')) {
             $this->dispatch('notify', ['type' => 'error', 'message' => 'Solo el Super Admin puede eliminar permanentemente.']);
             return;
        }

        $expediente = Expediente::onlyTrashed()->findOrFail($id);
        
        // Final Safety Check
        if ($expediente->facturas()->exists()) {
             $this->dispatch('notify', ['type' => 'error', 'message' => 'No se puede eliminar permanentemente si tiene facturas (aunque no esten pagadas, por integridad contable).']);
             return;
        }

        $expediente->forceDelete();
        $this->dispatch('notify', 'Expediente eliminado PERMANENTEMENTE.');
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

        if ($this->viewMode === 'kanban' && !$this->showTrash) {
            $estados = EstadoProcesal::orderBy('orden', 'asc')->orderBy('id', 'asc')->get();
            
            // Populate groups
            // Optimization: Fetch all matching expedientes and grouping in PHP to avoid N+1 queries
            $allExpedientes = $queryBuilder()
                ->with(['cliente', 'abogado'])
                ->when($this->showTrash, function($q) { $q->onlyTrashed(); })
                ->orderBy('orden', 'asc') // Respect sorting
                ->get();
            
            $kanbanData = [];
            foreach ($estados as $estado) {
                $kanbanData[] = [
                    'estado' => $estado,
                    'expedientes' => $allExpedientes->where('estado_procesal_id', $estado->id)
                ]; // The query already sorted by 'orden', so the collection preserves it
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
                ->when($this->showTrash, function($q) { $q->onlyTrashed(); })
                ->orderByDesc('id')
                ->paginate(10);

            return view('livewire.expedientes.index', [
                'expedientes' => $expedientes
            ]);
        }
    }
}
