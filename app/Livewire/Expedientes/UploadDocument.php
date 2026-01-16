<?php

namespace App\Livewire\Expedientes;

use Livewire\Component;

use App\Models\Documento;
use App\Models\Expediente;
use App\Models\AuditLog;
use Livewire\WithFileUploads;

class UploadDocument extends Component
{
    use WithFileUploads;

    public Expediente $expediente;
    public $files = [];

    public function mount(Expediente $expediente)
    {
        $this->expediente = $expediente;
    }

    protected $rules = [
        'files.*' => 'required|file', // Sin límite de peso por archivo
    ];

    public function save()
    {
        $this->validate();

        foreach ($this->files as $file) {
            $extension = strtolower($file->getClientOriginalExtension());
            $nombreOriginal = $file->getClientOriginalName();
            
            // Clasificación por tipo
            $tipo = 'other';
            if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'svg'])) $tipo = 'image';
            elseif ($extension === 'pdf') $tipo = 'pdf';
            elseif (in_array($extension, ['doc', 'docx'])) $tipo = 'word';
            elseif (in_array($extension, ['xls', 'xlsx'])) $tipo = 'excel';
            elseif (in_array($extension, ['mp4', 'webm', 'ogg', 'mov'])) $tipo = 'video';
            elseif (in_array($extension, ['mp3', 'wav', 'ogg'])) $tipo = 'audio';

            $path = $file->store('documentos/' . $this->expediente->id, 'local');

            Documento::create([
                'expediente_id' => $this->expediente->id,
                'nombre' => $nombreOriginal,
                'path' => $path,
                'extension' => $extension,
                'tipo' => $tipo,
                'version' => 1,
                'uploaded_by' => auth()->id(),
            ]);

            AuditLog::create([
                'user_id' => auth()->id(),
                'accion' => 'upload',
                'modulo' => 'documentos',
                'descripcion' => "Subió el archivo: {$nombreOriginal}",
                'metadatos' => ['expediente_id' => $this->expediente->id, 'extension' => $extension],
                'ip_address' => request()->ip(),
            ]);
        }

        $this->dispatch('document-uploaded');
        $this->reset(['files']);
    }

    public function render()
    {
        return view('livewire.expedientes.upload-document');
    }
}
