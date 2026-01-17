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

    public function mount($plan)
    {
        $this->planSlug = $plan;
        $this->plan = Plan::where('slug', $plan)->firstOrFail();

        // Verificar si ya tiene suscripción activa a este plan
        $tenant = Auth::user()->tenant;
        if ($tenant->subscription_status === 'active' && $tenant->plan === $plan) {
            return redirect()->route('dashboard');
        }

        // Aquí iniciaríamos el SetupIntent de Stripe si tuviéramos las llaves
        // $this->clientSecret = $tenant->createSetupIntent()->client_secret;
    }

    public function processPayment()
    {
        // SIMULACIÓN DE PAGO EXITOSO (Para MVP sin llaves de Stripe reales configuradas)
        // En producción, esto se maneja via Stripe.js y luego webhook o confirmación server-side
        
        $tenant = Auth::user()->tenant;
        
        // Actualizar Tenant
        $tenant->update([
            'plan' => $this->plan->slug,
            'plan_id' => $this->plan->id,
            'subscription_status' => 'active',
            'subscription_ends_at' => now()->addDays($this->plan->duration_in_days),
            'trial_ends_at' => null, // Fin del trial si existía
            'is_active' => true,
        ]);

        session()->flash('message', '¡Pago procesado con éxito! Bienvenido a LegalCore.');
        return redirect()->route('dashboard');
    }

    public function render()
    {
        return view('livewire.billing.subscribe')->layout('layouts.guest');
    }
}
