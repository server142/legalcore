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

    public function render()
    {
        $users = User::with('roles')
            ->where('tenant_id', auth()->user()->tenant_id)
            ->where(function($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->paginate(10);

        $roles = Role::all();

        return view('livewire.admin.users.index', [
            'users' => $users,
            'roles' => $roles,
        ]);
    }



    public function create()
    {
        $this->reset(['name', 'email', 'password', 'password_confirmation', 'selectedRoles', 'userId', 'editMode']);
        $this->showModal = true;
    }

    public function edit($id)
    {
        $this->editMode = true;
        $this->userId = $id;
        $user = User::findOrFail($id);
        
        $this->name = $user->name;
        $this->email = $user->email;
        $this->selectedRoles = $user->roles->pluck('name')->toArray();
        
        $this->showModal = true;
    }

    public function store()
    {
        $this->validate();

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'tenant_id' => auth()->user()->tenant_id,
            'role' => $this->selectedRoles[0] ?? 'abogado',
        ]);

        $user->syncRoles($this->selectedRoles);

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

        $user = User::findOrFail($this->userId);
        
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
        }

        $user->syncRoles($this->selectedRoles);

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
        $user->delete();
        $this->confirmingUserDeletion = false;
        $this->dispatch('notify', 'Usuario eliminado exitosamente');
        $this->reset(['userToDeleteId']);
    }
}
