<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckTrialStatus
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        
        if (!$user || !$user->tenant) {
            return $next($request);
        }

        $tenant = $user->tenant;

        // Si el trial expiró y no tiene suscripción activa
        if ($tenant->trialExpired() && !$tenant->subscription_ends_at) {
            // Redirigir a página de upgrade
            if (!$request->routeIs('upgrade.*')) {
                return redirect()->route('upgrade.index')
                    ->with('warning', 'Tu período de prueba ha expirado. Por favor, selecciona un plan para continuar.');
            }
        }

        // Si está en trial, mostrar días restantes
        if ($tenant->isOnTrial()) {
            $daysLeft = $tenant->daysLeftInTrial();
            if ($daysLeft <= 7 && $daysLeft > 0) {
                session()->flash('trial_warning', "Te quedan {$daysLeft} días de prueba gratuita.");
            }
        }

        return $next($request);
    }
}
