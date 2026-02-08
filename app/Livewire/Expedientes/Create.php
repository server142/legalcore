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
    use \App\Traits\Auditable;
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
    public $vencimiento_termino;
    public $honorarios_totales = 0;
    public $anticipo_inicial = 0;

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
        'fecha_inicio' => 'nullable|date',
        'vencimiento_termino' => 'nullable|date',
        'honorarios_totales' => 'nullable|numeric|min:0',
        'anticipo_inicial' => 'nullable|numeric|min:0',
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

        $estado = !empty($this->estado_procesal_id) ? EstadoProcesal::find($this->estado_procesal_id) : null;
        $saldo = (float)$this->honorarios_totales - (float)$this->anticipo_inicial;

        $expediente = Expediente::create([
            'numero' => $this->numero,
            'titulo' => $this->titulo,
            'materia' => $this->materia,
            'juzgado' => $this->juzgado,
            'nombre_juez' => $this->nombre_juez,
            'cliente_id' => $this->cliente_id,
            'abogado_responsable_id' => $this->abogado_responsable_id,
            'descripcion' => $this->descripcion ?: null,
            'fecha_inicio' => $this->fecha_inicio ?: null,
            'vencimiento_termino' => $this->vencimiento_termino ?: null,
            'estado_procesal' => $estado?->nombre ?? 'inicial',
            'estado_procesal_id' => !empty($this->estado_procesal_id) ? $this->estado_procesal_id : null,
            'honorarios_totales' => $this->honorarios_totales ?: 0,
            'saldo_pendiente' => $saldo > 0 ? $saldo : 0,
        ]);

        // Notify responsible lawyer
        $responsible = User::find($this->abogado_responsable_id);
        if ($responsible) {
            \Illuminate\Support\Facades\Mail::to($responsible->email)->queue(new \App\Mail\ExpedienteAssigned($expediente, $responsible, true));
        }

        // Crear factura por el anticipo si existe
        if ($this->anticipo_inicial > 0) {
            \App\Models\Factura::create([
                'tenant_id' => $user->tenant_id,
                'cliente_id' => $this->cliente_id,
                'expediente_id' => $expediente->id,
                'subtotal' => $this->anticipo_inicial / 1.16,
                'iva' => $this->anticipo_inicial - ($this->anticipo_inicial / 1.16),
                'total' => $this->anticipo_inicial,
                'estado' => 'pagada',
                'fecha_pago' => now(),
                'conceptos' => [['descripcion' => "Anticipo honorarios - Exp: {$this->numero}", 'monto' => $this->anticipo_inicial]],
                'fecha_emision' => now(),
            ]);
        }

        // Crear factura pendiente por el resto si existe
        if ($saldo > 0) {
            \App\Models\Factura::create([
                'tenant_id' => $user->tenant_id,
                'cliente_id' => $this->cliente_id,
                'expediente_id' => $expediente->id,
                'subtotal' => $saldo / 1.16,
                'iva' => $saldo - ($saldo / 1.16),
                'total' => $saldo,
                'estado' => 'pendiente',
                'fecha_vencimiento' => now()->addDays(30),
                'conceptos' => [['descripcion' => "Saldo pendiente honorarios - Exp: {$this->numero}", 'monto' => $saldo]],
                'fecha_emision' => now(),
            ]);
        }

        // Audit Log
        $this->logAudit('crear', 'Expedientes', "Creó el expediente: {$this->numero}", [
            'expediente_id' => $expediente->id,
            'titulo' => $this->titulo
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
