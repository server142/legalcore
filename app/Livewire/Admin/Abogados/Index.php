<?php

namespace App\Livewire\Admin\Abogados;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Hash;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;
    public $editMode = false;
    public $abogadoId;

    public $name;
    public $email;
    public $password;
    public $password_confirmation;

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|confirmed|min:8',
    ];

    public function render()
    {
        $abogados = User::where('tenant_id', auth()->user()->tenant_id)
            ->role('abogado')
            ->where(function($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->paginate(10);

        return view('livewire.admin.abogados.index', [
            'abogados' => $abogados,
        ]);
    }

    public function create()
    {
        $this->reset(['name', 'email', 'password', 'password_confirmation', 'abogadoId', 'editMode']);
        $this->showModal = true;
    }

    public function edit($id)
    {
        $this->editMode = true;
        $this->abogadoId = $id;
        $abogado = User::findOrFail($id);
        $this->name = $abogado->name;
        $this->email = $abogado->email;
        $this->showModal = true;
    }

    public function store()
    {
        $this->validate();

        $abogado = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'tenant_id' => auth()->user()->tenant_id,
            'role' => 'abogado',
        ]);

        $abogado->assignRole('abogado');

        $this->showModal = false;
        $this->dispatch('notify', 'Abogado creado exitosamente');
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $this->abogadoId,
        ]);

        $abogado = User::findOrFail($this->abogadoId);
        
        $abogado->update([
            'name' => $this->name,
            'email' => $this->email,
        ]);

        if (!empty($this->password)) {
            $this->validate([
                'password' => 'confirmed|min:8',
            ]);
            $abogado->update(['password' => Hash::make($this->password)]);
        }

        $this->showModal = false;
        $this->dispatch('notify', 'Abogado actualizado exitosamente');
    }

    public $confirmingDeletion = false;
    public $itemToDeleteId;

    public function confirmDelete($id)
    {
        $this->itemToDeleteId = $id;
        $this->confirmingDeletion = true;
    }

    public function delete()
    {
        $abogado = User::findOrFail($this->itemToDeleteId);
        $abogado->delete();
        $this->confirmingDeletion = false;
        $this->dispatch('notify', 'Abogado eliminado exitosamente');
        $this->reset(['itemToDeleteId']);
    }
}
