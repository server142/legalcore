<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\AiProvider;
use Illuminate\Support\Facades\Crypt;

class SecurityMigrateKeys extends Command
{
    protected $signature = 'security:migrate-keys';
    protected $description = 'Migra API Keys de texto plano en global_settings a AiProvider encriptado';

    public function handle()
    {
        $this->info('🛡️  Iniciando migración segura de credenciales...');

        // 1. Obtener configuración actual insegura
        $settings = DB::table('global_settings')
            ->whereIn('key', ['ai_api_key', 'ai_provider', 'ai_model'])
            ->pluck('value', 'key');

        $apiKey = $settings['ai_api_key'] ?? null;
        $providerSlug = $settings['ai_provider'] ?? 'openai';
        $model = $settings['ai_model'] ?? 'gpt-4o-mini';

        if (empty($apiKey)) {
            $this->warn('⚠️  No se encontró API Key en global_settings. Verificando AiProvider...');
        } else {
            $this->info("🔑 Encontrada API Key en texto plano para proveedor: {$providerSlug}");
            
            // 2. Buscar o crear el Provider
            $provider = AiProvider::firstOrNew(['slug' => $providerSlug]);
            
            // Solo actualizamos si la key es diferente o nueva para evitar doble encriptación accidental
            // Pero como la DB tiene texto plano y el modelo espera encriptado, 
            // al asignar directo y save(), Laravel encriptará.
            
            $provider->name = ucfirst($providerSlug);
            $provider->api_key = $apiKey; // Eloquent will encrypt this automatically due to 'casts'
            $provider->default_model = $model;
            $provider->is_active = true;
            $provider->save();

            $this->info("✅ API Key migrada y ENCRIPTADA en tabla ai_providers (ID: {$provider->id})");
        }

        // 3. Verificar limpieza
        if ($this->confirm('¿Deseas eliminar la key en texto plano de global_settings para mayor seguridad?', true)) {
            DB::table('global_settings')
                ->where('key', 'ai_api_key')
                ->update(['value' => 'MIGRATED_TO_AI_PROVIDERS_SECURE']);
            
            $this->info('🗑️  Clave insegura eliminada de global_settings.');
        }

        // 4. Validar que podemos desencriptar
        $testProvider = AiProvider::where('slug', $providerSlug)->first();
        try {
            // Acceder al atributo debería desencriptarlo automáticamente
            $decrypted = $testProvider->api_key; 
            $masked = substr($decrypted, 0, 5) . '...' . substr($decrypted, -4);
            $this->info("🔓 Prueba de desencriptación exitosa: {$masked}");
        } catch (\Exception $e) {
            $this->error('❌ Error desencriptando: ' . $e->getMessage());
        }
    }
}
