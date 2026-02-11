<?php

namespace App\Livewire\Admin\Users;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class Index extends Component
{

    use WithPagination;
    use \App\Traits\Auditable;

    public $search = '';
    public $showModal = false;
    public $editMode = false;
    public $userId;

    // Form fields
    public $name;
    public $email;
    public $password;
    public $password_confirmation;
    public $selectedRoles = [];

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|confirmed|min:8',
        'selectedRoles' => 'required|array|min:1',
    ];

    public function create()
    {
        $this->reset(['name', 'email', 'password', 'password_confirmation', 'selectedRoles', 'userId']);
        $this->editMode = false;
        $this->showModal = true;
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);

        if (!auth()->user()->hasRole('super_admin') && $user->tenant_id !== auth()->user()->tenant_id) {
            abort(403, 'No tienes permiso para editar este usuario.');
        }

        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->selectedRoles = $user->roles->pluck('name')->toArray();
        // Password fields remain empty
        $this->password = '';
        $this->password_confirmation = '';
        
        $this->editMode = true;
        $this->showModal = true;
    }

    public function store()
    {
        $this->validate();

        // Security Check: Prevent assigning super_admin if not super_admin
        if (in_array('super_admin', $this->selectedRoles) && !auth()->user()->hasRole('super_admin')) {
            abort(403, 'No tienes permiso para asignar el rol de Super Admin.');
        }

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'tenant_id' => auth()->user()->tenant_id,
            'role' => $this->selectedRoles[0] ?? 'abogado',
        ]);

        $user->syncRoles($this->selectedRoles);

        // Audit Log
        $this->logAudit('crear', 'Usuarios', "Creó al usuario: {$this->name} ({$this->email})", [
            'new_user_id' => $user->id,
            'roles' => $this->selectedRoles
        ]);

        // Enviar correo con los datos de acceso
        try {
            \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\UserCreatedMail($user, $this->password));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error enviando correo de bienvenida: ' . $e->getMessage());
        }

        $this->showModal = false;
        $this->dispatch('notify', 'Usuario creado exitosamente');
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $this->userId,
            'selectedRoles' => 'required|array|min:1',
        ]);

        // Security Check: Prevent assigning super_admin if not super_admin
        if (in_array('super_admin', $this->selectedRoles) && !auth()->user()->hasRole('super_admin')) {
            abort(403, 'No tienes permiso para asignar el rol de Super Admin.');
        }

        $user = User::findOrFail($this->userId);

        if (!auth()->user()->hasRole('super_admin') && $user->tenant_id !== auth()->user()->tenant_id) {
            abort(403, 'No tienes permiso para actualizar este usuario.');
        }

        $oldData = $user->only(['name', 'email', 'role']);

        $user->update([
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->selectedRoles[0] ?? $user->role,
        ]);

        if (!empty($this->password)) {
            $this->validate([
                'password' => 'confirmed|min:8',
            ]);
            $user->update(['password' => Hash::make($this->password)]);
            // Log variable to note password change
            $passChanged = true;
        }

        $user->syncRoles($this->selectedRoles);

        // Audit Log
        $this->logAudit('editar', 'Usuarios', "Editó al usuario: {$user->name}", [
            'user_id' => $user->id,
            'changes' => $user->getChanges(), // Eloquent changes
            'roles_assigned' => $this->selectedRoles,
            'password_changed' => $passChanged ?? false
        ]);

        $this->showModal = false;
        $this->dispatch('notify', 'Usuario actualizado exitosamente');
    }

    public $confirmingUserDeletion = false;
    public $userToDeleteId;

    public function confirmDelete($id)
    {
        $this->userToDeleteId = $id;
        $this->confirmingUserDeletion = true;
    }

    public function deleteUser()
    {
        $user = User::findOrFail($this->userToDeleteId);

        if (!auth()->user()->hasRole('super_admin') && $user->tenant_id !== auth()->user()->tenant_id) {
            abort(403, 'No tienes permiso para eliminar este usuario.');
        }

        $userName = $user->name;
        $userEmail = $user->email;
        
        $user->delete();

        // Audit Log
        $this->logAudit('eliminar', 'Usuarios', "Eliminó al usuario: {$userName} ({$userEmail})", [
            'deleted_user_id' => $this->userToDeleteId
        ]);

        $this->confirmingUserDeletion = false;
        $this->dispatch('notify', 'Usuario eliminado exitosamente');
        $this->reset(['userToDeleteId']);
    }

    public function resendInvitation($id)
    {
        $user = User::findOrFail($id);

        if (!auth()->user()->hasRole('super_admin') && $user->tenant_id !== auth()->user()->tenant_id) {
            abort(403, 'No tienes permiso para realizar esta acción.');
        }
        
        // Generamos una contraseña temporal nueva para el reenvío
        $newPassword = \Illuminate\Support\Str::random(10);
        $user->update(['password' => Hash::make($newPassword)]);

        try {
            \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\LawyerInvitationMail($user, $newPassword));
            $this->dispatch('notify', 'Invitación reenviada correctamente con nueva contraseña temporal.');
        } catch (\Exception $e) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Error al reenviar: ' . $e->getMessage()]);
        }
    }

    public function render()
    {
        $query = User::query();

        // Filter by tenant restriction if not super admin
        if (!auth()->user()->hasRole('super_admin')) {
            $query->where('tenant_id', auth()->user()->tenant_id);
        }

        $users = $query->where(function($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                  ->orWhere('email', 'like', '%'.$this->search.'%');
            })
            ->paginate(10);

        return view('livewire.admin.users.index', [
            'users' => $users,
            'roles' => Role::all(),
        ]);
    }
}
