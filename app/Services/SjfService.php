<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\SjfPublication;
use Carbon\Carbon;

class SjfService
{
    // Validated Microservice Endpoint (2026)
    protected $apiUrl = 'https://sjf2.scjn.gob.mx/services/sjftesismicroservice/api/public/tesis';

    /**
     * Try to fetch recent publications.
     */
    public function syncRecent($days = 7)
    {
        Log::info("SJF: Attempting sync via Microservice...");

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                    'Referer' => 'https://sjf2.scjn.gob.mx/listado-resultado-tesis',
                    'Origin' => 'https://sjf2.scjn.gob.mx',
                    'Accept' => 'application/json, text/plain, */*',
                ])
                ->get($this->apiUrl, [
                    'page' => 1,
                    'size' => 50,
                    // 'sort' => 'fechaPublicacion,desc' // Investigar si soporta sort. Por defecto suele ser relevancia o fecha.
                ]);

            // Retry with POST if GET is not allowed (Error 405)
            // Or if we default to POST based on recent findings.
            // We use the payload structure provided by successful browser interception.
            if ($response->status() === 405 || $response->status() === 400) {
                Log::info("SJF: Switching to POST with Payload...");
                
                $payload = [
                    "classifiers" => [], // Empty to search global/recent
                    "searchTerms" => [],
                    "bFacet" => true,
                    "ius" => [],
                    "idApp" => "SJFAPP2020", // Critical auth/app ID
                    "lbSearch" => [],
                    "filterExpression" => ""
                ];

                $response = Http::timeout(30)
                    ->withHeaders([
                        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                        'Referer' => 'https://sjf2.scjn.gob.mx/listado-resultado-tesis',
                        'Origin' => 'https://sjf2.scjn.gob.mx',
                        'Accept' => 'application/json, text/plain, */*',
                        'Content-Type' => 'application/json'
                    ])
                    ->post($this->apiUrl . '?page=1&size=50', $payload);
            }

            if ($response->successful()) {
                $json = $response->json();
                
                // Spring Boot Pagination usually returns 'content' array.
                // Found 'documents' via debug logs (step 836)
                $items = $json['documents'] ?? $json['content'] ?? $json['result'] ?? $json['data'] ?? ($json['lista'] ?? []);
                
                if (empty($items) && is_array($json) && isset($json[0])) {
                    $items = $json;
                }

                if (empty($items)) {
                    Log::warning("SJF Microservice returned 200 but no items found in known keys.");
                    Log::info("Full JSON response: " . json_encode($json));
                    return 0;
                }

                Log::info("SJF: Found " . count($items) . " items. Sample item structure: " . json_encode($items[0]));
                return $this->processItems($items, 'microservice');
            }
            
            Log::error("SJF Microservice failed: " . $response->status());
            Log::info("Response Body Snippet: " . substr($response->body(), 0, 200));
            return "Connection failed (Status: " . $response->status() . ")";

        } catch (\Exception $e) {
            Log::error("SJF Sync Error: " . $e->getMessage());
            return "Exception: " . $e->getMessage();
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
                'embedding_data' => $embedding,
            ], $extra)
        );
    }
    
    protected function cleanText($text) {
        return trim(strip_tags($text));
    }

    protected function processItem($item)
    {
        // Common key variations in SCJN microservices
        $regDigital = $item['registroDigital'] ?? $item['reg_digital'] ?? $item['ius'] ?? $item['id'] ?? null;
        if (!$regDigital) return false;

        $rubro = $item['rubro'] ?? $item['Rubro'] ?? 'Sin rubro';
        $texto = $item['texto'] ?? $item['Texto'] ?? '';

        $this->storeWithEmbedding($regDigital, $this->cleanText($rubro), $this->cleanText($texto), [
            'precedentes' => $this->cleanText($item['precedentes'] ?? $item['Precedentes'] ?? null),
            'localizacion' => $item['localizacion'] ?? $item['Localizacion'] ?? (($item['epoca'] ?? '') . ' ' . ($item['instancia'] ?? '')),
            'fecha_publicacion' => isset($item['fechaPublicacion']) ? Carbon::parse($item['fechaPublicacion']) : null,
            'tipo_tesis' => $item['tipoTesis'] ?? $item['tipo'] ?? null,
            'instancia' => $item['instancia'] ?? null,
            'materia' => $item['materia'] ?? null,
        ]);
        
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
