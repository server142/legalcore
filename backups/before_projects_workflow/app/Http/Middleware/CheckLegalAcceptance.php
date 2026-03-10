<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\LegalDocument;
use App\Models\LegalAcceptance;

class CheckLegalAcceptance
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return $next($request);
        }

        // Exclude specific routes to avoid loops or blocking essential actions
        $excludedRoutes = [
            'legal.acceptance',
            'logout',
            'legal.view',
            'privacy',
            'terms',
            'billing.*', // Permitir que paguen si es necesario
        ];

        foreach ($excludedRoutes as $route) {
            if ($request->routeIs($route)) {
                return $next($request);
            }
        }

        // Permitir solicitudes internas de Livewire para que los componentes funcionen
        if ($request->is('livewire/*')) {
            return $next($request);
        }

        // Find documents that the user MUST accept (Global + Tenant specific)
        $requiredDocs = LegalDocument::where('activo', true)
            ->where('requiere_aceptacion', true)
            ->forTenant(auth()->user()->tenant_id)
            ->get();

        foreach ($requiredDocs as $doc) {
            // Regla 1: Plantillas de contratos de servicios JAMÁS se aceptan en el sistema (son para imprimir)
            if ($doc->tipo === 'CONTRATO_SERVICIOS') {
                continue;
            }

            // Regla 2: El Contrato SaaS (pagos) solo lo debe aceptar el Admin/SuperAdmin, no los empleados
            if ($doc->tipo === 'CONTRATO_SAAS' && !auth()->user()->hasRole(['super_admin', 'admin'])) {
                continue;
            }

            $accepted = LegalAcceptance::where('user_id', auth()->id())
                ->where('legal_document_id', $doc->id)
                ->where('version', $doc->version)
                ->exists();

            if (!$accepted) {
                return redirect()->route('legal.acceptance');
            }
        }

        return $next($request);
    }
}
