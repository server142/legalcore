<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AIService
{
    protected $provider;
    protected $apiKey;
    protected $model;

    public function __construct()
    {
        $settings = DB::table('global_settings')->whereIn('key', ['ai_provider', 'ai_api_key', 'ai_model'])->pluck('value', 'key');
        
        $this->provider = $settings['ai_provider'] ?? 'openai';
        $this->apiKey = $settings['ai_api_key'] ?? '';
        $this->model = $settings['ai_model'] ?? 'gpt-4o-mini';
    }

    /**
     * Send a completion request to the AI (Text Generation)
     */
    public function ask(array $messages, float $temperature = 0.2, int $maxTokens = 6000)
    {
        if (empty($this->apiKey)) {
            return ['error' => 'AI API Key not configured.'];
        }

        if ($this->provider === 'openai') {
            return $this->askOpenAI($messages, $temperature, $maxTokens);
        }

        if ($this->provider === 'deepseek') {
            return $this->askDeepSeek($messages, $temperature, $maxTokens);
        }

        if ($this->provider === 'groq') {
            return $this->askGroq($messages, $temperature, $maxTokens);
        }

        if ($this->provider === 'anthropic') {
            return $this->askAnthropic($messages, $temperature, $maxTokens);
        }

        return ['error' => 'Provider not fully implemented yet.'];
    }

    protected function askOpenAI($messages, $temperature, $maxTokens)
    {
        return $this->sendOpenAIStyleRequest(
            'https://api.openai.com/v1/chat/completions',
            $messages,
            $temperature,
            $maxTokens
        );
    }

    protected function askDeepSeek($messages, $temperature, $maxTokens)
    {
        return $this->sendOpenAIStyleRequest(
            'https://api.deepseek.com/chat/completions',
            $messages,
            $temperature,
            $maxTokens
        );
    }

    protected function askGroq($messages, $temperature, $maxTokens)
    {
        // Groq is OpenAI Compatible
        // Default model if none specified: llama3-70b-8192
        if (empty($this->model) || $this->model === 'gpt-4o-mini') {
            $this->model = 'llama-3.3-70b-versatile'; 
        }

        return $this->sendOpenAIStyleRequest(
            'https://api.groq.com/openai/v1/chat/completions',
            $messages,
            $temperature,
            $maxTokens
        );
    }

    protected function sendOpenAIStyleRequest($url, $messages, $temperature, $maxTokens)
    {
        try {
            // OpenAI o1 and newer models replaced 'max_tokens' with 'max_completion_tokens'.
            // DeepSeek and Groq still use 'max_tokens'.
            $tokenParam = str_contains($url, 'api.openai.com') ? 'max_completion_tokens' : 'max_tokens';
            
            // OpenAI o1 models do not support 'temperature' (it must be 1, or omitted/default).
            // We'll keep it for now unless it causes errors, but usually they just ignore it or we might need to suppress it for o1.
            $payload = [
                'model' => $this->model,
                'messages' => $messages,
                $tokenParam => $maxTokens,
            ];

            // Only add temperature if it's NOT an o1 model (o1-preview, o1-mini, etc.)
            // o1 models strictly require temperature to be 1 or omitted.
            // Using str_contains is safer than str_starts_with in case the model name has prefixes/suffixes.
            if (!str_contains($this->model, 'o1')) {
                $payload['temperature'] = $temperature;
            } else {
                 // For o1 models, we might want to explicitly force temperature to 1 just in case, 
                 // but omitting it is the safest bet as per current docs.
                 // $payload['temperature'] = 1; 
            }

            $response = Http::withToken($this->apiKey)
                ->timeout(120) // Increased timeout for reasoning models
                ->post($url, $payload);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'content' => $response->json('choices.0.message.content'),
                    'usage' => $response->json('usage'),
                ];
            }

            Log::error('AI Provider Error (' . $url . '): ' . $response->body());
            return ['success' => false, 'error' => 'Error: ' . $response->status() . ' - ' . $response->json('error.message', 'Unknown custom error')];

        } catch (\Exception $e) {
            Log::error('AI Connection Error: ' . $e->getMessage());
            return ['success' => false, 'error' => 'Connection Error: ' . $e->getMessage()];
        }
    }

    protected function askAnthropic($messages, $temperature, $maxTokens)
    {
        try {
            // Convert OpenAI style messages system/user to Anthropic style if needed
            // Anthropic separates 'system' parameter from 'messages' usually, but recent APIs are flexible.
            // Best practice: extract system message.
            $systemMessage = '';
            $filteredMessages = [];
            
            foreach ($messages as $msg) {
                if ($msg['role'] === 'system') {
                    $systemMessage .= $msg['content'] . "\n";
                } else {
                    $filteredMessages[] = $msg;
                }
            }

            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
                'anthropic-version' => '2023-06-01',
                'content-type' => 'application/json',
            ])->timeout(60)->post('https://api.anthropic.com/v1/messages', [
                'model' => $this->model,
                'max_tokens' => $maxTokens,
                'system' => $systemMessage,
                'messages' => $filteredMessages,
                'temperature' => $temperature,
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'content' => $response->json('content.0.text'),
                    'usage' => $response->json('usage'),
                ];
            }

            Log::error('Anthropic Error: ' . $response->body());
            return ['success' => false, 'error' => 'Anthropic Error: ' . $response->status()];

        } catch (\Exception $e) {
            return ['success' => false, 'error' => 'Anthropic Connection Error: ' . $e->getMessage()];
        }
    }
}
