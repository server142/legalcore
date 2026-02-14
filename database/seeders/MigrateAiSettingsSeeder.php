<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AiProvider;
use Illuminate\Support\Facades\DB;

class MigrateAiSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get current AI settings from global_settings
        $settings = DB::table('global_settings')
            ->whereIn('key', ['ai_provider', 'ai_api_key', 'ai_model'])
            ->pluck('value', 'key');

        if (empty($settings['ai_provider']) || empty($settings['ai_api_key'])) {
            $this->command->warn('No hay configuración de IA previa para migrar.');
            return;
        }

        // Map provider names
        $providerMap = [
            'openai' => 'OpenAI',
            'anthropic' => 'Anthropic (Claude)',
            'groq' => 'Groq',
            'deepseek' => 'DeepSeek',
        ];

        $slug = $settings['ai_provider'];
        $name = $providerMap[$slug] ?? ucfirst($slug);

        // Create provider if it doesn't exist
        $provider = AiProvider::firstOrCreate(
            ['slug' => $slug],
            [
                'name' => $name,
                'api_key' => $settings['ai_api_key'],
                'default_model' => $settings['ai_model'] ?? 'gpt-4o-mini',
                'is_active' => true,
                'sort_order' => 1,
            ]
        );

        // Set as active
        $provider->setAsActive();

        $this->command->info("✅ Proveedor '{$name}' migrado y activado correctamente.");
    }
}
