<?php

namespace App\Livewire\Admin\LegalDocuments;

use App\Models\LegalDocument;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function delete($id)
    {
        $document = LegalDocument::findOrFail($id);
        $document->delete();
        session()->flash('info', 'Documento eliminado correctamente.');
    }

    public function toggleStatus($id)
    {
        $document = LegalDocument::findOrFail($id);
        $document->activo = !$document->activo;
        $document->save();
    }

    public function render()
    {
        $query = LegalDocument::query();

        if (auth()->user()->hasRole('super_admin')) {
            // Super Admin only manages GLOBAL documents (Platform Terms, Privacy, etc.)
            // They should not see thousands of tenant-specific contracts here.
            $query->whereNull('tenant_id');
        } else {
            // Tenant Admin only manages THEIR OWN documents.
            // Exclude global docs (they can't edit them anyway).
            $query->where('tenant_id', auth()->user()->tenant_id);
        }

        $documents = $query->where('nombre', 'like', '%' . $this->search . '%')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.admin.legal-documents.index', [
            'documents' => $documents
        ]);
    }
}
