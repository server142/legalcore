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
    public $useAI = false;
    public $loading = false;

    // Filters
    public $instancia = '';
    public $epoca = '';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedUseAI()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = SjfPublication::query();

        if ($this->search) {
            if ($this->useAI && strlen($this->search) > 3) {
                // Semantic Search Logic
                $aiService = app(\App\Services\AIService::class);
                $queryVector = $aiService->getEmbeddings($this->search);

                if ($queryVector) {
                    // Fetch candidates with embeddings
                    $candidates = SjfPublication::whereNotNull('embedding_data')->get();
                    
                    $rankedIds = $candidates->map(function ($pub) use ($aiService, $queryVector) {
                        return [
                            'id' => $pub->id,
                            'score' => $aiService->cosineSimilarity($queryVector, $pub->embedding_data)
                        ];
                    })
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
            ->when(!$this->useAI || !$this->search, function($q) {
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
     * Trigger a quick sync of latestesis (Example functionality)
     */
    public function syncLatest()
    {
        // This would call the Service/Command logic
        $this->loading = true;
        
        // Simulating delay for user feedback
        // In production, this might dispatch a Job
        
        $this->dispatch('notify', 'Sincronización iniciada en segundo plano.');
        $this->loading = false;
    }
}
