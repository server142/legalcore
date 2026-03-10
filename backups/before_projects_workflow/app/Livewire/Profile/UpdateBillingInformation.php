<?php

namespace App\Livewire\Profile;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Models\TenantBillingDetail;

class UpdateBillingInformation extends Component
{
    public $razon_social;
    public $rfc;
    public $regimen_fiscal;
    public $codigo_postal;
    public $direccion_fiscal;
    public $uso_cfdi = 'G03';
    public $email_facturacion;

    /**
     * Mount the component.
     */
    public function mount()
    {
        $tenant = Auth::user()->tenant;
        
        if ($tenant && $tenant->billingDetails) {
            $details = $tenant->billingDetails;
            $this->razon_social = $details->razon_social;
            $this->rfc = $details->rfc;
            $this->regimen_fiscal = $details->regimen_fiscal;
            $this->codigo_postal = $details->codigo_postal;
            $this->direccion_fiscal = $details->direccion_fiscal;
            $this->uso_cfdi = $details->uso_cfdi;
            $this->email_facturacion = $details->email_facturacion;
        } else {
            // Pre-fill email with current user's email if empty
            $this->email_facturacion = Auth::user()->email;
            $this->razon_social = $tenant->name ?? '';
        }
    }

    /**
     * Update the billing information.
     */
    public function updateBillingInformation()
    {
        $this->resetErrorBag();

        $this->validate([
            'razon_social' => ['required', 'string', 'max:255'],
            'rfc' => ['required', 'string', 'size:13', 'regex:/^[A-ZÑ&]{3,4}\d{6}(?:[A-Z\d]{3})?$/i'], // RFC Regex básico
            'regimen_fiscal' => ['required', 'string', 'max:50'],
            'codigo_postal' => ['required', 'digits:5'],
            'direccion_fiscal' => ['nullable', 'string', 'max:500'],
            'uso_cfdi' => ['required', 'string', 'max:5'],
            'email_facturacion' => ['required', 'email'],
        ], [
            'rfc.regex' => 'El formato del RFC no es válido (Debe ser de 12 o 13 caracteres alfanuméricos).',
            'codigo_postal.digits' => 'El Código Postal debe tener 5 dígitos.',
        ]);

        $tenant = Auth::user()->tenant;

        if (!$tenant) {
            return;
        }

        // Create or Update
        TenantBillingDetail::updateOrCreate(
            ['tenant_id' => $tenant->id],
            [
                'razon_social' => $this->razon_social,
                'rfc' => strtoupper($this->rfc),
                'regimen_fiscal' => $this->regimen_fiscal,
                'codigo_postal' => $this->codigo_postal,
                'direccion_fiscal' => $this->direccion_fiscal,
                'uso_cfdi' => $this->uso_cfdi,
                'email_facturacion' => $this->email_facturacion,
                'verified' => false, // Reset verification on update optionally
            ]
        );

        session()->flash('success', 'Información de facturación actualizada correctamente.');
        $this->dispatch('saved'); // For visual feedback if using Jetstream action-message
    }

    public function render()
    {
        return view('livewire.profile.update-billing-information');
    }
}
