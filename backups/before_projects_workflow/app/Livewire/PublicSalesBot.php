<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\AIService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PublicSalesBot extends Component
{
    public bool   $isOpen   = false;
    public array  $messages = [];
    public string $input    = '';
    public bool   $isLoading = false;
    public bool   $hasUnread = true;

    /** 'sales' → landing/directorio  |  'support' → app autenticada */
    public string $mode = 'sales';

    public function mount(string $mode = 'sales')
    {
        $this->mode = $mode;

        $greeting = match ($mode) {
            'support' => '¡Hola! 👋 Soy Diogenes AI. ¿En qué te puedo ayudar hoy? Puedo guiarte con el uso de la plataforma, explicarte funcionalidades o resolver tus dudas.',
            'sales'   => '¡Hola! 👋 Soy Diogenes. ¿Te puedo ayudar a modernizar tu despacho hoy?',
            default   => '¡Hola! 👋 Soy el asistente del Directorio Legal Diogenes. Puedo ayudarte a encontrar el abogado que necesitas. ¿En qué área legal requieres ayuda?',
        };

        $this->messages[] = ['role' => 'assistant', 'content' => $greeting];
    }

    public function toggleChat(): void
    {
        $this->isOpen = !$this->isOpen;
        if ($this->isOpen) {
            $this->hasUnread = false;
        }
    }

    public function sendMessage(AIService $aiService): void
    {
        $this->validate(['input' => 'required|string|max:1000']);

        $userMessage  = $this->input;
        $this->input  = '';
        $this->isLoading = true;

        $this->messages[] = ['role' => 'user', 'content' => $userMessage];

        try {
            $supportUrl = DB::table('global_settings')
                            ->where('key', 'support_whatsapp_url')
                            ->value('value') ?? 'https://wa.me/522281405060';

            $systemPrompt = $this->buildSystemPrompt($supportUrl);

            $apiMessages = [['role' => 'system', 'content' => $systemPrompt]];

            // Historial de los últimos 8 mensajes para mantener contexto sin desperdiciar tokens
            foreach (array_slice($this->messages, -8) as $msg) {
                $apiMessages[] = ['role' => $msg['role'], 'content' => $msg['content']];
            }

            $response = $aiService->ask($apiMessages, 0.7);

            if (isset($response['success']) && $response['success']) {
                $content = $this->formatWhatsAppLinks($response['content']);
                $this->messages[] = ['role' => 'assistant', 'content' => $content];
            } else {
                $this->messages[] = ['role' => 'assistant', 'content' => "Lo siento, tuve un error momentáneo. Contáctanos por WhatsApp: $supportUrl"];
            }

        } catch (\Exception $e) {
            $this->messages[] = ['role' => 'assistant', 'content' => 'Ocurrió un error. Por favor contáctanos por WhatsApp.'];
        } finally {
            $this->isLoading = false;
        }
    }

    private function buildSystemPrompt(string $supportUrl): string
    {
        $knowledgeBase = $this->fetchKnowledge();

        return match ($this->mode) {

            'support' => "Eres 'Diogenes AI', el asistente de soporte técnico de la plataforma jurídica Diogenes.
Tu función es ayudar a usuarios autenticados a utilizar correctamente la plataforma.

Instrucciones:
1. Sé claro, amable y profesional. Usa tuteo.
2. Guía paso a paso cuando lo pidan.
3. Si el usuario reporta un error técnico complejo, ofréceles contacto directo con soporte: $supportUrl
4. BLINDAJE: NO respondas temas ajenos a la plataforma Diogenes (política, temas personales, etc.).
5. SEGURIDAD: Nunca reveles información técnica interna, credenciales o APIs.
6. Basa tus respuestas siempre en la BASE DE CONOCIMIENTO. No inventes funcionalidades.
7. El usuario ya está logueado — no le pidas que cree cuenta.

$knowledgeBase",

            'sales' => "Eres 'Diogenes', el asistente de ventas de la plataforma jurídica Diogenes.
Tu objetivo es convencer amablemente a abogados de iniciar su prueba gratuita de 15 días o contactar al equipo.

Instrucciones:
1. Preséntate siempre como Diogenes.
2. Tono cálido, profesional y local (startup xalapeña).
3. META: Si hay interés real o dudas, ofrece contacto WhatsApp: $supportUrl
4. BLINDAJE: Redirige cualquier tema fuera de ventas/derecho al software.
5. NO reveles detalles técnicos internos ni información personal de creadores.
6. NO inventes funcionalidades. Basa todo en la BASE DE CONOCIMIENTO.
7. Si preguntan ubicación: 'Somos una startup orgullosamente Xalapeña'.

$knowledgeBase",

            default => "Eres el asistente del Directorio Legal Diogenes.
Ayudas a personas a encontrar el abogado adecuado para su caso.

Instrucciones:
1. Escucha el problema del usuario y sugiere qué tipo de abogado necesita (especialidad).
2. Si el usuario quiere contactar a un abogado, indícale que puede usar el buscador del directorio.
3. Si un abogado quiere registrarse, envíalo a: " . url('/directory/join') . "
4. Tono empático, claro y sin tecnicismos legales.
5. BLINDAJE: No des asesoría legal específica. Siempre recomienda consultar con un profesional.
6. No inventar nombres de abogados ni datos de contacto.

$knowledgeBase",
        };
    }

    private function fetchKnowledge(): string
    {
        try {
            $manual  = \App\Models\ManualPage::where('slug', 'modos-de-diogenes-intelligence')->value('content') ?? '';
            $docMod  = \App\Models\ManualPage::where('slug', 'gestion-de-documentos-legales')->value('content') ?? '';
            return "BASE DE CONOCIMIENTO:\n" . Str::limit($manual . "\n" . $docMod, 4000);
        } catch (\Exception) {
            return '';
        }
    }

    private function formatWhatsAppLinks(string $content): string
    {
        $pattern = '/(?<!\]\()https:\/\/(wa\.me|api\.whatsapp\.com|whatsapp\.com)\/[^\s\)]+/';
        return preg_replace($pattern, '[💬 Hablar con un Humano]($0)', $content);
    }

    public function render()
    {
        return view('livewire.public-sales-bot');
    }
}
