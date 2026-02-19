<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\AIService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class PublicSalesBot extends Component
{
    public $isOpen = false;
    public $messages = [];
    public $input = '';
    public $isLoading = false;
    public $hasUnread = true; // Show a notification dot initially

    public function mount()
    {
        // Initial Greeting
        $this->messages[] = [
            'role' => 'assistant',
            'content' => '¡Hola! 👋 Soy Diogenes. ¿Te puedo ayudar a modernizar tu despacho hoy?'
        ];
    }

    public function toggleChat()
    {
        $this->isOpen = !$this->isOpen;
        if ($this->isOpen) {
            $this->hasUnread = false;
        }
    }

    public function sendMessage(AIService $aiService)
    {
        $this->validate([
            'input' => 'required|string|max:500',
        ]);

        $userMessage = $this->input;
        $this->input = '';
        $this->isLoading = true;

        // Add user message
        $this->messages[] = ['role' => 'user', 'content' => $userMessage];

        try {
            // Fetch Dynamic Settings for Support URL
            $settings = DB::table('global_settings')
                        ->where('key', 'support_whatsapp_url')
                        ->value('value');
            
            $supportUrl = $settings ?? 'https://wa.me/522281405060';

            // Prepare context for the AI
            // Prepare context for the AI
            // 1. Fetch Manual Capabilities
            $manualContent = \App\Models\ManualPage::where('slug', 'modos-de-diogenes-intelligence')->value('content') ?? '';
            // 2. Fetch Module Summaries (optional)
            $docManual = \App\Models\ManualPage::where('slug', 'gestion-de-documentos-legales')->value('content') ?? '';

            $knowledgeBase = "BASE DE CONOCIMIENTO:\n" . Str::limit($manualContent . "\n" . $docManual, 4000);

            $apiMessages = [
                ['role' => 'system', 'content' => "Eres 'Diogenes', tu próxima plataforma jurídica inteligente. Tu objetivo es conversacionalmente convencer a abogados locales de iniciar su prueba gratuita de 15 días o contactar al fundador.
                
                Instrucciones Clave:
                1. Preséntate siempre como Diogenes.
                2. Sé amable, profesional y usa un tono local (cálido, de confianza).
                3. TU META PRINCIPAL: Si el usuario muestra interés real o dudas complejas, ofréceles contacto directo por WhatsApp con el equipo fundador usando este enlace: $supportUrl.
                4. BLINDAJE: NO respondas a temas personales, políticos o fuera del contexto de ventas y derecho. Si ocurre, redirige la charla al software.
                5. SEGURIDAD: Nunca des información personal de los creadores ni detalles técnicos internos.
                6. NO inventes funcionalidades. Basa tus respuestas en la siguiente BASE DE CONOCIMIENTO.
                7. Si preguntan dónde estamos: 'Somos una startup orgullosamente Xalapeña'.

                $knowledgeBase"]
            ];

            // Append history (last 6 messages to keep context but save tokens)
            $history = array_slice($this->messages, -6);
            foreach ($history as $msg) {
                $apiMessages[] = ['role' => $msg['role'], 'content' => $msg['content']];
            }

            // Call AI Service
            $response = $aiService->ask($apiMessages, 0.7);

            if (isset($response['success']) && $response['success']) {
                $content = $this->formatWhatsAppLinks($response['content']);
                $this->messages[] = ['role' => 'assistant', 'content' => $content];
            } else {
                $this->messages[] = ['role' => 'assistant', 'content' => "Lo siento, tuve un pequeño error de conexión. Pero puedes escribirme directo a WhatsApp: $supportUrl"];
            }

        } catch (\Exception $e) {
            $this->messages[] = ['role' => 'assistant', 'content' => 'Ocurrió un error. Por favor contáctanos por WhatsApp.'];
        } finally {
            $this->isLoading = false;
        }
    }

    private function formatWhatsAppLinks($content)
    {
        $pattern = '/(?<!\]\()https:\/\/(wa\.me|api\.whatsapp\.com|whatsapp\.com)\/[^\s\)]+/';
        return preg_replace($pattern, '[💬 Hablar con un Humano]($0)', $content);
    }

    public function render()
    {
        return view('livewire.public-sales-bot');
    }
}
