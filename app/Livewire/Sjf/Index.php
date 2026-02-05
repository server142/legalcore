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
            // Use AI automatically if search phrase is significant (concept search)
            if (strlen($this->search) > 5) {
                // Semantic Search Logic
                $aiService = app(\App\Services\AIService::class);
                $queryVector = $aiService->getEmbeddings($this->search);

                if ($queryVector) {
                    // Fetch candidates with embeddings
                    // Ensure we cast or parse embedding_data if stored as string
                    $candidates = SjfPublication::whereNotNull('embedding_data')->get();
                    
                    $rankedIds = $candidates->map(function ($pub) use ($aiService, $queryVector) {
                        $vec = is_string($pub->embedding_data) ? json_decode($pub->embedding_data, true) : $pub->embedding_data;
                        
                        if (!is_array($vec)) return null;

                        return [
                            'id' => $pub->id,
                            'score' => $aiService->cosineSimilarity($queryVector, $vec)
                        ];
                    })
                    ->filter()
                    ->sortByDesc('score')
                    ->take(50)
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
            } else {
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
        $query->where(function ($q) {
            $q->where('rubro', 'like', '%' . $this->search . '%')
              ->orWhere('texto', 'like', '%' . $this->search . '%')
              ->orWhere('reg_digital', 'like', '%' . $this->search . '%');
        });
    }

    /**
     * Trigger a quick sync of latestesis
     */
    public function syncLatest()
    {
        $this->loading = true;
        
        try {
            $service = app(\App\Services\SjfService::class);
            $service->syncRecent(7);
            $this->dispatch('notify', 'Sincronización completada exitosamente.');
        } catch (\Exception $e) {
            $this->dispatch('notify', 'Error al sincronizar: ' . $e->getMessage());
        }

        $this->loading = false;
    }
}
