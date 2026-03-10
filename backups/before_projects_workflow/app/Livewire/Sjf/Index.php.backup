<?php

namespace App\Livewire\Sjf;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\SjfPublication;
use Illuminate\Support\Facades\Http;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $useAI = true; // Always on by default
    public $loading = false;

    // Filters
    public $instancia = '';
    public $epoca = '';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = SjfPublication::query();

        if ($this->search) {
            // Skip AI for numeric searches (like registro digital)
            $isNumericSearch = is_numeric($this->search) || preg_match('/^\d+$/', trim($this->search));
            
            // Use AI automatically if search phrase is significant (concept search) and NOT numeric
            if (!$isNumericSearch && strlen($this->search) > 5) {
                try {
                    // Semantic Search Logic
                    $aiService = app(App\Services\AIService::class);
                    $queryVector = $aiService->getEmbeddings($this->search);

                    if ($queryVector) {
                        // OPTIMIZED: Use FullText to filter candidates first if table is large
                        $candidateIds = SjfPublication::query()
                            ->whereRaw("MATCH(rubro, texto) AGAINST(? IN NATURAL LANGUAGE MODE)", [$this->search])
                            ->limit(1000)
                            ->pluck('id');

                        if ($candidateIds->isEmpty()) {
                            $candidateIds = SjfPublication::latest('fecha_publicacion')->take(500)->pluck('id');
                        }

                        $candidates = SjfPublication::whereIn('id', $candidateIds)
                            ->whereNotNull('embedding_data')
                            ->select('id', 'rubro', 'texto', 'embedding_data', 'fecha_publicacion', 'reg_digital', 'instancia')
                            ->limit(300)
                            ->get();
                        
                        $rankedIds = $candidates->map(function ($pub) use ($aiService, $queryVector) {
                            $vec = $pub->embedding_data; 
                            if (!is_array($vec)) return null;

                            return [
                                'id' => $pub->id,
                                'score' => $aiService->cosineSimilarity($queryVector, $vec)
                            ];
                        })
                        ->filter(fn($item) => $item !== null && $item['score'] > 0.55) // Refined threshold
                        ->sortByDesc('score')
                        ->take(50) // Show top 50 in current view
                        ->pluck('id');

                        if ($rankedIds->isNotEmpty()) {
                            $query->whereIn('id', $rankedIds)
                                  ->orderByRaw('FIELD(id, ' . $rankedIds->implode(',') . ')');
                        } else {
                            $this->applyTraditionalSearch($query);
                        }
                    } else {
                        $this->applyTraditionalSearch($query);
                    }
                } catch (\Exception $e) {
                    // Fallback to traditional search if AI fails
                    \Log::warning("AI Search failed, falling back to traditional: " . $e->getMessage());
                    $this->applyTraditionalSearch($query);
                }
            } else {
                // Use traditional search for numeric/short queries
                $this->applyTraditionalSearch($query);
            }
        }

        $publications = $query
            ->when($this->instancia, function ($q) {
                $q->where('instancia', 'like', '%' . $this->instancia . '%');
            })
            ->when(strlen($this->search) <= 5, function($q) {
                $q->orderBy('fecha_publicacion', 'desc')
                  ->orderBy('reg_digital', 'desc');
            })
            ->paginate(15);

        return view('livewire.sjf.index', [
            'publications' => $publications,
        ]);
    }

    protected function applyTraditionalSearch($query)
    {
        $query->whereRaw("MATCH(rubro, texto) AGAINST(? IN NATURAL LANGUAGE MODE)", [$this->search])
              ->orWhere('reg_digital', 'like', '%' . $this->search . '%')
              ->orderByRaw("MATCH(rubro, texto) AGAINST(? IN NATURAL LANGUAGE MODE) DESC", [$this->search]);
    }

    /**
     * Trigger a quick sync of latest tesis
     */
    public function syncLatest()
    {
        $this->loading = true;
        
        try {
            $service = app(App\Services\SjfService::class);
            $service->syncRecent(7);
            $this->dispatch('notify', 'Sincronización completada exitosamente.');
        } catch (\Exception $e) {
            $this->dispatch('notify', 'Error al sincronizar: ' . $e->getMessage());
        }

        $this->loading = false;
    }
}


