<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait Auditable
{
    /**
     * Log an action to the AuditLog table.
     *
     * @param string $accion Short action name (e.g., 'crear', 'editar', 'eliminar', 'login')
     * @param string $modulo The module where the action happened (e.g., 'Usuarios', 'Expedientes', 'Auth')
     * @param string $descripcion Human readable description of what happened.
     * @param array|null $metadatos Additional data to store as JSON.
     * @return void
     */
    public function logAudit($accion, $modulo, $descripcion, $metadatos = [])
    {
        try {
            $user = Auth::user();
            
            // If strictly checking tenant_id from user, make sure user exists. 
            // For failed logins, user might be null, so handle that case carefully if reused outside authenticated context.
            // Assuming this trait is mostly used in authenticated Livewire components.
            
            // However, for Auth events or unauthenticated contexts, we might need flexibility.
            $tenantId = $user ? $user->tenant_id : null;
            $userId = $user ? $user->id : null;

            // If running in a context where we can get tenant from metadata or other source if user is null?
            // For now, let's strictly log what we have.

            AuditLog::create([
                'tenant_id'   => $tenantId, // Nullable in DB? Hopefully. If not, this might fail for system events.
                'user_id'     => $userId,
                'accion'      => $accion,
                'modulo'      => $modulo,
                'descripcion' => $descripcion,
                'metadatos'   => $metadatos,
                'ip_address'  => Request::ip(),
            ]);

        } catch (\Exception $e) {
            // Silently fail to avoid breaking the main app flow if logging fails?
            // Or log to system log.
            \Illuminate\Support\Facades\Log::error('Failed to write to AuditLog: ' . $e->getMessage());
        }
    }
}
