<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // 1. Si no hay usuario o es Super Admin, permitir acceso
        if (!$user || $user->hasRole('super_admin')) {
            return $next($request);
        }

        $tenant = $user->tenant;

        // 2. Si no hay tenant asociado, algo está mal (o es un usuario global sin tenant)
        if (!$tenant) {
            return $next($request);
        }

        // Rutas exentas (para poder pagar o ver estado)
        if ($request->routeIs('subscription.*') || $request->routeIs('billing.*') || $request->routeIs('profile.*') || $request->routeIs('logout') || $request->is('logout')) {
            return $next($request);
        }

        $now = now();

        // 0. Verificar Pago Pendiente (Nuevo registro con plan de pago)
        if ($tenant->subscription_status === 'pending_payment') {
            return redirect()->route('billing.subscribe', ['plan' => $tenant->plan]);
        }

        // 3. Verificar Estado de Prueba (Trial)
        if ($tenant->plan === 'trial') {
            if ($tenant->isOnTrial()) {
                return $next($request);
            } else {
                // Trial vencido -> Mover a Grace Period si no estaba ya
                if ($tenant->subscription_status !== 'grace_period' && $tenant->subscription_status !== 'cancelled') {
                    $this->handleExpiredTrial($tenant);
                }
            }
        }

        // 4. Verificar Suscripción Activa
        if ($tenant->plan !== 'trial' && $tenant->isSubscriptionActive()) {
            return $next($request);
        }

        // 5. Verificar Periodo de Gracia (Grace Period)
        if ($tenant->isOnGracePeriod()) {
            // En periodo de gracia: Solo lectura (GET)
            if ($request->isMethod('POST') || $request->isMethod('PUT') || $request->isMethod('DELETE') || $request->isMethod('PATCH')) {
                // Bloquear acciones de escritura
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Tu suscripción ha vencido. Estás en periodo de gracia (solo lectura).'], 403);
                }
                return redirect()->route('subscription.expired')->with('error', 'Modo solo lectura: Tu suscripción ha vencido.');
            }
            // Permitir GET (ver datos, exportar)
            if (!session()->has('warning')) {
                session()->flash('warning', 'Tu suscripción ha vencido. Tienes acceso limitado para exportar tus datos.');
            }
            return $next($request);
        }

        // 6. Expirado / Cancelado / Sin fechas válidas
        return redirect()->route('subscription.expired');
    }

    protected function handleExpiredTrial($tenant)
    {
        // Obtener configuración de días de gracia (default 3)
        $graceDays = \DB::table('global_settings')->where('key', 'grace_period_days')->value('value') ?? 3;
        
        $tenant->update([
            'subscription_status' => 'grace_period',
            'grace_period_ends_at' => now()->addDays((int)$graceDays)
        ]);
    }
}
