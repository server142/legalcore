<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class GlobalSettings extends Component
{
    // Stripe Settings
    public $stripe_key;
    public $stripe_secret;
    public $stripe_webhook_secret;

    // SMS Settings (Twilio example)
    public $sms_provider = 'twilio';
    public $sms_sid;
    public $sms_token;
    public $sms_from;

    // Mail Settings
    public $mail_mailer = 'smtp';
    public $mail_host;
    public $mail_port;
    public $mail_username;
    public $mail_password;
    public $mail_encryption;
    public $mail_from_address;
    public $mail_from_name;

    public function mount()
    {
        $this->loadSettings();
    }

    public function loadSettings()
    {
        $settings = DB::table('global_settings')->pluck('value', 'key')->toArray();

        $this->stripe_key = $settings['stripe_key'] ?? '';
        $this->stripe_secret = $settings['stripe_secret'] ?? '';
        $this->stripe_webhook_secret = $settings['stripe_webhook_secret'] ?? '';

        $this->sms_sid = $settings['sms_sid'] ?? '';
        $this->sms_token = $settings['sms_token'] ?? '';
        $this->sms_from = $settings['sms_from'] ?? '';

        $this->mail_mailer = $settings['mail_mailer'] ?? 'smtp';
        $this->mail_host = $settings['mail_host'] ?? '';
        $this->mail_port = $settings['mail_port'] ?? '587';
        $this->mail_username = $settings['mail_username'] ?? '';
        $this->mail_password = $settings['mail_password'] ?? '';
        $this->mail_encryption = $settings['mail_encryption'] ?? 'tls';
        $this->mail_from_address = $settings['mail_from_address'] ?? '';
        $this->mail_from_name = $settings['mail_from_name'] ?? '';
    }

    public function save()
    {
        $data = [
            'stripe_key' => $this->stripe_key,
            'stripe_secret' => $this->stripe_secret,
            'stripe_webhook_secret' => $this->stripe_webhook_secret,
            'sms_sid' => $this->sms_sid,
            'sms_token' => $this->sms_token,
            'sms_from' => $this->sms_from,
            'mail_mailer' => $this->mail_mailer,
            'mail_host' => $this->mail_host,
            'mail_port' => $this->mail_port,
            'mail_username' => $this->mail_username,
            'mail_password' => $this->mail_password,
            'mail_encryption' => $this->mail_encryption,
            'mail_from_address' => $this->mail_from_address,
            'mail_from_name' => $this->mail_from_name,
        ];

        foreach ($data as $key => $value) {
            DB::table('global_settings')->updateOrInsert(
                ['key' => $key],
                ['value' => $value, 'updated_at' => now()]
            );
        }

        session()->flash('message', 'Configuraciones globales actualizadas correctamente.');
    }

    public function render()
    {
        return view('livewire.admin.global-settings')->layout('layouts.app');
    }
}
