<?php

namespace App\Livewire\Admin\EstadosProcesales;

use App\Models\EstadoProcesal;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;
    public $editMode = false;
    public $estadoProcesalId;

    public $nombre;
    public $descripcion;

    public $confirmingDeletion = false;
    public $itemToDeleteId;

    protected $rules = [
        'nombre' => 'required|string|max:255',
        'descripcion' => 'nullable|string',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    protected function assertCanWrite(): void
    {
        if (!auth()->user()->hasRole('super_admin')) {
            abort(403);
        }
    }

    public function create()
    {
        $this->assertCanWrite();

        $this->reset(['nombre', 'descripcion', 'estadoProcesalId', 'editMode']);
        $this->showModal = true;
    }

    public function edit($id)
    {
        $this->assertCanWrite();

        $this->editMode = true;
        $this->estadoProcesalId = $id;
        $estado = EstadoProcesal::findOrFail($id);
        $this->nombre = $estado->nombre;
        $this->descripcion = $estado->descripcion;
        $this->showModal = true;
    }

    public function store()
    {
        $this->assertCanWrite();

        $this->validate();

        EstadoProcesal::create([
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
        ]);

        $this->showModal = false;
        $this->dispatch('notify', 'Estado procesal creado exitosamente');
    }

    public function update()
    {
        $this->assertCanWrite();

        $this->validate();

        $estado = EstadoProcesal::findOrFail($this->estadoProcesalId);
        $estado->update([
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
        ]);

        $this->showModal = false;
        $this->dispatch('notify', 'Estado procesal actualizado exitosamente');
    }

    public function confirmDelete($id)
    {
        $this->assertCanWrite();

        $this->itemToDeleteId = $id;
        $this->confirmingDeletion = true;
    }

    public function delete()
    {
        $this->assertCanWrite();

        $estado = EstadoProcesal::findOrFail($this->itemToDeleteId);
        $estado->delete();
        $this->confirmingDeletion = false;
        $this->dispatch('notify', 'Estado procesal eliminado exitosamente');
        $this->reset(['itemToDeleteId']);
    }

    public function render()
    {
        $estados = EstadoProcesal::query()
            ->where('nombre', 'like', '%' . $this->search . '%')
            ->orderBy('nombre')
            ->paginate(10);

        return view('livewire.admin.estados-procesales.index', [
            'estados' => $estados,
            'canWrite' => auth()->user()->hasRole('super_admin'),
        ]);
    }
}
