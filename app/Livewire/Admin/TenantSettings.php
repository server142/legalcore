<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class TenantSettings extends Component
{
    use WithFileUploads;

    public $name;
    public $direccion;
    public $titular;
    public $titulares_adjuntos;
    public $datos_generales;
    public $logo;
    public $logo_path;
    
    // SMS Settings
    public $sms_enabled = false;
    public $sms_days_before = 3;
    public $sms_recipients = '';

    // Plan & Billing
    public $currentPlanDetails;
    public $availablePlans;
    public $subscriptionStatus;
    public $subscriptionEndsAt;

    public function mount()
    {
        $tenant = auth()->user()->tenant;
        $this->name = $tenant->name;
        
        $settings = $tenant->settings ?? [];
        $this->direccion = $settings['direccion'] ?? '';
        $this->titular = $settings['titular'] ?? '';
        $this->titulares_adjuntos = $settings['titulares_adjuntos'] ?? '';
        $this->datos_generales = $settings['datos_generales'] ?? '';
        $this->logo_path = $settings['logo_path'] ?? '';
        
        $this->sms_enabled = $settings['sms_enabled'] ?? false;
        $this->sms_days_before = $settings['sms_days_before'] ?? 3;
        $this->sms_recipients = $settings['sms_recipients'] ?? '';

        // Plan Info
        $this->currentPlanDetails = $tenant->planRelation;
        $this->subscriptionStatus = $tenant->subscription_status;
        $this->subscriptionEndsAt = $tenant->subscription_ends_at;

        // Available Upgrades (Only higher price)
        $currentPrice = $this->currentPlanDetails ? $this->currentPlanDetails->price : 0;
        $this->availablePlans = \App\Models\Plan::where('price', '>', $currentPrice)
            ->where('is_active', true)
            ->orderBy('price', 'asc')
            ->get();
    }

    public function save()
    {
        $tenant = auth()->user()->tenant;
        
        $this->validate([
            'name' => 'required|string|max:255',
            'direccion' => 'nullable|string',
            'titular' => 'nullable|string',
            'titulares_adjuntos' => 'nullable|string',
            'datos_generales' => 'nullable|string',
            'logo' => 'nullable|image|max:5120', // 5MB Max
            'sms_enabled' => 'boolean',
            'sms_days_before' => 'required|integer|min:1|max:30',
            'sms_recipients' => 'nullable|string',
        ], [
            'logo.max' => 'La imagen es demasiado pesada. El límite es de 5MB.',
            'logo.image' => 'El archivo debe ser una imagen (jpg, png, etc).',
        ]);

        if ($this->logo) {
            // Delete old logo if exists
            if ($this->logo_path) {
                Storage::disk('public')->delete($this->logo_path);
            }
            $this->logo_path = $this->logo->store('logos', 'public');
        }

        $tenant->update([
            'name' => $this->name,
            'settings' => [
                'direccion' => $this->direccion,
                'titular' => $this->titular,
                'titulares_adjuntos' => $this->titulares_adjuntos,
                'datos_generales' => $this->datos_generales,
                'logo_path' => $this->logo_path,
                'sms_enabled' => $this->sms_enabled,
                'sms_days_before' => $this->sms_days_before,
                'sms_recipients' => $this->sms_recipients,
            ]
        ]);

        $this->dispatch('notify', 'Configuración actualizada exitosamente');
    }

    public function render()
    {
        return view('livewire.admin.tenant-settings')->layout('layouts.app');
    }
}
