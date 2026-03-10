<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\DofPublication;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class DofService
{
    protected $baseUrl = 'https://sidof.segob.gob.mx/dof/sidof';

    /**
     * Fetch publications for a specific date from SIDOF API.
     * 
     * @param string|Carbon $date
     * @return int Number of new records created
     */
    public function fetchDailyPublications($date)
    {
        $date = Carbon::parse($date);
        $formattedDate = $date->format('d-m-Y');
        
        // Endpoint structure based on public knowledge of SIDOF: /notas/dd-mm-yyyy
        $url = "{$this->baseUrl}/notas/{$formattedDate}";
        
        try {
            // Note: This is an example request. Actual API might need specific headers or slightly different path.
            // Using a generic get request for now.
            $response = Http::withOptions(['verify' => false])->timeout(30)->get($url);

            if ($response->successful()) {
                $data = $response->json();
                
                // Merge all types of notes
                $notas = array_merge(
                    $data['NotasMatutinas'] ?? [],
                    $data['NotasVespertinas'] ?? [],
                    $data['NotasExtraordinarias'] ?? []
                );

                $count = 0;
                foreach ($notas as $nota) {
                    // Check required fields
                    if (!isset($nota['codNota'])) continue;

                    // Avoid duplicates
                    if (DofPublication::where('cod_nota', $nota['codNota'])->exists()) {
                        continue;
                    }

                    DofPublication::create([
                        'fecha_publicacion' => $date->format('Y-m-d'),
                        'cod_nota' => $nota['codNota'],
                        'titulo' => $this->cleanText($nota['titulo'] ?? 'Sin título'),
                        'resumen' => $this->cleanText($nota['resumen'] ?? strip_tags($nota['contenido'] ?? '')),
                        'link_pdf' => $this->constructPdfLink($nota, $date),
                        'seccion' => $nota['codSeccion'] ?? $nota['seccion'] ?? null,
                        'organismo' => $nota['nombreCodOrgaDos'] ?? $nota['organismo'] ?? null,
                        'texto_completo' => null, 
                    ]);
                    $count++;
                }
                
                return $count;
            } else {
                Log::error("DOF API Error: " . $response->status());
                return 0;
            }

        } catch (\Exception $e) {
            Log::error("DOF Connection Error: " . $e->getMessage());
            return 0;
        }
    }

    protected function constructPdfLink($nota, $date)
    {
        // Logic to construct PDF link if not directly provided
        // Often: https://dof.gob.mx/nota_detalle.php?codigo={codNota}&fecha={dd/mm/yyyy}
        if (isset($nota['codNota'])) {
             $fechaParams = $date->format('d/m/Y');
             return "https://dof.gob.mx/nota_detalle.php?codigo={$nota['codNota']}&fecha={$fechaParams}#gsc.tab=0";
        }
        return null;
    }

    /**
     * Search publications.
     * Currently standard SQL, ready for vector search upgrade.
     */
    public function search($queryRaw, $filters = [])
    {
        $query = DofPublication::query();

        if (!empty($queryRaw)) {
            // Standard search (keyword based)
            $query->where(function($q) use ($queryRaw) {
                $q->where('titulo', 'like', "%{$queryRaw}%")
                  ->orWhere('resumen', 'like', "%{$queryRaw}%")
                  ->orWhere('texto_completo', 'like', "%{$queryRaw}%");
            });
            
            // TODO: Semantic Search integration point
            // $vector = $this->aiService->getEmbeddings($queryRaw);
            // $query->semanticSearch($vector);
        }

        if (isset($filters['date_from'])) {
            $query->whereDate('fecha_publicacion', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->whereDate('fecha_publicacion', '<=', $filters['date_to']);
        }

        if (isset($filters['organismo'])) {
            $query->where('organismo', 'like', "%{$filters['organismo']}%");
        }

        return $query->orderBy('fecha_publicacion', 'desc')->paginate(20);
    }
    protected function cleanText($text)
    {
        if (is_array($text)) return '';
        return trim(preg_replace('/\s+/', ' ', $text));
    }
}
