<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\DofPublication;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

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
        $aiService = app(\App\Services\AIService::class); // Resolve manually or via DI
        
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

                    $titulo = $this->cleanText($nota['titulo'] ?? 'Sin título');
                    $resumen = $this->cleanText($nota['resumen'] ?? strip_tags($nota['contenido'] ?? ''));
                    
                    // Generate Embedding (Semantic Search) - Protected
                    try {
                        $textToEmbed = substr($titulo . "\n" . $resumen, 0, 8000); // Limit context length
                        $embedding = $aiService->getEmbeddings($textToEmbed);
                    } catch (\Exception $e) {
                        Log::warning("Embedding failed for DOF Nota {$nota['codNota']}: " . $e->getMessage());
                        $embedding = null;
                    }

                    DofPublication::create([
                        'fecha_publicacion' => $date->format('Y-m-d'),
                        'cod_nota' => $nota['codNota'],
                        'titulo' => $titulo,
                        'resumen' => $resumen,
                        'link_pdf' => $this->constructPdfLink($nota, $date),
                        'seccion' => $nota['codSeccion'] ?? $nota['seccion'] ?? null,
                        'organismo' => $nota['nombreCodOrgaDos'] ?? $nota['organismo'] ?? null,
                        'texto_completo' => null, 
                        'embedding_data' => $embedding ? json_encode($embedding) : null,
                    ]);
                    $count++;
                    
                    // Prevent rate limits
                    usleep(rand(100000, 300000));
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
        
        // Apply Filters first (Date, Organismo) to reduce search space
        if (isset($filters['date_from'])) {
            $query->whereDate('fecha_publicacion', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->whereDate('fecha_publicacion', '<=', $filters['date_to']);
        }

        if (isset($filters['organismo']) && !empty($filters['organismo'])) {
            $query->where('organismo', 'like', "%{$filters['organismo']}%");
        }

        // Semantic Search Logic
        if (!empty($queryRaw)) {
            $aiService = app(\App\Services\AIService::class);
            $vector = $aiService->getEmbeddings($queryRaw);

            if ($vector) {
                // CANDIDATE POOL: Get the most relevant candidates via FullText first to reduce vector calculations
                // This prevents memory exhaustion and speed up the process significantly.
                $searchTerms = collect(explode(' ', $queryRaw))
                    ->filter()
                    ->map(fn($term) => "+{$term}*")
                    ->implode(' ');
                
                $candidateIds = DofPublication::query()
                    ->select('id')
                    ->whereRaw("MATCH(titulo, resumen) AGAINST(? IN BOOLEAN MODE)", [$searchTerms])
                    ->when(isset($filters['date_from']), function($q) use ($filters) {
                        $q->whereDate('fecha_publicacion', '>=', $filters['date_from']);
                    })
                    ->limit(1000)
                    ->pluck('id');

                if ($candidateIds->isEmpty()) {
                    $candidateIds = DofPublication::latest('fecha_publicacion')->take(100)->pluck('id');
                }

                $candidates = DofPublication::whereIn('id', $candidateIds)->limit(100)->get();

                // Calculate similarity and FILTER by threshold to "reduce" results
                $scoredCandidates = $candidates->map(function ($item) use ($vector) {
                    $itemVec = is_string($item->embedding_data) ? json_decode($item->embedding_data, true) : $item->embedding_data;
                     
                    if (!$itemVec || !is_array($itemVec)) {
                        // If no embedding, we still keep it if it matched keywords, 
                        // but give it a neutral score below the high-relevance threshold
                        $item->score = 0.50; 
                        return $item;
                    }
                    
                    $item->score = DofPublication::cosineSimilarity($vector, $itemVec);
                    return $item;
                })
                ->filter(fn($item) => $item->score >= 0.40) // Lower threshold to allow keyword matches
                ->sortByDesc('score');

                // Pagination logic for collection
                $page = Paginator::resolveCurrentPage() ?: 1;
                $perPage = 20;
                $results = $scoredCandidates->forPage($page, $perPage);

                return new LengthAwarePaginator(
                    $results,
                    $scoredCandidates->count(),
                    $perPage,
                    $page,
                    ['path' => Paginator::resolveCurrentPath()]
                );
            } else {
                // Fallback to FullText but with better ranking
                $searchTerms = collect(explode(' ', $queryRaw))
                    ->filter()
                    ->map(fn($term) => "+{$term}*")
                    ->implode(' ');
                
                $query->where(function($q) use ($searchTerms, $queryRaw) {
                    $q->whereRaw("MATCH(titulo, resumen) AGAINST(? IN BOOLEAN MODE)", [$searchTerms])
                      ->orWhere('titulo', 'like', "%{$queryRaw}%");
                });
            }
        }

        return $query->orderBy('fecha_publicacion', 'desc')->paginate(20);
    }
    protected function cleanText($text)
    {
        if (is_array($text)) return '';
        return trim(preg_replace('/\s+/', ' ', $text));
    }
}
