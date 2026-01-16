<?php

namespace App\Livewire\Admin\Materias;

use App\Models\Materia;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;
    public $editMode = false;
    public $materiaId;

    public $nombre;

    protected $rules = [
        'nombre' => 'required|string|max:255',
    ];

    public function render()
    {
        $materias = Materia::where('tenant_id', auth()->user()->tenant_id)
            ->where('nombre', 'like', '%' . $this->search . '%')
            ->paginate(10);

        return view('livewire.admin.materias.index', [
            'materias' => $materias,
        ]);
    }

    public function create()
    {
        $this->reset(['nombre', 'materiaId', 'editMode']);
        $this->showModal = true;
    }

    public function edit($id)
    {
        $this->editMode = true;
        $this->materiaId = $id;
        $materia = Materia::findOrFail($id);
        $this->nombre = $materia->nombre;
        $this->showModal = true;
    }

    public function store()
    {
        $this->validate();

        Materia::create([
            'nombre' => $this->nombre,
            'tenant_id' => auth()->user()->tenant_id,
        ]);

        $this->showModal = false;
        $this->dispatch('notify', 'Materia creada exitosamente');
    }

    public function update()
    {
        $this->validate();

        $materia = Materia::findOrFail($this->materiaId);
        $materia->update([
            'nombre' => $this->nombre,
        ]);

        $this->showModal = false;
        $this->dispatch('notify', 'Materia actualizada exitosamente');
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
        $materia = Materia::findOrFail($this->itemToDeleteId);
        $materia->delete();
        $this->confirmingDeletion = false;
        $this->dispatch('notify', 'Materia eliminada exitosamente');
        $this->reset(['itemToDeleteId']);
    }
}
