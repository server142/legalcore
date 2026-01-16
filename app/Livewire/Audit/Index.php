<?php

namespace App\Livewire\Audit;

use Livewire\Component;
use App\Models\AuditLog;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';

    public function render()
    {
        // Solo administradores pueden ver esto
        if (!auth()->user()->hasRole('super_admin') && !auth()->user()->hasRole('admin')) {
            abort(403);
        }

        $logs = AuditLog::with('user')
            ->where(function($query) {
                $query->where('descripcion', 'like', '%' . $this->search . '%')
                      ->orWhere('accion', 'like', '%' . $this->search . '%')
                      ->orWhere('modulo', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(20);

        return view('livewire.audit.index', [
            'logs' => $logs
        ]);
    }
}
