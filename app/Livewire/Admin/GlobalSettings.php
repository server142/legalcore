<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class GlobalSettings extends Component
{
    use \App\Traits\Auditable;
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
    public $mail_lawyer_invitation_subject;
    public $mail_lawyer_invitation_body;
    public $mail_user_welcome_subject;
    public $mail_user_welcome_body;

    // File Upload & Processing Settings
    public $max_file_size_mb = 100;
    public $ocr_mode = 'local'; // 'off', 'local', 'vision'

    // AI Settings
    public $ai_provider = 'openai';
    public $ai_api_key;
    public $ai_model = 'gpt-4o-mini';

    // Welcome Settings
    public $welcome_video_url;
    public $welcome_message;
    public $welcome_title;
    public $welcome_version = 1;
    public $welcome_target = 'all';

    // Infrastructure Settings
    public $infrastructure_domain_expiry;
    public $infrastructure_vps_cost;
    public $infrastructure_vps_provider;
    public $infrastructure_ai_budget;

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
        $this->mail_lawyer_invitation_subject = $settings['mail_lawyer_invitation_subject'] ?? 'Invitación al Despacho {despacho} - Diogenes';
        $this->mail_lawyer_invitation_body = $settings['mail_lawyer_invitation_body'] ?? "¡Hola {nombre}!\n\nHas sido invitado a colaborar en el despacho **{despacho}**. A partir de ahora podrás gestionar tus expedientes y agenda de forma segura en nuestra plataforma.\n\nTu acceso ha sido configurado correctamente.";
        $this->mail_user_welcome_subject = $settings['mail_user_welcome_subject'] ?? 'Bienvenido a Diogenes - Tu despacho en la nube';
        $this->mail_user_welcome_body = $settings['mail_user_welcome_body'] ?? "¡Bienvenido a bordo {nombre}!\n\nEstamos emocionados de tenerte con nosotros. Diogenes es tu nuevo aliado para la gestión legal.\n\n**Primeros pasos:**\n1. Explora el Dashboard para ver tus próximas citas.\n2. Crea tu primer Expediente en la sección correspondiente.\n3. Prueba nuestra Inteligencia Artificial para analizar documentos.";

        $this->max_file_size_mb = $settings['max_file_size_mb'] ?? 100;
        
        // OCR Logic Upgrade
        if (isset($settings['ocr_mode'])) {
            $this->ocr_mode = $settings['ocr_mode'];
        } elseif (isset($settings['ocr_enabled'])) {
            $this->ocr_mode = ((bool)$settings['ocr_enabled']) ? 'local' : 'off';
        } else {
            $this->ocr_mode = 'local';
        }

        $this->ai_provider = $settings['ai_provider'] ?? 'openai';
        $this->ai_api_key = $settings['ai_api_key'] ?? '';
        $this->ai_model = $settings['ai_model'] ?? 'gpt-4o-mini';

        $this->welcome_video_url = $settings['welcome_video_url'] ?? '';
        $this->welcome_message = $settings['welcome_message'] ?? 'Bienvenido a Diogenes, tu plataforma de gestión legal.';
        $this->welcome_title = $settings['welcome_title'] ?? 'Bienvenido a tu Espacio Legal';
        $this->welcome_version = intval($settings['welcome_version'] ?? 1);
        $this->welcome_target = $settings['welcome_target'] ?? 'all';

        $this->infrastructure_domain_expiry = $settings['infrastructure_domain_expiry'] ?? '';
        $this->infrastructure_vps_cost = $settings['infrastructure_vps_cost'] ?? '';
        $this->infrastructure_vps_provider = $settings['infrastructure_vps_provider'] ?? '';
        $this->infrastructure_ai_budget = $settings['infrastructure_ai_budget'] ?? '';
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
            'mail_lawyer_invitation_subject' => $this->mail_lawyer_invitation_subject,
            'mail_lawyer_invitation_body' => $this->mail_lawyer_invitation_body,
            'mail_user_welcome_subject' => $this->mail_user_welcome_subject,
            'mail_user_welcome_body' => $this->mail_user_welcome_body,
            'max_file_size_mb' => $this->max_file_size_mb,
            'ocr_mode' => $this->ocr_mode,
            'ai_provider' => $this->ai_provider,
            'ai_api_key' => $this->ai_api_key,
            'ai_model' => $this->ai_model,
            'welcome_video_url' => $this->welcome_video_url,
            'welcome_message' => $this->welcome_message,
            'welcome_title' => $this->welcome_title,
            'welcome_version' => $this->welcome_version,
            'welcome_target' => $this->welcome_target,
            'infrastructure_domain_expiry' => $this->infrastructure_domain_expiry,
            'infrastructure_vps_cost' => $this->infrastructure_vps_cost,
            'infrastructure_vps_provider' => $this->infrastructure_vps_provider,
            'infrastructure_ai_budget' => $this->infrastructure_ai_budget,
        ];

        foreach ($data as $key => $value) {
            DB::table('global_settings')->updateOrInsert(
                ['key' => $key],
                ['value' => $value, 'updated_at' => now()]
            );
        }
        
        // Clean up legacy key
        DB::table('global_settings')->where('key', 'ocr_enabled')->delete();

        // Audit Log
        $this->logAudit('editar', 'Configuración', "Actualizó la configuración global del despacho", []);

        session()->flash('message', 'Configuración guardada correctamente.');
        $this->dispatch('notify', 'Configuraciones globales actualizadas correctamente.');
    }



    public function testAI()
    {
        if (empty($this->ai_api_key)) {
            session()->flash('error', 'Debe configurar la API Key de IA primero.');
            return;
        }

        try {
            $url = 'https://api.openai.com/v1/models'; // Default OpenAI
            
            if ($this->ai_provider === 'groq') {
                $url = 'https://api.groq.com/openai/v1/models';
            } elseif ($this->ai_provider === 'deepseek') {
                $url = 'https://api.deepseek.com/models';
            } elseif ($this->ai_provider === 'anthropic') {
                // Anthropic doesn't have a simple public 'models' endpoint that behaves exactly the same, 
                // but checking account or a simple message is better. Let's send a dummy message for all.
                // Sending a dummy message is the most reliable test.
                $url = null; 
            }

            // Real Test: Send a minimal "Hello" message
            // To do this simply without instantiating the full service logic again here, 
            // let's just do a provider check.
            
            $service = new \App\Services\AIService();
            // We need to temporarily force the settings into the service or just trust the manual check.
            // Since AIService reads from DB in constructor, and we haven't saved DB yet if user just typed it...
            // We'll do a direct HTTP check mirroring AIService logic.

            if ($this->ai_provider === 'anthropic') {
                 $response = \Illuminate\Support\Facades\Http::withHeaders([
                    'x-api-key' => $this->ai_api_key,
                    'anthropic-version' => '2023-06-01',
                ])->post('https://api.anthropic.com/v1/messages', [
                    'model' => 'claude-3-haiku-20240307',
                    'max_tokens' => 10,
                    'messages' => [['role' => 'user', 'content' => 'Hi']],
                ]);
            } else {
                // OpenAI Compatible (OpenAI, DeepSeek, Groq)
                $targetUrl = match($this->ai_provider) {
                    'groq' => 'https://api.groq.com/openai/v1/chat/completions',
                    'deepseek' => 'https://api.deepseek.com/chat/completions',
                    default => 'https://api.openai.com/v1/chat/completions'
                };
                
                $model = match($this->ai_provider) {
                    'groq' => 'llama-3.1-8b-instant', // Smallest/Fastest for test
                    'deepseek' => 'deepseek-chat',
                    default => 'gpt-3.5-turbo'
                };

                $response = \Illuminate\Support\Facades\Http::withToken($this->ai_api_key)
                    ->post($targetUrl, [
                        'model' => $model,
                        'messages' => [['role' => 'user', 'content' => 'Hi']],
                        'max_tokens' => 5
                    ]);
            }

            if ($response->successful()) {
                session()->flash('message', 'Conexión Exitosa con ' . ucfirst($this->ai_provider));
                $this->dispatch('notify', 'Conexión Exitosa con ' . ucfirst($this->ai_provider));
            } else {
                throw new \Exception('Error ' . $response->status() . ': ' . $response->body());
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Error de conexión IA: ' . $e->getMessage());
            $this->dispatch('notify', 'Error: ' . $e->getMessage());
        }
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
