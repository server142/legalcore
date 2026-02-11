<?php

namespace App\Livewire\Library;

use Livewire\Component;
use Livewire\WithPagination;

class TemplateLibrary extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedCategory = 'Todos';
    public $selectedTemplate = null;
    public $showPreview = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'selectedCategory' => ['except' => 'Todos'],
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function selectCategory($category)
    {
        $this->selectedCategory = $category;
        $this->resetPage();
    }

    public function selectTemplate($id)
    {
        $this->selectedTemplate = \App\Models\LegalTemplate::find($id);
        $this->showPreview = true;
    }

    public function closePreview()
    {
        $this->showPreview = false;
        $this->selectedTemplate = null;
    }

    public function render()
    {
        $query = \App\Models\LegalTemplate::forTenant(auth()->user()->tenant_id);

        if ($this->selectedCategory !== 'Todos') {
            $query->where('category', $this->selectedCategory);
        }

        if (!empty($this->search)) {
            // Hibrid Search: FullText + Like fallback
            $searchTerms = collect(explode(' ', $this->search))
                ->filter()
                ->map(fn($term) => "{$term}*")
                ->implode(' ');

            $searchTermRaw = $this->search;
            $query->where(function($q) use ($searchTerms, $searchTermRaw) {
                $q->whereRaw("MATCH(name, description, extracted_text) AGAINST(? IN BOOLEAN MODE)", [$searchTerms])
                  ->orWhere('name', 'like', "%{$searchTermRaw}%");
            })->orderByRaw("MATCH(name, description, extracted_text) AGAINST(? IN BOOLEAN MODE) DESC", [$searchTerms]);
        } else {
            $query->latest();
        }

        $categories = \App\Models\LegalTemplate::forTenant(auth()->user()->tenant_id)
            ->select('category')
            ->distinct()
            ->pluck('category');

        return view('livewire.library.template-library', [
            'templates' => $query->paginate(12),
            'categories' => $categories
        ])->layout('layouts.app');
    }
}
