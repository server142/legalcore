<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckExpedienteLimit
{
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = auth()->user()?->tenant;
        
        if (!$tenant) {
            return $next($request);
        }

        // Get active plan
        $subscription = $tenant->subscriptions()->active()->first();
        
        if (!$subscription || !$subscription->plan) {
            return $next($request);
        }

        $plan = $subscription->plan;
        
        // 0 = unlimited
        if ($plan->max_expedientes == 0) {
            return $next($request);
        }

        // Count current expedientes
        $currentCount = \App\Models\Expediente::where('tenant_id', $tenant->id)->count();

        if ($currentCount >= $plan->max_expedientes) {
            if ($request->wantsJson() || $request->is('livewire/*')) {
                session()->flash('error', "Has alcanzado el límite de {$plan->max_expedientes} expedientes de tu plan. Actualiza tu suscripción para crear más.");
                return redirect()->back();
            }
            
            return redirect()->back()->with('error', "Has alcanzado el límite de {$plan->max_expedientes} expedientes de tu plan. Actualiza tu suscripción para crear más.");
        }

        return $next($request);
    }
}
