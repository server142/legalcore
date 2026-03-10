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

    public $replyContent = '';

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

        $comentario = Comentario::create([
            'expediente_id' => $this->expediente->id,
            'user_id' => $user->id,
            'tenant_id' => $user->tenant_id,
            'contenido' => trim($this->nuevoComentario),
            'parent_id' => null,
        ]);

        $this->notificarParticipantes($comentario);

        $this->nuevoComentario = '';
        $this->expediente->load('comentarios.user', 'comentarios.respuestas.user', 'comentarios.reacciones.user');
    }

    protected function notificarParticipantes(Comentario $comentario)
    {
        $expediente = $comentario->expediente;
        $authorId = $comentario->user_id;

        // Collect all participants (responsible + assigned)
        $participants = collect();
        
        if ($expediente->abogado_responsable_id && $expediente->abogado_responsable_id != $authorId) {
            $participants->push(\App\Models\User::find($expediente->abogado_responsable_id));
        }

        foreach ($expediente->assignedUsers as $u) {
            if ($u->id != $authorId) {
                $participants->push($u);
            }
        }

        // Unique to avoid duplicate emails to the same user
        $participants = $participants->unique('id')->filter();

        foreach ($participants as $p) {
            \Illuminate\Support\Facades\Mail::to($p->email)->queue(new \App\Mail\CommentPosted($comentario, $p));
        }
    }

    public function responder($comentarioId)
    {
        $this->respondiendo = $comentarioId;
        $this->editando = null;
        $this->replyContent = '';
    }

    public function cancelarRespuesta()
    {
        $this->respondiendo = null;
        $this->replyContent = '';
    }

    public function publicarRespuesta()
    {
        $this->validate([
            'replyContent' => 'required|string|min:1|max:5000',
        ], [
            'replyContent.required' => 'La respuesta no puede estar vacía.',
        ]);

        $user = auth()->user();
        
        // Determine parent ID (flatten to max 1 level of nesting)
        $targetComment = Comentario::find($this->respondiendo);
        
        if (!$targetComment) {
            $this->dispatch('notify-error', 'El comentario al que intentas responder ya no existe.');
            $this->cancelarRespuesta();
            return;
        }

        $parentId = $targetComment->parent_id ?? $targetComment->id;
        
        // If replying to a reply, maybe prepend mention?
        $content = trim($this->replyContent);
        if ($targetComment->parent_id) {
            $content = '@' . $targetComment->user->name . ' ' . $content;
        }

        $comentario = Comentario::create([
            'expediente_id' => $this->expediente->id,
            'user_id' => $user->id,
            'tenant_id' => $user->tenant_id,
            'contenido' => $content,
            'parent_id' => $parentId,
        ]);

        $this->notificarParticipantes($comentario);

        $this->replyContent = '';
        $this->respondiendo = null;
        $this->expediente->load('comentarios.user', 'comentarios.respuestas.user', 'comentarios.reacciones.user');
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
