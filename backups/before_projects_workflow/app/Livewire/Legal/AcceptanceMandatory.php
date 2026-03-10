<?php

namespace App\Livewire\Legal;

use App\Models\LegalDocument;
use App\Models\LegalAcceptance;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.legal-acceptance')]
class AcceptanceMandatory extends Component
{
    public $pendingDocs = [];
    public $accepted = [];

    public function mount()
    {
        $this->loadPendingDocs();
        
        if (empty($this->pendingDocs)) {
            return redirect()->route('dashboard');
        }
    }

    public function loadPendingDocs()
    {
        $requiredDocs = LegalDocument::where('activo', true)
            ->where('requiere_aceptacion', true)
            ->forTenant(auth()->user()->tenant_id)
            ->get();

        $this->pendingDocs = [];

        foreach ($requiredDocs as $doc) {
            // Regla 1: Plantillas de contratos de servicios JAMÁS se aceptan en el sistema
            if ($doc->tipo === 'CONTRATO_SERVICIOS') {
                continue;
            }

            // Regla 2: El Contrato SaaS (pagos) solo lo debe aceptar el Admin/SuperAdmin
            if ($doc->tipo === 'CONTRATO_SAAS' && !auth()->user()->hasRole(['super_admin', 'admin'])) {
                continue;
            }

            $alreadyAccepted = LegalAcceptance::where('user_id', auth()->id())
                ->where('legal_document_id', $doc->id)
                ->where('version', $doc->version)
                ->exists();

            if (!$alreadyAccepted) {
                // Si es el primero que encontramos (solo mostramos uno a la vez para no saturar)
                // OJO: El usuario pidió "muestras el primero, lo aceptas y se desliza al siguiente".
                // Para simplificar: Solo cargamos el PRIMER pendiente en el array.
                // Así la UI solo renderizará uno. Al aceptar, recargará y mostrará el siguiente.
                
                $this->pendingDocs[] = $doc;
                $this->accepted[$doc->id] = false;
                
                // Break para solo mostrar UNO a la vez
                break; 
            }
        }
    }

    public function accept()
    {
        // 1. Identificar qué documento estamos aceptando (siempre es el único en pendingDocs)
        if (empty($this->pendingDocs)) {
            return redirect()->route('dashboard');
        }
        
        $doc = $this->pendingDocs[0]; // El documento actual en pantalla

        // 2. Validar checkbox
        if (!($this->accepted[$doc->id] ?? false)) {
            $this->addError('accepted.'.$doc->id, 'Debes leer y aceptar este documento para continuar.');
            return;
        }

        // 3. Guardar aceptación
        LegalAcceptance::create([
            'user_id' => auth()->id(),
            'legal_document_id' => $doc->id,
            'version' => $doc->version,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'fecha_aceptacion' => now(),
        ]);

        // 4. Limpiar input y mensaje de error
        $this->reset('accepted'); 
        $this->resetErrorBag();

        // 5. Recargar la lista de pendientes
        $this->loadPendingDocs();

        // 6. Decidir si redirigir o mostrar el siguiente
        if (empty($this->pendingDocs)) {
            session()->flash('notify', '¡Documentos aceptados correctamente!');
            return redirect()->route('dashboard');
        }
        
        // Si aún hay pendientes, el render() automático de Livewire mostrará el siguiente (el nuevo índice 0)
        session()->flash('success_step', 'Documento aceptado. Por favor revisa el siguiente:');
    }

    public function render()
    {
        return view('livewire.legal.acceptance-mandatory');
    }
}
