<?php

namespace App\Livewire\Expedientes;

use Livewire\Component;
use App\Models\Expediente;
use App\Models\User;
use App\Models\AuditLog;

class ManageAssignments extends Component
{
    public Expediente $expediente;
    public $selectedUsers = [];
    public $newResponsible = null;

    public function mount(Expediente $expediente)
    {
        $this->expediente = $expediente->load(['assignedUsers', 'abogado']);
        $this->selectedUsers = $expediente->assignedUsers->pluck('id')->toArray();
        $this->newResponsible = $expediente->abogado_responsable_id;
    }

    public function updateAssignments()
    {
        $oldUsers = $this->expediente->assignedUsers->pluck('id')->toArray();
        
        $this->expediente->assignedUsers()->sync($this->selectedUsers);

        // Notify NEWLY assigned users
        $newlyAssignedIds = array_diff($this->selectedUsers, $oldUsers);
        foreach ($newlyAssignedIds as $userId) {
            $user = User::find($userId);
            if ($user) {
                \Illuminate\Support\Facades\Mail::to($user->email)->queue(new \App\Mail\ExpedienteAssigned($this->expediente, $user));
            }
        }

        AuditLog::create([
            'user_id' => auth()->id(),
            'accion' => 'update_assignments',
            'modulo' => 'expedientes',
            'descripcion' => 'Actualizó las asignaciones del expediente ' . $this->expediente->numero,
            'metadatos' => [
                'expediente_id' => $this->expediente->id,
                'assigned_users' => $this->selectedUsers
            ],
            'ip_address' => request()->ip(),
        ]);

        $this->dispatch('notify', 'Asignaciones actualizadas exitosamente');
        $this->dispatch('assignments-updated');
    }

    public function changeResponsible()
    {
        $oldResponsible = $this->expediente->abogado_responsable_id;
        
        $this->expediente->update([
            'abogado_responsable_id' => $this->newResponsible
        ]);

        // Notify NEW responsible if changed
        if ($oldResponsible != $this->newResponsible) {
            $user = User::find($this->newResponsible);
            if ($user) {
                \Illuminate\Support\Facades\Mail::to($user->email)->queue(new \App\Mail\ExpedienteAssigned($this->expediente, $user, true));
            }
        }

        AuditLog::create([
            'user_id' => auth()->id(),
            'accion' => 'change_responsible',
            'modulo' => 'expedientes',
            'descripcion' => 'Cambió el abogado responsable del expediente ' . $this->expediente->numero,
            'metadatos' => [
                'expediente_id' => $this->expediente->id,
                'old_responsible' => $oldResponsible,
                'new_responsible' => $this->newResponsible
            ],
            'ip_address' => request()->ip(),
        ]);

        $this->dispatch('notify', 'Abogado responsable actualizado exitosamente');
        $this->dispatch('responsible-changed');
        $this->expediente->refresh();
    }

    public function render()
    {
        $abogados = User::role('abogado')
            ->where('tenant_id', auth()->user()->tenant_id)
            ->get();

        return view('livewire.expedientes.manage-assignments', [
            'abogados' => $abogados
        ]);
    }
}
