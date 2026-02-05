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
        $publications = SjfPublication::query()
            ->when($this->search, function ($query) {
                // In future: use Semantic Search if available
                $query->where('rubro', 'like', '%' . $this->search . '%')
                      ->orWhere('texto', 'like', '%' . $this->search . '%')
                      ->orWhere('reg_digital', 'like', '%' . $this->search . '%');
            })
            ->when($this->instancia, function ($query) {
                $query->where('instancia', 'like', '%' . $this->instancia . '%');
            })
            ->orderBy('fecha_publicacion', 'desc')
            ->orderBy('reg_digital', 'desc')
            ->paginate(15);

        return view('livewire.sjf.index', [
            'publications' => $publications,
        ]);
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
