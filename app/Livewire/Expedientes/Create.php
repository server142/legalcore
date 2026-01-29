<?php

namespace App\Livewire\Expedientes;

use Livewire\Component;
use App\Models\Expediente;
use App\Models\Cliente;
use App\Models\User;
use App\Models\Materia;
use App\Models\Juzgado;
use App\Models\EstadoProcesal;
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
    public $estado_procesal_id;
    public $descripcion;
    public $fecha_inicio;
    
    // Campos de pago
    public $costo_total;
    public $anticipo;
    public $metodo_pago_anticipo;
    public $referencia_anticipo;

    // Modals
    public $showMateriaModal = false;
    public $newMateriaNombre;

    public $showJuzgadoModal = false;
    public $newJuzgadoNombre;
    public $newJuzgadoDireccion;
    public $newJuzgadoTelefono;

    public $showClienteModal = false;
    public $newClienteNombre;
    public $newClienteEmail;
    public $newClienteTelefono;

    public $showAbogadoModal = false;
    public $newAbogadoNombre;
    public $newAbogadoEmail;

    public function mount()
    {
        $user = auth()->user();

        $defaultEstado = EstadoProcesal::where('nombre', 'Radicación/Inicio')->first();
        if ($defaultEstado) {
            $this->estado_procesal_id = $defaultEstado->id;
        }

        if (($user && $user->hasRole('abogado') && !$user->can('view all expedientes')) || ($user && $user->role === 'super_admin')) {
            $this->abogado_responsable_id = $user->id;
        }
    }

    protected $rules = [
        'numero' => 'required|unique:expedientes,numero',
        'titulo' => 'required|string|max:255',
        'materia' => 'required|string|max:255',
        'juzgado' => 'nullable|string|max:255',
        'cliente_id' => 'required|exists:clientes,id',
        'abogado_responsable_id' => 'required|exists:users,id',
        'estado_procesal_id' => 'nullable|exists:estados_procesales,id',
        'costo_total' => 'nullable|numeric|min:0',
        'anticipo' => 'nullable|numeric|min:0',
        'metodo_pago_anticipo' => 'required_if:anticipo,>,0|string',
    ];

    public function save()
    {
        $this->validate();

        $user = auth()->user();

        if ($user->role === 'super_admin') {
            $this->abogado_responsable_id = $user->id;
        } else {
            $selected = User::find($this->abogado_responsable_id);
            if ($selected && $selected->tenant_id !== $user->tenant_id) {
                abort(403);
            }
        }

        if ($user && $user->hasRole('abogado') && !$user->can('view all expedientes')) {
            $this->abogado_responsable_id = $user->id;
        }

        // Check expediente limit
        $tenant = auth()->user()->tenant;
        
        if ($tenant && $tenant->plan_id) {
            $plan = \App\Models\Plan::find($tenant->plan_id);
            
            if ($plan && !$plan->canAddExpediente($tenant)) {
                $limit = $plan->max_expedientes;
                session()->flash('error', "Has alcanzado el límite de {$limit} expedientes de tu plan. Actualiza tu suscripción para crear más.");
                return;
            }
        }

        $estado = $this->estado_procesal_id ? EstadoProcesal::find($this->estado_procesal_id) : null;

        // Calcular saldo pendiente y estado de cobro
        $saldo_pendiente = $this->costo_total - $this->anticipo;
        $estado_cobro = 'pendiente';
        
        if ($this->anticipo > 0 && $saldo_pendiente > 0) {
            $estado_cobro = 'parcial_pagado';
        } elseif ($saldo_pendiente <= 0) {
            $estado_cobro = 'liquidado';
            $saldo_pendiente = 0;
        }

        $expediente = Expediente::create([
            'numero' => $this->numero,
            'titulo' => $this->titulo,
            'materia' => $this->materia,
            'juzgado' => $this->juzgado,
            'nombre_juez' => $this->nombre_juez,
            'cliente_id' => $this->cliente_id,
            'abogado_responsable_id' => $this->abogado_responsable_id,
            'descripcion' => $this->descripcion,
            'fecha_inicio' => $this->fecha_inicio,
            'estado_procesal' => $estado?->nombre ?? 'inicial',
            'estado_procesal_id' => $this->estado_procesal_id,
            'costo_total' => $this->costo_total,
            'anticipo' => $this->anticipo,
            'saldo_pendiente' => $saldo_pendiente,
            'estado_cobro' => $estado_cobro,
        ]);

        // Crear pago de anticipo si existe
        if ($this->anticipo > 0) {
            ExpedientePago::create([
                'tenant_id' => auth()->user()->tenant_id,
                'expediente_id' => $expediente->id,
                'monto' => $this->anticipo,
                'tipo_pago' => 'anticipo',
                'fecha_pago' => now(),
                'metodo_pago' => $this->metodo_pago_anticipo,
                'referencia' => $this->referencia_anticipo,
                'notas' => 'Anticipo del expediente',
            ]);
        }

        session()->flash('message', 'Expediente creado exitosamente.');

        return redirect()->to('/expedientes');
    }

    public function createMateria()
    {
        $this->validate(['newMateriaNombre' => 'required|string|max:255']);
        
        $materia = Materia::create(['nombre' => $this->newMateriaNombre]);
        $this->materia = $materia->nombre;
        $this->showMateriaModal = false;
        $this->reset(['newMateriaNombre']);
    }

    public function createJuzgado()
    {
        $this->validate([
            'newJuzgadoNombre' => 'required|string|max:255',
            'newJuzgadoDireccion' => 'nullable|string|max:255',
            'newJuzgadoTelefono' => 'nullable|string|max:255',
        ]);

        $juzgado = Juzgado::create([
            'nombre' => $this->newJuzgadoNombre,
            'direccion' => $this->newJuzgadoDireccion,
            'telefono' => $this->newJuzgadoTelefono,
        ]);

        $this->juzgado = $juzgado->nombre;
        $this->showJuzgadoModal = false;
        $this->reset(['newJuzgadoNombre', 'newJuzgadoDireccion', 'newJuzgadoTelefono']);
    }

    public function createCliente()
    {
        $this->validate([
            'newClienteNombre' => 'required|string|max:255',
            'newClienteEmail' => 'nullable|email',
            'newClienteTelefono' => 'nullable|string|max:255',
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
        $authUser = auth()->user();
        $isAdmin = $authUser->hasRole('admin') || $authUser->can('view all expedientes');
        if (!$isAdmin || $authUser->role === 'super_admin') {
            abort(403);
        }

        $this->validate([
            'newAbogadoNombre' => 'required|string|max:255',
            'newAbogadoEmail' => 'required|email|unique:users,email',
        ]);

        $user = User::create([
            'name' => $this->newAbogadoNombre,
            'email' => $this->newAbogadoEmail,
            'password' => Hash::make('password123'), // Default password
            'role' => 'abogado',
            'tenant_id' => auth()->user()->tenant_id,
        ]);

        $user->assignRole('abogado');

        $this->abogado_responsable_id = $user->id;
        $this->showAbogadoModal = false;
        $this->reset(['newAbogadoNombre', 'newAbogadoEmail']);
    }

    public function render()
    {
        $user = auth()->user();
        $isAdmin = ($user->hasRole('admin') || $user->can('view all expedientes')) && $user->role !== 'super_admin';

        $tenantId = session('tenant_id') ?? $user?->tenant_id;

        $abogadosQuery = User::role(['abogado', 'admin']);
        if ($user->role !== 'super_admin') {
            $abogadosQuery->where('tenant_id', $user->tenant_id);
        }

        if ($tenantId) {
            $materias = Materia::withoutGlobalScope('tenant')->where('tenant_id', $tenantId)->orderBy('nombre')->get();
            $juzgados = Juzgado::withoutGlobalScope('tenant')->where('tenant_id', $tenantId)->orderBy('nombre')->get();
        } else {
            $materias = Materia::orderBy('nombre')->get();
            $juzgados = Juzgado::orderBy('nombre')->get();
        }

        return view('livewire.expedientes.create', [
            'clientes' => Cliente::all(),
            'abogados' => $abogadosQuery->get(),
            'materias' => $materias,
            'juzgados' => $juzgados,
            'estadosProcesales' => EstadoProcesal::orderBy('nombre')->get(),
            'isAdmin' => $isAdmin,
        ]);
    }
}
