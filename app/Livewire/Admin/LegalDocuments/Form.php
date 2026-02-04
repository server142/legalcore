<?php

namespace App\Livewire\Admin\LegalDocuments;

use App\Models\LegalDocument;
use Livewire\Component;

class Form extends Component
{
    public $legalDocumentId;
    public $nombre;
    public $tipo = 'OTRO';
    public $texto;
    public $version = '1.0';
    public $activo = true;
    public $fecha_publicacion;
    public $requiere_aceptacion = true;
    public $visible_en = [];

    protected $rules = [
        'nombre' => 'required|string|max:255',
        'tipo' => 'required|string',
        'texto' => 'required|string',
        'version' => 'required|string',
        'activo' => 'boolean',
        'fecha_publicacion' => 'nullable|date',
        'requiere_aceptacion' => 'boolean',
        'visible_en' => 'array',
    ];

    public function mount($legalDocument = null)
    {
        if ($legalDocument) {
            $doc = LegalDocument::findOrFail($legalDocument);
            
            // SECURITY: Prevent editing Global documents if not Super Admin
            if (is_null($doc->tenant_id) && !auth()->user()->hasRole('super_admin')) {
                abort(403, 'No tienes permiso para editar documentos globales de la plataforma.');
            }
            
            // SECURITY: Prevent editing other tenant's documents
            if ($doc->tenant_id && $doc->tenant_id != auth()->user()->tenant_id && !auth()->user()->hasRole('super_admin')) {
                 abort(403, 'No tienes permiso para editar este documento.');
            }

            $this->legalDocumentId = $doc->id;
            $this->nombre = $doc->nombre;
            $this->tipo = $doc->tipo;
            $this->texto = $doc->texto;
            $this->version = $doc->version;
            $this->activo = $doc->activo;
            $this->fecha_publicacion = $doc->fecha_publicacion?->format('Y-m-d\TH:i');
            $this->requiere_aceptacion = $doc->requiere_aceptacion;
            $this->visible_en = $doc->visible_en ?? [];
        } else {
            $this->fecha_publicacion = now()->format('Y-m-d\TH:i');
        }
    }

    public function save()
    {
        $this->validate();

        $data = [
            'nombre' => $this->nombre,
            'tipo' => $this->tipo,
            'texto' => $this->texto,
            'version' => $this->version,
            'activo' => $this->activo,
            'fecha_publicacion' => $this->fecha_publicacion,
            'requiere_aceptacion' => $this->requiere_aceptacion,
            // SECURITY: Non-super admins cannot control global visibility places (login, register, etc.)
            'visible_en' => auth()->user()->hasRole('super_admin') ? $this->visible_en : [],
        ];

        if ($this->legalDocumentId) {
            $doc = LegalDocument::findOrFail($this->legalDocumentId);
            $doc->update($data);
            $msg = 'Documento actualizado correctamente.';
        } else {
            // Assign tenant_id if not super_admin
            if (!auth()->user()->hasRole('super_admin')) {
                $data['tenant_id'] = auth()->user()->tenant_id;
            }
            
            LegalDocument::create($data);
            $msg = 'Documento creado correctamente.';
        }

        session()->flash('info', $msg);
        return redirect()->route('admin.legal-documents.index');
    }

    public function render()
    {
        return view('livewire.admin.legal-documents.form');
    }
}
