<?php

namespace App\Livewire\Billing;

use Livewire\Component;
use App\Models\Plan;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Url;

class Subscribe extends Component
{
    public $planSlug;
    public $plan;
    public $clientSecret;
    public $isFree     = false;
    public $showPricing = false;
    public $availablePlans = [];
    
    #[Url]
    public $context    = 'despacho'; // 'despacho' | 'directory'

    public function mount($plan)
    {
        $this->planSlug = $plan;
        $this->context  = request()->query('context', 'despacho');

        // Mostrar selector de planes
        if ($plan === 'trial') {
            $this->showPricing = true;

            $query = Plan::where('is_active', true)
                ->where('slug', '!=', 'trial')
                ->where('slug', '!=', 'exento');

            // Filtrar por contexto
            if ($this->context === 'directory') {
                // Solo planes del directorio
                $query->where('slug', 'like', 'directory-%');
            } else {
                // Solo planes del despacho (excluye plans directory-*)
                $query->where('slug', 'not like', 'directory-%');
            }

            $this->availablePlans = $query->orderBy('price', 'asc')->get();
            return;
        }

        $this->plan  = Plan::where('slug', $plan)->firstOrFail();
        $this->isFree = $this->plan->price <= 0 || $this->plan->slug === 'trial';

        if (!$this->isFree && !$this->plan->stripe_price_id) {
            session()->flash('error', 'Este plan no tiene un ID de precio de Stripe configurado.');
            return redirect()->route('dashboard');
        }

        $tenant = Auth::user()->tenant;
        if ($tenant->subscription_status === 'active' && $tenant->plan === $plan) {
            return redirect()->route('dashboard');
        }

        if (!$this->isFree) {
            try {
                $this->clientSecret = $tenant->createSetupIntent()->client_secret;
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Stripe Error: ' . $e->getMessage());
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
                $tenant->newSubscription('default', $this->plan->stripe_price_id)
                    ->create($paymentMethod);
            }

            $tenant->update([
                'plan'                => $this->plan->slug,
                'plan_id'             => $this->plan->id,
                'subscription_status' => 'active',
                'subscription_ends_at'=> now()->addDays($this->plan->duration_in_days),
                'trial_ends_at'       => null,
                'is_active'           => true,
            ]);

            session()->flash('message', '¡Suscripción activada con éxito! Bienvenido a Diogenes.');

            // Redirigir según contexto
            return $this->context === 'directory'
                ? redirect()->route('directory.dashboard')
                : redirect()->route('dashboard');

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
