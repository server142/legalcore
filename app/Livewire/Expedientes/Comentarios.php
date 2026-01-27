<?php

namespace App\Livewire\Expedientes;

use Livewire\Component;
use App\Models\Expediente;
use App\Models\Comentario;
use App\Models\ComentarioReaccion;
use Livewire\Attributes\On;

class Comentarios extends Component
{
    public Expediente $expediente;
    public $nuevoComentario = '';
    public $respondiendo = null;
    public $editando = null;
    public $contenidoEditado = '';

    public function mount(Expediente $expediente)
    {
        $this->expediente = $expediente;
    }

    public function agregarComentario()
    {
        $this->validate([
            'nuevoComentario' => 'required|string|min:1|max:5000',
        ], [
            'nuevoComentario.required' => 'El comentario no puede estar vacío.',
            'nuevoComentario.max' => 'El comentario no puede exceder 5000 caracteres.',
        ]);

        $user = auth()->user();
        $isResponsable = $this->expediente->abogado_responsable_id === $user->id;
        $isAsignado = $this->expediente->assignedUsers()->where('users.id', $user->id)->exists();

        if (!$isResponsable && !$isAsignado && !$user->can('manage users')) {
            $this->dispatch('notify-error', 'No tienes permiso para comentar en este expediente.');
            return;
        }

        Comentario::create([
            'expediente_id' => $this->expediente->id,
            'user_id' => $user->id,
            'tenant_id' => $user->tenant_id,
            'contenido' => trim($this->nuevoComentario),
            'parent_id' => $this->respondiendo,
        ]);

        $this->nuevoComentario = '';
        $this->respondiendo = null;
        $this->expediente->load('comentarios.user', 'comentarios.respuestas.user', 'comentarios.reacciones.user');
    }

    public function responder($comentarioId)
    {
        $this->respondiendo = $comentarioId;
        $this->editando = null;
    }

    public function cancelarRespuesta()
    {
        $this->respondiendo = null;
        $this->nuevoComentario = '';
    }

    public function editar($comentarioId)
    {
        $comentario = Comentario::find($comentarioId);
        
        if (!$comentario || $comentario->user_id !== auth()->id()) {
            return;
        }

        $this->editando = $comentarioId;
        $this->contenidoEditado = $comentario->contenido;
        $this->respondiendo = null;
    }

    public function guardarEdicion()
    {
        $this->validate([
            'contenidoEditado' => 'required|string|min:1|max:5000',
        ]);

        $comentario = Comentario::find($this->editando);
        
        if (!$comentario || $comentario->user_id !== auth()->id()) {
            return;
        }

        $comentario->update(['contenido' => trim($this->contenidoEditado)]);
        
        $this->editando = null;
        $this->contenidoEditado = '';
        $this->expediente->load('comentarios.user', 'comentarios.respuestas.user', 'comentarios.reacciones.user');
    }

    public function cancelarEdicion()
    {
        $this->editando = null;
        $this->contenidoEditado = '';
    }

    public function eliminarComentario($comentarioId)
    {
        $comentario = Comentario::find($comentarioId);

        if (!$comentario) {
            return;
        }

        if ($comentario->user_id !== auth()->id() && !auth()->user()->can('manage users')) {
            $this->dispatch('notify-error', 'No tienes permiso para eliminar este comentario.');
            return;
        }

        $comentario->delete();
        $this->expediente->load('comentarios.user', 'comentarios.respuestas.user', 'comentarios.reacciones.user');
    }

    public function toggleReaccion($comentarioId, $tipo)
    {
        $reaccion = ComentarioReaccion::where('comentario_id', $comentarioId)
            ->where('user_id', auth()->id())
            ->first();

        if ($reaccion) {
            if ($reaccion->tipo === $tipo) {
                // Quitar reacción
                $reaccion->delete();
            } else {
                // Cambiar reacción
                $reaccion->update(['tipo' => $tipo]);
            }
        } else {
            // Agregar reacción
            ComentarioReaccion::create([
                'comentario_id' => $comentarioId,
                'user_id' => auth()->id(),
                'tipo' => $tipo,
            ]);
        }

        $this->expediente->load('comentarios.user', 'comentarios.respuestas.user', 'comentarios.reacciones.user');
    }

    public function render()
    {
        return view('livewire.expedientes.comentarios', [
            'comentarios' => $this->expediente->comentarios()
                ->whereNull('parent_id')
                ->with(['user', 'respuestas.user', 'respuestas.reacciones.user', 'reacciones.user'])
                ->get(),
        ]);
    }
}
