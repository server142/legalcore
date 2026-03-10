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
    public function logAudit($accion, $modulo, $descripcion, $metadatos = [], $severity = 'low')
    {
        try {
            $user = Auth::user();
            $request = request();
            
            $tenantId = $user ? $user->tenant_id : (session('tenant_id') ?? null);
            $userId = $user ? $user->id : null;

            // Detección de navegador y sistema con fallback
            $browser = 'Desconocido';
            $os = 'Desconocido';
            $device = 'Desktop';

            if (class_exists('\Jenssegers\Agent\Agent')) {
                $agent = new \Jenssegers\Agent\Agent();
                $browser = $agent->browser() ?: 'Desconocido';
                $os = $agent->platform() ?: 'Desconocido';
                $device = $agent->device() ?: 'Desktop';
            }

            // Detección automática de severidad
            if ($severity === 'low') {
                $criticalActions = ['delete', 'destroy', 'login_fallido', 'unauthorized', 'eliminar'];
                if (in_array(strtolower($accion), $criticalActions)) $severity = 'critical';
            }

            AuditLog::create([
                'tenant_id'   => $tenantId,
                'user_id'     => $userId,
                'accion'      => $accion,
                'modulo'      => $modulo,
                'descripcion' => $descripcion,
                'metadatos'   => $metadatos,
                'ip_address'  => $request->ip(),
                'user_agent'  => $request->userAgent(),
                'severity'    => $severity,
                'browser'     => $browser,
                'os'          => $os,
                'device'      => $device,
                'session_id'  => session()->getId(),
            ]);

        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Security Audit Failure: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
        }
    }
}
