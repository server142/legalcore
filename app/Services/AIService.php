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
        // Try to get active provider from new system
        $activeProvider = \App\Models\AiProvider::getActive();
        
        if ($activeProvider) {
            $this->provider = $activeProvider->slug;
            $this->apiKey = $activeProvider->api_key; // Will be decrypted automatically
            $this->model = $activeProvider->default_model;
        } else {
            // Fallback to old global_settings system for backward compatibility
            $settings = DB::table('global_settings')->whereIn('key', ['ai_provider', 'ai_api_key', 'ai_model'])->pluck('value', 'key');
            
            $this->provider = $settings['ai_provider'] ?? 'openai';
            $this->apiKey = $settings['ai_api_key'] ?? '';
            $this->model = $settings['ai_model'] ?? 'gpt-4o-mini';
        }
    }

    /**
     * Send a completion request to the AI (Text Generation)
     */
    public function ask(array $messages, float $temperature = 0.2, int $maxTokens = 6000)
    {
        if (empty($this->apiKey)) {
            return ['error' => 'AI API Key not configured.'];
        }

        $result = ['error' => 'Provider not fully implemented yet.'];

        if ($this->provider === 'openai') {
            $result = $this->askOpenAI($messages, $temperature, $maxTokens);
        } elseif ($this->provider === 'deepseek') {
            $result = $this->askDeepSeek($messages, $temperature, $maxTokens);
        } elseif ($this->provider === 'groq') {
            $result = $this->askGroq($messages, $temperature, $maxTokens);
        } elseif ($this->provider === 'anthropic') {
            $result = $this->askAnthropic($messages, $temperature, $maxTokens);
        }

        if (isset($result['success']) && $result['success']) {
            $this->logUsage($result);
        }

        return $result;
    }

    protected function logUsage($result)
    {
        try {
            $usage = $result['usage'] ?? [];
            $input = $usage['prompt_tokens'] ?? ($usage['input_tokens'] ?? 0);
            $output = $usage['completion_tokens'] ?? ($usage['output_tokens'] ?? 0);
            
            $cost = $this->calculateCost($this->model, $input, $output);

            \App\Models\AiUsageLog::create([
                'tenant_id' => auth()->user()?->tenant_id,
                'user_id' => auth()->id(), // System tasks might be null
                'feature' => 'chat',
                'provider' => $this->provider,
                'model' => $this->model,
                'input_tokens' => $input,
                'output_tokens' => $output,
                'cost' => $cost
            ]);
        } catch (\Exception $e) {
            Log::warning('Failed to log AI usage: ' . $e->getMessage());
        }
    }

    protected function calculateCost($model, $input, $output)
    {
        // Prices per 1M tokens (USD)
        $prices = [
            'gpt-4o' => ['in' => 5.00, 'out' => 15.00],
            'gpt-4o-mini' => ['in' => 0.15, 'out' => 0.60],
            'gpt-4-turbo' => ['in' => 10.00, 'out' => 30.00],
            'gpt-3.5-turbo' => ['in' => 0.50, 'out' => 1.50],
            'claude-3-opus' => ['in' => 15.00, 'out' => 75.00],
            'claude-3-sonnet' => ['in' => 3.00, 'out' => 15.00],
            'claude-3-haiku' => ['in' => 0.25, 'out' => 1.25],
            'deepseek-chat' => ['in' => 0.14, 'out' => 0.28], // Approx
            'llama-3' => ['in' => 0.10, 'out' => 0.10], // Groq is cheap
        ];

        // Find closest match logic
        $match = 'gpt-4o-mini'; // default fallback
        foreach ($prices as $key => $price) {
            if (str_contains(strtolower($model), $key)) {
                $match = $key;
                break;
            }
        }

        $p = $prices[$match];
        return ($input * ($p['in'] / 1000000)) + ($output * ($p['out'] / 1000000));
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
                ->timeout(120)
                ->post($url, $payload);

            // Auto-retry mechanism for 'temperature' errors (common with o1 models if name detection fails)
            if ($response->status() === 400 && str_contains($response->body(), 'temperature')) {
                unset($payload['temperature']);
                $response = Http::withToken($this->apiKey)
                    ->timeout(120)
                    ->post($url, $payload);
            }

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
    /**
     * Generate embeddings for a given text input.
     * 
     * @param string $text
     * @return array|null The embedding vector or null on failure.
     */
    public function getEmbeddings($text)
    {
        if (empty($this->apiKey)) {
            Log::warning('Embedding skipped: No API Key');
            return null;
        }

        // Default to OpenAI standard if not specified otherwise
        $url = 'https://api.openai.com/v1/embeddings';
        $model = 'text-embedding-3-small';

        // Partial support for other providers could go here
        if ($this->provider !== 'openai') {
             // For now, assume keys might be cross-compatible or user is on OpenAI for this feature
             // Or we could log a warning.
             // We'll proceed optimistically but log if it's likely to fail.
        }

        try {
            $response = Http::withToken($this->apiKey)
                ->timeout(60)
                ->post($url, [
                    'input' => $text,
                    'model' => $model,
                ]);

            if ($response->successful()) {
                return $response->json('data.0.embedding');
            }
            
            Log::error('Embedding API Error: ' . $response->body());
            return null;

        } catch (\Exception $e) {
            Log::error('Embedding Connection Exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Calculate cosine similarity between two vectors.
     */
    public function cosineSimilarity(array $vec1, array $vec2): float
    {
        $dotProduct = 0.0;
        $normA = 0.0;
        $normB = 0.0;

        foreach ($vec1 as $i => $value) {
            $dotProduct += $value * ($vec2[$i] ?? 0);
            $normA += $value * $value;
            $normB += ($vec2[$i] ?? 0) * ($vec2[$i] ?? 0);
        }

        if ($normA == 0 || $normB == 0) return 0.0;
        
        return $dotProduct / (sqrt($normA) * sqrt($normB));
    }
}
