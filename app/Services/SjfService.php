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
    protected $odataUrl = 'https://sjf2.scjn.gob.mx/sjfsist/odata/Tesis';

    /**
     * Try to fetch recent publications.
     * 
     * @param int $days
     * @return int|string Count of items or Error Message string
     */
    public function syncRecent($days = 7)
    {
        // 1. Try modern JFAPI (JSON)
        try {
            $response = Http::timeout(20)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0',
                    'Accept' => 'application/json',
                ])
                ->get("{$this->apiUrl}/busqueda", ['limit' => 20, 'sort' => 'fecha_publicacion,desc']);

            if ($response->successful()) {
                $items = $response->json('result') ?? $response->json('data') ?? [];
                return $this->processItems($items);
            }
            
            Log::warning("SJF API V1 failed: " . $response->status());
        } catch (\Exception $e) {
            Log::warning("SJF API V1 Exception: " . $e->getMessage());
        }

        // 2. Fallback: OData Endpoint (Often more open)
        try {
            Log::info("Attempting OData Fallback...");
            $response = Http::timeout(30)
                ->withOptions(['verify' => false]) // SCJN SSL sometimes has issues
                ->get($this->odataUrl, [
                    '$orderby' => 'FechaPublicacion desc',
                    '$top' => 20,
                    '$format' => 'json'
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $items = $data['value'] ?? []; // OData collection is usually in 'value'
                return $this->processItems($items, 'odata');
            }
            Log::error("SJF OData failed: " . $response->status());
            return "Connection failed (API: " . $response->status() . ")";

        } catch (\Exception $e) {
            Log::error("SJF Sync/OData Error: " . $e->getMessage());
            return "Connection Error: " . $e->getMessage();
        }
    }

    public function processItems($items, $source = 'api')
    {
        $count = 0;
        foreach ($items as $item) {
            $success = ($source === 'odata') ? $this->processODataItem($item) : $this->processItem($item);
            if ($success) $count++;
        }
        return $count;
    }

    protected function processODataItem($item)
    {
        $regDigital = $item['RegistroDigital'] ?? null;
        if (!$regDigital) return false;

        $rubro = $this->cleanText($item['Rubro'] ?? 'Sin rubro');
        $texto = $this->cleanText($item['Texto'] ?? '');
        
        $this->storeWithEmbedding($regDigital, $rubro, $texto, [
            'precedentes' => $this->cleanText($item['Precedentes'] ?? null),
            'localizacion' => $item['Localizacion'] ?? null,
            'fecha_publicacion' => isset($item['FechaPublicacion']) ? Carbon::parse($item['FechaPublicacion']) : null,
            'tipo_tesis' => $item['TipoTesis'] ?? null,
            'instancia' => $item['Instancia'] ?? null,
            'materia' => $item['Materia'] ?? null,
        ]);
        
        return true;
    }

    protected function storeWithEmbedding($reg, $rubro, $texto, $extra)
    {
        // Check existence to avoid re-embedding cost
        if (SjfPublication::where('reg_digital', $reg)->exists()) {
             // Maybe update? For now skip cost.
             return;
        }

        $aiService = app(\App\Services\AIService::class);
        $embedding = $aiService->getEmbeddings($rubro . "\n" . $texto);

        SjfPublication::updateOrCreate(
            ['reg_digital' => $reg],
            array_merge([
                'rubro' => $rubro,
                'texto' => $texto,
                'embedding_data' => $embedding ? json_encode($embedding) : null,
            ], $extra)
        );
    }
    
    protected function cleanText($text) {
        return trim(strip_tags($text));
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
