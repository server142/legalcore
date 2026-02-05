<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\SjfPublication;
use Carbon\Carbon;

class SjfService
{
    // Official API backend often used by mobile apps
    protected $apiUrl = 'https://jfapi.scjn.gob.mx/api/v1/jurisprudencia';

    /**
     * Try to fetch recent publications from the API.
     * 
     * @param int $days
     * @return int|bool Count of items or false on failure
     */
    public function syncRecent($days = 7)
    {
        // 1. Try modern JFAPI (JSON)
        // Endpoint structure usually: /busqueda or /lista
        // Using a generic search payload to get latest
        
        try {
            // Note: Endpoints change. This is a best-effort pattern matching standard REST APIs.
            // If this fails, we fall back to scraping logic or manual import.
            
            $response = Http::timeout(30)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
                    'Accept' => 'application/json',
                ])
                ->get("{$this->apiUrl}/busqueda", [
                    'limit' => 50,
                    'sort' => 'fecha_publicacion,desc'
                ]);

            if ($response->successful()) {
                $items = $response->json('result') ?? $response->json('data') ?? [];
                
                $count = 0;
                foreach ($items as $item) {
                    if ($this->processItem($item)) {
                        $count++;
                    }
                }
                return $count;
            }
            
            Log::warning("SJF API (Modern) failed: " . $response->status());
            
            // Fallback: Check if there's an RSS (Publicaciones recientes)
            // Not implemented yet to avoid delay.
            return false;

        } catch (\Exception $e) {
            Log::error("SJF Sync Error: " . $e->getMessage());
            return false;
        }
    }

    protected function processItem($item)
    {
        $regDigital = $item['registroDigital'] ?? $item['reg_digital'] ?? null;
        if (!$regDigital) return false;

        $aiService = app(\App\Services\AIService::class);
        $rubro = $item['rubro'] ?? 'Sin rubro';
        $texto = $item['texto'] ?? '';

        // Generate embedding if new
        $embedding = null;
        if (!SjfPublication::where('reg_digital', $regDigital)->exists()) {
             $embedding = $aiService->getEmbeddings($rubro . "\n" . $texto);
        }

        SjfPublication::updateOrCreate(
            ['reg_digital' => $regDigital],
            [
                'rubro' => $rubro,
                'texto' => $texto,
                'precedentes' => $item['precedentes'] ?? null,
                'localizacion' => $item['localizacion'] ?? ($item['epoca'] . ' ' . $item['instancia']),
                'fecha_publicacion' => isset($item['fechaPublicacion']) ? Carbon::parse($item['fechaPublicacion']) : null,
                'tipo_tesis' => $item['tipoTesis'] ?? null,
                'instancia' => $item['instancia'] ?? null,
                'materia' => $item['materia'] ?? null,
                'embedding_data' => $embedding ? json_encode($embedding) : null,
            ]
        );
        
        return true;
    }

    /**
     * Create or Update a publication record
     */
    public function storePublication($data)
    {
        // Implementation for storage
        // Similar to DofService but mapped to SJF columns
        if (empty($data['reg_digital'])) return false;
        
        SjfPublication::updateOrCreate(
            ['reg_digital' => $data['reg_digital']],
            $data
        );
        
        return true;
    }
}
