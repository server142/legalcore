<?php

namespace App\Livewire\Projects;

use Livewire\Component;

use App\Models\LegalProject;
use App\Models\LegalWorkflow;
use App\Models\Cliente;
use App\Traits\Auditable;

class ProjectWizard extends Component
{
    use Auditable;

    public $project;
    public $workflow;
    public $currentStep = 1;
    public $responses = [];
    public $title = '';
    public $cliente_id = null;

    // Fast Client Creation
    public $showCreateClienteModal = false;
    public $newCliente = [
        'nombre' => '',
        'tipo' => 'persona_fisica',
        'email' => '',
        'telefono' => '',
    ];

    public function openCreateClienteModal()
    {
        $this->reset('newCliente');
        $this->newCliente = [
            'nombre' => '',
            'tipo' => 'persona_fisica',
            'email' => '',
            'telefono' => '',
        ];
        $this->showCreateClienteModal = true;
    }

    public function saveNewCliente()
    {
        $this->validate([
            'newCliente.nombre' => 'required|string|max:255',
            'newCliente.tipo' => 'required|in:persona_fisica,persona_moral',
            'newCliente.email' => 'nullable|email',
        ]);

        $cliente = Cliente::create(array_merge($this->newCliente, [
            'tenant_id' => auth()->user()->tenant_id
        ]));

        $this->logAudit('crear', 'Clientes', "Registró al cliente (Express via Project): {$cliente->nombre}", [
            'cliente_id' => $cliente->id
        ]);

        $this->cliente_id = $cliente->id;
        $this->showCreateClienteModal = false;
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Cliente creado y asignado con éxito.']);
    }

    public function mount($workflow = null, $project = null)
    {
        if ($project) {
            $this->project = LegalProject::findOrFail($project);
            $this->workflow = $this->project->workflow;
            $this->currentStep = $this->project->current_step;
            $this->responses = $this->project->data ?? [];
            $this->title = $this->project->title;
            $this->cliente_id = $this->project->cliente_id;
        } elseif ($workflow) {
            $this->workflow = LegalWorkflow::findOrFail($workflow);
            $this->title = 'Proyecto: ' . $this->workflow->name;
        } else {
            return redirect()->route('projects.index');
        }
    }

    public function nextStep()
    {
        $this->saveProgress();
        
        $totalSteps = count($this->workflow->steps);
        if ($this->currentStep < $totalSteps) {
            $this->currentStep++;
            $this->updateProgress();
        }
    }

    public function prevStep()
    {
        $this->saveProgress();
        if ($this->currentStep > 1) {
            $this->currentStep--;
            $this->updateProgress();
        }
    }

    public function investigateIA()
    {
        $this->dispatch('open-ai-chat', [
            'context' => "Estoy en el paso {$this->currentStep} del proyecto: {$this->workflow->name}. El paso se trata de: {$currentStepData['title']}. Ayúdame con jurisprudencia.",
        ]);
        $this->dispatch('notify', ['type' => 'info', 'message' => 'Consultando con Diogenes AI...']);
    }

    public function saveProgress()
    {
        if (!$this->project) {
            $this->project = LegalProject::create([
                'tenant_id' => auth()->user()->tenant_id,
                'user_id' => auth()->id(),
                'legal_workflow_id' => $this->workflow->id,
                'title' => $this->title,
                'status' => 'active',
                'current_step' => $this->currentStep,
                'data' => $this->responses,
                'progress' => $this->calculateProgress(),
                'cliente_id' => $this->cliente_id
            ]);
        } else {
            $this->project->update([
                'title' => $this->title,
                'current_step' => $this->currentStep,
                'data' => $this->responses,
                'progress' => $this->calculateProgress(),
                'cliente_id' => $this->cliente_id
            ]);
        }
    }

    public function calculateProgress()
    {
        $totalSteps = count($this->workflow->steps);
        return round(($this->currentStep / $totalSteps) * 100);
    }

    public function updateProgress()
    {
        if ($this->project) {
            $this->project->update([
                'current_step' => $this->currentStep,
                'progress' => $this->calculateProgress()
            ]);
        }
    }

