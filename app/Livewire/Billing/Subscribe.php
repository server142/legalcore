<?php

namespace App\Livewire\Billing;

use Livewire\Component;
use App\Models\Plan;
use Illuminate\Support\Facades\Auth;

class Subscribe extends Component
{
    public $planSlug;
    public $plan;
    public $clientSecret;
    public $isFree = false;

    // New properties
    public $showPricing = false;
    public $availablePlans = [];

    public function mount($plan)
    {
        $this->planSlug = $plan;

        // Si el plan solicitado es 'trial', mostramos la tabla de precios para que elija uno real
        if ($plan === 'trial') {
            $this->showPricing = true;
            $this->availablePlans = Plan::where('is_active', true)
                ->where('slug', '!=', 'trial')
                ->where('slug', '!=', 'exento')
                ->orderBy('price', 'asc')
                ->get();
            return;
        }

        $this->plan = Plan::where('slug', $plan)->firstOrFail();
        $this->isFree = $this->plan->price <= 0 || $this->plan->slug === 'trial';

        if (!$this->isFree && !$this->plan->stripe_price_id) {
            session()->flash('error', 'Este plan no tiene un ID de precio de Stripe configurado.');
            return redirect()->route('dashboard');
        }

        // Verificar si ya tiene suscripción activa a este plan
        $tenant = Auth::user()->tenant;
        if ($tenant->subscription_status === 'active' && $tenant->plan === $plan) {
            return redirect()->route('dashboard');
        }

        // Iniciar el SetupIntent de Stripe para capturar el método de pago de forma segura
        if (!$this->isFree) {
            if (!config('cashier.secret')) {
                // Modo desarrollo/sin configurar: permitimos proceder sin stripe (simulado) si es necesario o mostramos error
                 // Para este caso, asumiremos que puede haber error si no hay keys, pero no bloqueamos el render para diseño
            }

            try {
                $this->clientSecret = $tenant->createSetupIntent()->client_secret;
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Stripe Error: ' . $e->getMessage());
                // No redirigimos para permitir ver el diseño, pero mostramos error
                session()->flash('error', 'Error al conectar con Stripe. Verifique la configuración.');
            }
        }
    }

    public function processPayment($paymentMethod = null)
    {
        $tenant = Auth::user()->tenant;

        try {
            if (!$this->isFree) {
                if (!$paymentMethod) {
                    throw new \Exception("No se proporcionó un método de pago válido.");
                }

                // Crear la suscripción en Stripe
                $tenant->newSubscription('default', $this->plan->stripe_price_id)
                    ->create($paymentMethod);
            }
            
            // Actualizar datos locales del Tenant
            $tenant->update([
                'plan' => $this->plan->slug,
                'plan_id' => $this->plan->id,
                'subscription_status' => 'active',
                'subscription_ends_at' => now()->addDays($this->plan->duration_in_days),
                'trial_ends_at' => null,
                'is_active' => true,
            ]);

            session()->flash('message', '¡Suscripción activada con éxito! Bienvenido a Diogenes.');
            return redirect()->route('dashboard');

        } catch (\Exception $e) {
            $this->addError('payment', 'Error al procesar el pago: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.billing.subscribe')
            ->layout('layouts.guest', ['maxWidth' => 'sm:max-w-7xl']);
    }
}
