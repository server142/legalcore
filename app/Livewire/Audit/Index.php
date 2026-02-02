<?php

namespace App\Livewire\Audit;

use Livewire\Component;
use App\Models\AuditLog;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $filterTenant = '';
    public $filterModule = '';
    public $filterAction = '';
    public $filterSeverity = '';

    public function render()
    {
        $user = auth()->user();
        
        // Solo administradores pueden ver esto
        if (!$user->hasRole('super_admin') && !$user->hasRole('admin')) {
            abort(403);
        }

        $isSuperAdmin = $user->hasRole('super_admin');

        $query = AuditLog::with(['user', 'tenant']);

        // Si es Super Admin, saltamos el scope global de tenant para auditoría completa
        if ($isSuperAdmin) {
            $query->withoutGlobalScopes();
        }

        $query->where(function($q) {
            $q->where('descripcion', 'like', '%' . $this->search . '%')
              ->orWhere('accion', 'like', '%' . $this->search . '%')
              ->orWhere('modulo', 'like', '%' . $this->search . '%');
        });

        if ($this->filterTenant && $isSuperAdmin) {
            $query->where('tenant_id', $this->filterTenant);
        }

        if ($this->filterModule) {
            $query->where('modulo', $this->filterModule);
        }

        if ($this->filterAction) {
            $query->where('accion', $this->filterAction);
        }

        if ($this->filterSeverity) {
            $query->where('severity', $this->filterSeverity);
        }

        $logs = $query->latest()->paginate(20);

        // Obtener datos para los combos de filtrado
        $tenants = $isSuperAdmin ? \App\Models\Tenant::all() : collect();
        
        // Obtener módulos únicos que existen en los logs
        $modules = AuditLog::query()
            ->when($isSuperAdmin, fn($q) => $q->withoutGlobalScopes())
            ->distinct()
            ->pluck('modulo');

        return view('livewire.audit.index', [
            'logs' => $logs,
            'tenants' => $tenants,
            'modules' => $modules,
            'isSuperAdmin' => $isSuperAdmin
        ]);
    }
}
