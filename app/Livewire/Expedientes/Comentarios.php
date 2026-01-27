<?php

namespace App\Livewire\Expedientes;

use Livewire\Component;
use App\Models\Expediente;
use App\Models\Comentario;
use Livewire\Attributes\On;

class Comentarios extends Component
{
    public Expediente $expediente;
    public $nuevoComentario = '';

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

        // Verificar que el usuario esté asignado al expediente
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
        ]);

        $this->nuevoComentario = '';
        $this->expediente->load('comentarios.user');
        $this->dispatch('comentario-agregado');
    }

    public function eliminarComentario($comentarioId)
    {
        $comentario = Comentario::find($comentarioId);

        if (!$comentario) {
            return;
        }

        // Solo el autor o un admin puede eliminar
        if ($comentario->user_id !== auth()->id() && !auth()->user()->can('manage users')) {
            $this->dispatch('notify-error', 'No tienes permiso para eliminar este comentario.');
            return;
        }

        $comentario->delete();
        $this->expediente->load('comentarios.user');
        $this->dispatch('comentario-eliminado');
    }

    public function render()
    {
        return view('livewire.expedientes.comentarios', [
            'comentarios' => $this->expediente->comentarios()->with('user')->get(),
        ]);
    }
}
