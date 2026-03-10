<?php

namespace App\Livewire\Admin\Roles;

use Livewire\Component;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $showModal = false;
    public $roleId;
    public $name;
    public $selectedPermissions = [];

    protected $rules = [
        'name' => 'required|string|unique:roles,name',
    ];

    public function render()
    {
        return view('livewire.admin.roles.index', [
            'roles' => Role::where('name', '!=', 'super_admin')->paginate(10),
            'permissions' => Permission::all()
        ]);
    }

    public function create()
    {
        $this->reset(['roleId', 'name', 'selectedPermissions']);
        $this->showModal = true;
    }

    public function edit($id)
    {
        $role = Role::findOrFail($id);
        $this->roleId = $role->id;
        $this->name = $role->name;
        $this->selectedPermissions = $role->permissions->pluck('name')->toArray();
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|unique:roles,name,' . $this->roleId,
        ]);

        if ($this->roleId) {
            $role = Role::findOrFail($this->roleId);
            $role->update(['name' => $this->name]);
        } else {
            $role = Role::create(['name' => $this->name]);
        }

        $role->syncPermissions($this->selectedPermissions);

        $this->showModal = false;
        $this->dispatch('notify', 'Rol guardado exitosamente');
    }

    public function delete($id)
    {
        $role = Role::findOrFail($id);
        if ($role->name === 'admin' || $role->name === 'super_admin') {
            $this->dispatch('notify', 'No se pueden eliminar los roles del sistema', 'error');
            return;
        }
        $role->delete();
        $this->dispatch('notify', 'Rol eliminado');
    }
}
