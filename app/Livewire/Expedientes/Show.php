<?php

namespace App\Livewire\Expedientes;

use Livewire\Component;

use App\Models\Expediente;
use App\Models\Actuacion;
use App\Models\Documento;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\On;

class Show extends Component
{
    public Expediente $expediente;
    public $activeTab = 'actuaciones';
    public $showAddActuacion = false;
    public $showAddEvent = false;
    public $selectedDoc = null;
    public $showViewer = false;
    public $showEditModal = false;

    // Edit fields
    public $numero, $titulo, $materia, $juzgado, $estado_procesal, $nombre_juez, $fecha_inicio, $cliente_id, $abogado_responsable_id;

    public function mount(Expediente $expediente)
    {
        $user = auth()->user();
        if ($user->hasRole('abogado') && !$user->can('view all expedientes')) {
            $isAssigned = $expediente->assignedUsers()->where('users.id', $user->id)->exists();
            if ($expediente->abogado_responsable_id !== $user->id && !$isAssigned) {
                abort(403);
            }
        }
        
        $this->expediente = $expediente->load(['cliente', 'abogado', 'actuaciones', 'documentos', 'eventos', 'comentarios.user']);
    }

    #[On('actuacion-added')]
    public function refreshActuaciones()
    {
        $this->expediente->load('actuaciones');
        $this->showAddActuacion = false;
    }

    #[On('document-uploaded')]
    public function refreshDocumentos()
    {
        $this->expediente->load('documentos');
    }

    #[On('event-added')]
    public function refreshEventos()
    {
        $this->expediente->load('eventos');
        $this->showAddEvent = false;
    }

    public function toggleAddActuacion()
    {
        $this->showAddActuacion = !$this->showAddActuacion;
    }

    public function toggleAddEvent()
    {
        $this->showAddEvent = !$this->showAddEvent;
    }

    public function openViewer($docId)
    {
        $this->selectedDoc = Documento::find($docId);
        $this->showViewer = true;
    }

    public function closeViewer()
    {
        $this->showViewer = false;
        $this->selectedDoc = null;
    }

    public function deleteDocument($docId)
    {
        $doc = Documento::find($docId);
        if ($doc) {
            $nombre = $doc->nombre;
            Storage::disk('local')->delete($doc->path);
            $doc->delete();

            AuditLog::create([
                'user_id' => auth()->id(),
                'accion' => 'delete',
                'modulo' => 'documentos',
                'descripcion' => "Eliminó el archivo: {$nombre}",
                'metadatos' => ['documento_id' => $docId],
                'ip_address' => request()->ip(),
            ]);

            $this->expediente->load('documentos');
        }
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function edit()
    {
        $this->numero = $this->expediente->numero;
        $this->titulo = $this->expediente->titulo;
        $this->materia = $this->expediente->materia;
        $this->juzgado = $this->expediente->juzgado;
        $this->estado_procesal = $this->expediente->estado_procesal;
        $this->nombre_juez = $this->expediente->nombre_juez;
        $this->fecha_inicio = $this->expediente->fecha_inicio?->format('Y-m-d');
        $this->cliente_id = $this->expediente->cliente_id;
        $this->abogado_responsable_id = $this->expediente->abogado_responsable_id;
        
        $this->showEditModal = true;
    }

    public function update()
    {
        $this->validate([
            'numero' => 'required|string|max:255',
            'titulo' => 'required|string|max:255',
            'materia' => 'required|string|max:255',
            'juzgado' => 'required|string|max:255',
            'estado_procesal' => 'required|string|max:255',
            'cliente_id' => 'required|exists:clientes,id',
            'abogado_responsable_id' => 'required|exists:users,id',
        ]);

        $this->expediente->update([
            'numero' => $this->numero,
            'titulo' => $this->titulo,
            'materia' => $this->materia,
            'juzgado' => $this->juzgado,
            'estado_procesal' => $this->estado_procesal,
            'nombre_juez' => $this->nombre_juez,
            'fecha_inicio' => $this->fecha_inicio,
            'cliente_id' => $this->cliente_id,
            'abogado_responsable_id' => $this->abogado_responsable_id,
        ]);

        $this->showEditModal = false;
        $this->expediente->refresh();
        $this->dispatch('notify', 'Expediente actualizado exitosamente');
    }

    public function render()
    {
        return view('livewire.expedientes.show', [
            'clientes' => \App\Models\Cliente::all(),
            'abogados' => \App\Models\User::role('abogado')->get(),
        ]);
    }
}
