<?php

namespace App\Livewire\Admin\Juzgados;

use App\Models\Juzgado;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;
    public $editMode = false;
    public $juzgadoId;

    public $nombre;
    public $direccion;
    public $telefono;

    protected $rules = [
        'nombre' => 'required|string|max:255',
        'direccion' => 'nullable|string|max:255',
        'telefono' => 'nullable|string|max:255',
    ];

    public function render()
    {
        $juzgados = Juzgado::where('tenant_id', auth()->user()->tenant_id)
            ->where('nombre', 'like', '%' . $this->search . '%')
            ->paginate(10);

        return view('livewire.admin.juzgados.index', [
            'juzgados' => $juzgados,
        ]);
    }

    public function create()
    {
        $this->reset(['nombre', 'direccion', 'telefono', 'juzgadoId', 'editMode']);
        $this->showModal = true;
    }

    public function edit($id)
    {
        $this->editMode = true;
        $this->juzgadoId = $id;
        $juzgado = Juzgado::findOrFail($id);
        $this->nombre = $juzgado->nombre;
        $this->direccion = $juzgado->direccion;
        $this->telefono = $juzgado->telefono;
        $this->showModal = true;
    }

    public function store()
    {
        $this->validate();

        Juzgado::create([
            'nombre' => $this->nombre,
            'direccion' => $this->direccion,
            'telefono' => $this->telefono,
            'tenant_id' => auth()->user()->tenant_id,
        ]);

        $this->showModal = false;
        $this->dispatch('notify', 'Juzgado creado exitosamente');
    }

    public function update()
    {
        $this->validate();

        $juzgado = Juzgado::findOrFail($this->juzgadoId);
        $juzgado->update([
            'nombre' => $this->nombre,
            'direccion' => $this->direccion,
            'telefono' => $this->telefono,
        ]);

        $this->showModal = false;
        $this->dispatch('notify', 'Juzgado actualizado exitosamente');
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
        $juzgado = Juzgado::findOrFail($this->itemToDeleteId);
        $juzgado->delete();
        $this->confirmingDeletion = false;
        $this->dispatch('notify', 'Juzgado eliminado exitosamente');
        $this->reset(['itemToDeleteId']);
    }
}
