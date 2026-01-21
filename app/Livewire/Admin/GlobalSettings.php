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

    // File Upload Settings
    public $max_file_size_mb = 100;

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

        $this->max_file_size_mb = $settings['max_file_size_mb'] ?? 100;
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
            'max_file_size_mb' => $this->max_file_size_mb,
        ];

        foreach ($data as $key => $value) {
            DB::table('global_settings')->updateOrInsert(
                ['key' => $key],
                ['value' => $value, 'updated_at' => now()]
            );
        }

        session()->flash('message', 'Configuraciones globales actualizadas correctamente.');
    }

    public function testStripe()
    {
        if (empty($this->stripe_secret)) {
            session()->flash('error', 'Debe configurar la Secret Key de Stripe primero.');
            return;
        }

        try {
            $stripe = new \Stripe\StripeClient($this->stripe_secret);
            $stripe->customers->all(['limit' => 1]);
            session()->flash('message', 'Conexión con Stripe exitosa.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error de Stripe: ' . $e->getMessage());
        }
    }

    public function testSMS()
    {
        if (empty($this->sms_sid) || empty($this->sms_token) || empty($this->sms_from)) {
            session()->flash('error', 'Debe configurar SID, Token y Número de origen primero.');
            return;
        }

        try {
            $twilio = new \Twilio\Rest\Client($this->sms_sid, $this->sms_token);
            // Solo probamos la autenticación listando mensajes (sin enviar para no gastar saldo)
            $twilio->messages->read([], 1);
            session()->flash('message', 'Conexión con Twilio exitosa.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error de SMS: ' . $e->getMessage());
        }
    }

    public function testMail($testEmail)
    {
        if (empty($testEmail)) {
            session()->flash('error', 'Debe proporcionar un correo electrónico para la prueba.');
            return;
        }

        if (empty($this->mail_host) || empty($this->mail_username) || empty($this->mail_password)) {
            session()->flash('error', 'Debe configurar los datos del servidor SMTP primero.');
            return;
        }

        try {
            // Forzar la limpieza del mailer para que tome la nueva configuración
            \Illuminate\Support\Facades\Mail::purge('smtp');

            // Configurar temporalmente el mailer para la prueba
            config([
                'mail.default' => 'smtp',
                'mail.mailers.smtp.transport' => 'smtp',
                'mail.mailers.smtp.host' => $this->mail_host,
                'mail.mailers.smtp.port' => $this->mail_port,
                'mail.mailers.smtp.encryption' => $this->mail_encryption === 'none' ? null : $this->mail_encryption,
                'mail.mailers.smtp.username' => $this->mail_username,
                'mail.mailers.smtp.password' => $this->mail_password,
                'mail.mailers.smtp.timeout' => 5, // Timeout corto para la prueba
                'mail.from.address' => $this->mail_from_address,
                'mail.from.name' => $this->mail_from_name,
            ]);

            $fromAddress = $this->mail_from_address;
            $fromName = $this->mail_from_name;

            \Illuminate\Support\Facades\Mail::mailer('smtp')->raw('Esta es una prueba de configuración de correo desde Diogenes. Si recibes esto, tu configuración SMTP es correcta.', function ($message) use ($testEmail, $fromAddress, $fromName) {
                $message->to($testEmail)
                    ->from($fromAddress, $fromName)
                    ->subject('Prueba de Configuración de Correo - Diogenes');
            });

            session()->flash('message', '¡Éxito! El servidor SMTP aceptó el correo y lo envió a ' . $testEmail);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Mail Test Error: ' . $e->getMessage());
            session()->flash('error', 'Fallo en el envío: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.global-settings')->layout('layouts.app');
    }
}
