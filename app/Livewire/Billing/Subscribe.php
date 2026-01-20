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

    public function mount($plan)
    {
        $this->planSlug = $plan;
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
                session()->flash('error', 'La pasarela de pagos no está configurada correctamente. Por favor, contacte al administrador.');
                return redirect()->route('dashboard');
            }

            try {
                $this->clientSecret = $tenant->createSetupIntent()->client_secret;
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Stripe Error: ' . $e->getMessage());
                session()->flash('error', 'Error al conectar con Stripe. Verifique la configuración.');
                return redirect()->route('dashboard');
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
        return view('livewire.billing.subscribe')->layout('layouts.guest');
    }
}