    public function generateDocument()
    {
        $this->saveProgress();

        // Obtener el nombre sugerido de la plantilla del paso actual
        $steps = $this->workflow->steps;
        $currentStepData = collect($steps)->firstWhere('id', $this->currentStep);
        $templateName = $currentStepData['template_suggest'] ?? null;

        if (!$templateName) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'No se definió una plantilla para este paso.']);
            return;
        }

        // Buscar la plantilla en la biblioteca
        $template = \App\Models\LegalTemplate::where('name', 'like', "%$templateName%")
            ->forTenant(auth()->user()->tenant_id)
            ->first();

        if (!$template) {
            $this->dispatch('notify', ['type' => 'error', 'message' => "No se encontró la plantilla '$templateName' en la biblioteca. Por favor, súbela primero."]);
            return;
        }

        $extension = strtolower($template->extension);
        $originalPath = \Illuminate\Support\Facades\Storage::disk('public')->path($template->file_path);
        
        if (!file_exists($originalPath)) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'El archivo de la plantilla no existe en el servidor.']);
            return;
        }

        // Preparar mapeo de placeholders: nombre_menor -> [NOMBRE_MENOR]
        $replacements = [];
        foreach ($this->responses as $key => $value) {
            $upperKey = '[' . strtoupper($key) . ']';
            $replacements[$upperKey] = $value;
        }

        // Placeholders Globales
        $replacements['[FECHA_ACTUAL]'] = date('d/m/Y');
        $replacements['[CIUDAD_FIRMA]'] = $this->responses['ciudad_firma'] ?? 'Veracruz';

        // Nombre de archivo final
        $cleanName = str_replace(' ', '_', $this->project->title);
        $newFileName = 'Generado_' . $cleanName . '.' . $extension;

        if ($extension === 'docx') {
            if (class_exists('\ZipArchive')) {
                $tempFile = storage_path('app/public/temp_' . time() . '.docx');
                copy($originalPath, $tempFile);

                $zip = new \ZipArchive();
                if ($zip->open($tempFile) === true) {
                    if (($index = $zip->locateName('word/document.xml')) !== false) {
                        $xmlContent = $zip->getFromIndex($index);
                        foreach ($replacements as $placeholder => $value) {
                            $xmlContent = str_replace($placeholder, htmlspecialchars($value ?? ''), $xmlContent);
                        }
                        $zip->addFromString('word/document.xml', $xmlContent);
                    }
                    $zip->close();
                    
                    $this->project->update(['status' => 'completed', 'progress' => 100]);
                    $this->dispatch('notify', ['type' => 'success', 'message' => '¡Documento generado con éxito!']);
                    return response()->download($tempFile, $newFileName)->deleteFileAfterSend(true);
                }
            }
            
            // Si ZIP falla o no está, descargar original
            $this->project->update(['status' => 'completed', 'progress' => 100]);
            $this->dispatch('notify', ['type' => 'info', 'message' => 'Descargando plantilla (sin auto-llenado por restricción técnica).']);
            return response()->download($originalPath, $newFileName);
        }

        // Fallback para otros formatos
        $this->project->update(['status' => 'completed', 'progress' => 100]);
        return response()->download($originalPath, $newFileName);
    }

    public function convertToExpediente()
    {
        $this->saveProgress();

        if (!$this->cliente_id) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Debes asignar un cliente al proyecto antes de convertirlo en expediente.']);
            return;
        }

        // Crear el expediente formal
        $expediente = \App\Models\Expediente::create([
            'tenant_id' => $this->project->tenant_id,
            'titulo' => $this->project->title,
            'cliente_id' => $this->cliente_id,
            'materia' => $this->workflow->materia,
            'abogado_responsable_id' => $this->project->user_id,
            'fecha_inicio' => now(),
            'descripcion' => 'Expediente generado desde proyecto: ' . $this->workflow->name,
            'estado_procesal' => 'Inicial',
            'numero' => 'PROY-' . $this->project->id // Número provisional
        ]);

        // Vincular y marcar como convertido
        $this->project->update([
            'expediente_id' => $expediente->id,
            'status' => 'converted',
            'progress' => 100
        ]);

        $this->dispatch('notify', ['type' => 'success', 'message' => '¡Proyecto convertido en expediente formal exitosamente!']);
        
        return redirect()->route('expedientes.show', $expediente);
    }

    public function render()
    {
        $clientes = Cliente::where('tenant_id', auth()->user()->tenant_id)->get();
        $steps = $this->workflow->steps;
        $currentStepData = collect($steps)->firstWhere('id', $this->currentStep);

        return view('livewire.projects.project-wizard', [
            'currentStepData' => $currentStepData,
            'totalSteps' => count($steps),
            'clientes' => $clientes
        ])->layout('layouts.app');
    }
}
