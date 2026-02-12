<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class TenantSettings extends Component
{
    use WithFileUploads;

    public $name;
    public $direccion;
    public $titular;
    public $rfc; // Added
    public $ciudad; // Added
    public $email_contacto; // Added
    public $titulares_adjuntos;
    public $datos_generales;
    public $logo;
    public $logo_path;

    // Datos para tarjeta pública de cita
    public $asesorias_contact_phone;
    public $asesorias_location_lat;
    public $asesorias_location_lon;

    // Datos de pago (mostrar en tarjeta pública y cobro)
    public $payment_transfer_bank;
    public $payment_transfer_holder;
    public $payment_transfer_clabe;
    public $payment_transfer_account;
    public $payment_card_bank;
    public $payment_card_holder;
    public $payment_card_number;
    
    // SMS Settings
    public $sms_enabled = false;
    public $sms_days_before = 3;
    public $sms_recipients = '';

    // Asesorías & Agenda Settings
    public $asesorias_working_hours_start = '09:00';
    public $asesorias_working_hours_end = '18:00';
    public $asesorias_business_days = ['mon', 'tue', 'wed', 'thu', 'fri'];
    public $asesorias_slot_minutes = 15;
    public $asesorias_enforce_availability = true;
    public $asesorias_sync_to_agenda = true;
    public $asesorias_billing_enabled = false;
    public $billing_apply_iva = true;

    public $agenda_enforce_availability = false;
    
    // Agenda Reminders
    public $reminder_1_hours = 120;
    public $reminder_2_hours = 72;
    public $reminder_3_hours = 24;
    public $reminder_4_hours = 12;

    // Plan & Billing
    public $currentPlanDetails;
    public $availablePlans;
    public $subscriptionStatus;
    public $subscriptionEndsAt;

    public function mount()
    {
        $tenant = auth()->user()->tenant;
        Log::info("TenantSettings Debug: Tenant ID {$tenant->id}, Plan: {$tenant->plan}, TrialEnds: {$tenant->trial_ends_at}, SubEnds: {$tenant->subscription_ends_at}");
        $this->name = $tenant->name;
        
        $settings = $tenant->settings ?? [];
        $this->direccion = $settings['direccion'] ?? '';
        $this->titular = $settings['titular'] ?? '';
        $this->rfc = $settings['rfc'] ?? ''; // Added
        $this->ciudad = $settings['ciudad'] ?? ''; // Added
        $this->email_contacto = $settings['email_contacto'] ?? ''; // Added
        $this->titulares_adjuntos = $settings['titulares_adjuntos'] ?? '';
        $this->datos_generales = $settings['datos_generales'] ?? '';
        $this->logo_path = $settings['logo_path'] ?? '';

        $this->asesorias_contact_phone = $settings['asesorias_contact_phone'] ?? '';
        $this->asesorias_location_lat = $settings['asesorias_location_lat'] ?? '';
        $this->asesorias_location_lon = $settings['asesorias_location_lon'] ?? '';

        $this->payment_transfer_bank = $settings['payment_transfer_bank'] ?? '';
        $this->payment_transfer_holder = $settings['payment_transfer_holder'] ?? '';
        $this->payment_transfer_clabe = $settings['payment_transfer_clabe'] ?? '';
        $this->payment_transfer_account = $settings['payment_transfer_account'] ?? '';
        $this->payment_card_bank = $settings['payment_card_bank'] ?? '';
        $this->payment_card_holder = $settings['payment_card_holder'] ?? '';
        $this->payment_card_number = $settings['payment_card_number'] ?? '';
        
        $this->sms_enabled = $settings['sms_enabled'] ?? false;
        $this->sms_days_before = $settings['sms_days_before'] ?? 3;
        $this->sms_recipients = $settings['sms_recipients'] ?? '';

        $this->asesorias_working_hours_start = $settings['asesorias_working_hours_start'] ?? '09:00';
        $this->asesorias_working_hours_end = $settings['asesorias_working_hours_end'] ?? '18:00';
        $this->asesorias_business_days = $settings['asesorias_business_days'] ?? ['mon', 'tue', 'wed', 'thu', 'fri'];
        $this->asesorias_slot_minutes = $settings['asesorias_slot_minutes'] ?? 15;
        $this->asesorias_enforce_availability = $settings['asesorias_enforce_availability'] ?? true;
        $this->asesorias_sync_to_agenda = $settings['asesorias_sync_to_agenda'] ?? true;
        $this->asesorias_billing_enabled = $settings['asesorias_billing_enabled'] ?? false;
        
        // Check for new key 'billing_apply_iva' first, fallback to old 'asesorias_billing_apply_iva', default true
        if (isset($settings['billing_apply_iva'])) {
            $this->billing_apply_iva = $settings['billing_apply_iva'];
        } else {
            $this->billing_apply_iva = $settings['asesorias_billing_apply_iva'] ?? true;
        }

        $this->agenda_enforce_availability = $settings['agenda_enforce_availability'] ?? false;

        $this->reminder_1_hours = $settings['reminder_1_hours'] ?? 120;
        $this->reminder_2_hours = $settings['reminder_2_hours'] ?? 72;
        $this->reminder_3_hours = $settings['reminder_3_hours'] ?? 24;
        $this->reminder_4_hours = $settings['reminder_4_hours'] ?? 12;

        // Plan Info
        $this->currentPlanDetails = $tenant->planRelation;
        $this->subscriptionStatus = $tenant->subscription_status;
        
        // Correct date display logic
        $date = $tenant->plan === 'trial' ? $tenant->trial_ends_at : $tenant->subscription_ends_at;
        
        if ($date) {
            // Ensure we handle both Carbon objects and string dates
            $this->subscriptionEndsAt = \Carbon\Carbon::parse($date)->format('Y-m-d'); 
        } else {
            $this->subscriptionEndsAt = null;
        }

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
            'rfc' => 'nullable|string|max:13',
            'ciudad' => 'nullable|string|max:255',
            'email_contacto' => 'nullable|email|max:255',
            'titulares_adjuntos' => 'nullable|string',
            'datos_generales' => 'nullable|string',
            'logo' => 'nullable|image|max:5120', // 5MB Max
            'asesorias_contact_phone' => 'nullable|string',
            'asesorias_location_lat' => 'nullable|numeric',
            'asesorias_location_lon' => 'nullable|numeric',
            'payment_transfer_bank' => 'nullable|string',
            'payment_transfer_holder' => 'nullable|string',
            'payment_transfer_clabe' => 'nullable|string',
            'payment_transfer_account' => 'nullable|string',
            'payment_card_bank' => 'nullable|string',
            'payment_card_holder' => 'nullable|string',
            'payment_card_number' => 'nullable|string',
            'sms_enabled' => 'boolean',
            'sms_days_before' => 'required|integer|min:1|max:30',
            'sms_recipients' => 'nullable|string',
            'asesorias_working_hours_start' => 'required|date_format:H:i',
            'asesorias_working_hours_end' => 'required|date_format:H:i',
            'asesorias_business_days' => 'required|array',
            'asesorias_business_days.*' => 'in:mon,tue,wed,thu,fri,sat,sun',
            'asesorias_slot_minutes' => 'required|integer|min:5|max:60',
            'asesorias_enforce_availability' => 'boolean',
            'asesorias_sync_to_agenda' => 'boolean',
            'asesorias_billing_enabled' => 'boolean',
            'billing_apply_iva' => 'boolean',
            'agenda_enforce_availability' => 'boolean',
            'reminder_1_hours' => 'required|integer|min:1',
            'reminder_2_hours' => 'required|integer|min:1',
            'reminder_3_hours' => 'required|integer|min:1',
            'reminder_4_hours' => 'required|integer|min:1',
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

        $settings = $tenant->settings ?? [];
        $settings['direccion'] = $this->direccion;
        $settings['titular'] = $this->titular;
        $settings['rfc'] = $this->rfc;
        $settings['ciudad'] = $this->ciudad;
        $settings['email_contacto'] = $this->email_contacto;
        $settings['titulares_adjuntos'] = $this->titulares_adjuntos;
        $settings['datos_generales'] = $this->datos_generales;
        $settings['logo_path'] = $this->logo_path;

        $settings['asesorias_contact_phone'] = $this->asesorias_contact_phone;
        $settings['asesorias_location_lat'] = $this->asesorias_location_lat;
        $settings['asesorias_location_lon'] = $this->asesorias_location_lon;

        $settings['payment_transfer_bank'] = $this->payment_transfer_bank;
        $settings['payment_transfer_holder'] = $this->payment_transfer_holder;
        $settings['payment_transfer_clabe'] = $this->payment_transfer_clabe;
        $settings['payment_transfer_account'] = $this->payment_transfer_account;
        $settings['payment_card_bank'] = $this->payment_card_bank;
        $settings['payment_card_holder'] = $this->payment_card_holder;
        $settings['payment_card_number'] = $this->payment_card_number;
        $settings['sms_enabled'] = (bool) $this->sms_enabled;
        $settings['sms_days_before'] = (int) $this->sms_days_before;
        $settings['sms_recipients'] = $this->sms_recipients;

        $settings['asesorias_working_hours_start'] = $this->asesorias_working_hours_start;
        $settings['asesorias_working_hours_end'] = $this->asesorias_working_hours_end;
        $settings['asesorias_business_days'] = $this->asesorias_business_days;
        $settings['asesorias_slot_minutes'] = (int) $this->asesorias_slot_minutes;
        $settings['asesorias_enforce_availability'] = (bool) $this->asesorias_enforce_availability;
        $settings['asesorias_sync_to_agenda'] = (bool) $this->asesorias_sync_to_agenda;
        $settings['asesorias_billing_enabled'] = (bool) $this->asesorias_billing_enabled;
        $settings['billing_apply_iva'] = (bool) $this->billing_apply_iva;
        unset($settings['asesorias_billing_apply_iva']); // Cleanup old key if moving forward

        $settings['agenda_enforce_availability'] = (bool) $this->agenda_enforce_availability;
        
        $settings['reminder_1_hours'] = (int) $this->reminder_1_hours;
        $settings['reminder_2_hours'] = (int) $this->reminder_2_hours;
        $settings['reminder_3_hours'] = (int) $this->reminder_3_hours;
        $settings['reminder_4_hours'] = (int) $this->reminder_4_hours;

        // Construct the array that the command uses
        $settings['reminder_intervals'] = [
            ['label' => ($this->reminder_1_hours / 24) >= 1 ? round($this->reminder_1_hours / 24) . ' días' : $this->reminder_1_hours . ' horas', 'hours' => (int)$this->reminder_1_hours],
            ['label' => ($this->reminder_2_hours / 24) >= 1 ? round($this->reminder_2_hours / 24) . ' días' : $this->reminder_2_hours . ' horas', 'hours' => (int)$this->reminder_2_hours],
            ['label' => ($this->reminder_3_hours / 24) >= 1 ? round($this->reminder_3_hours / 24) . ' días' : $this->reminder_3_hours . ' horas', 'hours' => (int)$this->reminder_3_hours],
            ['label' => ($this->reminder_4_hours / 24) >= 1 ? round($this->reminder_4_hours / 24) . ' días' : $this->reminder_4_hours . ' horas', 'hours' => (int)$this->reminder_4_hours],
        ];

        $tenant->update([
            'name' => $this->name,
            'settings' => $settings,
        ]);

        $this->dispatch('notify', 'Configuración actualizada exitosamente');
    }

    public function render()
    {
        return view('livewire.admin.tenant-settings')->layout('layouts.app');
    }
}
