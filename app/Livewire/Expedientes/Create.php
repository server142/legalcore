<?php

namespace App\Livewire\Expedientes;

use Livewire\Component;
use App\Models\Expediente;
use App\Models\Cliente;
use App\Models\User;
use App\Models\Materia;
use Illuminate\Support\Facades\Hash;

class Create extends Component
{
    public $numero;
    public $titulo;
    public $materia;
    public $juzgado;
    public $nombre_juez;
    public $cliente_id;
    public $abogado_responsable_id;
    public $descripcion;
    public $fecha_inicio;

    // Modals
    public $showMateriaModal = false;
    public $newMateriaNombre;

    public $showClienteModal = false;
    public $newClienteNombre;
    public $newClienteEmail;
    public $newClienteTelefono;

    public $showAbogadoModal = false;
    public $newAbogadoNombre;
    public $newAbogadoEmail;

    protected $rules = [
        'numero' => 'required|unique:expedientes,numero',
        'titulo' => 'required|string|max:255',
        'materia' => 'required|string|max:255',
        'cliente_id' => 'required|exists:clientes,id',
        'abogado_responsable_id' => 'required|exists:users,id',
    ];

    public function save()
    {
        $this->validate();

        Expediente::create([
            'numero' => $this->numero,
            'titulo' => $this->titulo,
            'materia' => $this->materia,
            'juzgado' => $this->juzgado,
            'nombre_juez' => $this->nombre_juez,
            'cliente_id' => $this->cliente_id,
            'abogado_responsable_id' => $this->abogado_responsable_id,
            'descripcion' => $this->descripcion,
            'fecha_inicio' => $this->fecha_inicio,
            'estado_procesal' => 'inicial',
        ]);

        session()->flash('message', 'Expediente creado exitosamente.');

        return redirect()->to('/expedientes');
    }

    public function createMateria()
    {
        $this->validate(['newMateriaNombre' => 'required|string|max:255']);
        
        $materia = Materia::create(['nombre' => $this->newMateriaNombre]);
        $this->materia = $materia->nombre;
        $this->showMateriaModal = false;
        $this->newMateriaNombre = '';
    }

    public function createCliente()
    {
        $this->validate([
            'newClienteNombre' => 'required|string|max:255',
            'newClienteEmail' => 'nullable|email',
        ]);

        $cliente = Cliente::create([
            'nombre' => $this->newClienteNombre,
            'email' => $this->newClienteEmail,
            'telefono' => $this->newClienteTelefono,
        ]);

        $this->cliente_id = $cliente->id;
        $this->showClienteModal = false;
        $this->reset(['newClienteNombre', 'newClienteEmail', 'newClienteTelefono']);
    }

    public function createAbogado()
    {
        $this->validate([
            'newAbogadoNombre' => 'required|string|max:255',
            'newAbogadoEmail' => 'required|email|unique:users,email',
        ]);

        $user = User::create([
            'name' => $this->newAbogadoNombre,
            'email' => $this->newAbogadoEmail,
            'password' => Hash::make('password123'), // Default password
            'role' => 'abogado',
        ]);

        $user->assignRole('abogado');

        $this->abogado_responsable_id = $user->id;
        $this->showAbogadoModal = false;
        $this->reset(['newAbogadoNombre', 'newAbogadoEmail']);
    }

    public function render()
    {
        return view('livewire.expedientes.create', [
            'clientes' => Cliente::all(),
            'abogados' => User::role(['abogado', 'admin'])->get(),
            'materias' => Materia::all(),
        ]);
    }
}
