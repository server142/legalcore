<?php

namespace App\Livewire\Projects;

use Livewire\Component;

use App\Models\LegalProject;
use App\Models\LegalWorkflow;
use Livewire\WithPagination;

class ProjectIndex extends Component
{
    use WithPagination;

    public $search = '';

    public function requestMoreFlows()
    {
        $this->dispatch('open-ai-chat', [
            'context' => 'El usuario desea solicitar nuevos flujos de trabajo para el sistema de proyectos.',
            'message' => 'Hola Diogenes, me gustaría solicitar un nuevo flujo de trabajo para mi despacho.'
        ]);
        
        $this->dispatch('notify', [
            'type' => 'success', 
            'message' => '¡Petición enviada! Diogenes AI te atenderá en un momento.'
        ]);
    }

    public function render()
    {
        $projects = LegalProject::where('tenant_id', auth()->user()->tenant_id)
            ->with('workflow', 'cliente')
            ->when($this->search, function($q) {
                $q->where('title', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(10);

        $workflows = LegalWorkflow::where('is_active', true)->get();

        return view('livewire.projects.project-index', [
            'projects' => $projects,
            'workflows' => $workflows
        ])->layout('layouts.app');
    }
}
