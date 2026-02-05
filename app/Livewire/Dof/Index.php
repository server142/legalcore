<?php

namespace App\Livewire\Dof;

use Livewire\Component;
use Livewire\WithPagination;
use App\Services\DofService;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $dateFrom;
    public $dateTo;

    public function mount()
    {
        // Default to last 30 days maybe? Or just empty.
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function render(DofService $dofService)
    {
        $filters = [
            'date_from' => $this->dateFrom,
            'date_to' => $this->dateTo,
        ];

        $results = $dofService->search($this->search, $filters);

        return view('livewire.dof.index', [
            'results' => $results
        ])->layout('layouts.app', ['header' => 'Monitor Diario Oficial de la Federación']);
    }
}
