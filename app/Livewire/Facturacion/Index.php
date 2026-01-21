<?php

namespace App\Livewire\Facturacion;

use Livewire\Component;

use App\Models\Factura;

class Index extends Component
{
    public $facturas;
    public $totalCobrado = 0;
    public $totalPendiente = 0;
    public $facturasMes = 0;
    public $facturasVencidas = 0;

    public function mount()
    {
        if (!auth()->user()->can('manage billing')) {
            abort(403);
        }
        $this->loadData();
    }

    public function loadData()
    {
        $this->facturas = Factura::with('cliente')->latest()->get();
        
        $this->totalCobrado = Factura::where('tenant_id', auth()->user()->tenant_id)
            ->where('estado', 'pagada')
            ->sum('total');
            
        $this->totalPendiente = Factura::where('tenant_id', auth()->user()->tenant_id)
            ->where('estado', 'pendiente')
            ->sum('total');
            
        $this->facturasMes = Factura::where('tenant_id', auth()->user()->tenant_id)
            ->whereMonth('created_at', now()->month)
            ->count();
            
        $this->facturasVencidas = Factura::where('tenant_id', auth()->user()->tenant_id)
            ->where('estado', 'pendiente')
            ->where('fecha_vencimiento', '<', now())
            ->count();
    }

    public $showModal = false;
    public $cliente_id;
    public $total;
    public $estado = 'pendiente';
    public $moneda = 'MXN';
    public $concepto = 'Servicios Legales Profesionales';

    protected $rules = [
        'cliente_id' => 'required|exists:clientes,id',
        'total' => 'required|numeric|min:0',
        'moneda' => 'required|in:MXN,USD',
        'estado' => 'required|in:pendiente,pagada,cancelada',
        'concepto' => 'required|string|max:255',
    ];

    public function create()
    {
        $this->reset(['cliente_id', 'total', 'moneda', 'estado']);
        $this->showModal = true;
    }

    public function store()
    {
        $this->validate();

        $subtotal = $this->total / 1.16;
        $iva = $this->total - $subtotal;

        Factura::create([
            'tenant_id' => auth()->user()->tenant_id,
            'cliente_id' => $this->cliente_id,
            'subtotal' => $subtotal,
            'iva' => $iva,
            'total' => $this->total,
            'moneda' => $this->moneda,
            'estado' => $this->estado,
            'conceptos' => [['descripcion' => $this->concepto, 'monto' => $this->total]],
            'fecha_emision' => now(),
            'fecha_vencimiento' => now()->addDays(30),
        ]);

        $this->showModal = false;
        $this->dispatch('notify', 'Factura creada exitosamente');
        $this->loadData();
    }

    public function markAsPaid($id)
    {
        $factura = Factura::where('tenant_id', auth()->user()->tenant_id)->findOrFail($id);
        $factura->update(['estado' => 'pagada']);
        
        $this->dispatch('notify', 'Factura marcada como pagada');
        $this->loadData();
    }

    public function markAsCancelled($id)
    {
        $factura = Factura::where('tenant_id', auth()->user()->tenant_id)->findOrFail($id);
        $factura->update(['estado' => 'cancelada']);
        
        $this->dispatch('notify', 'Factura cancelada');
        $this->loadData();
    }

    public function render()
    {
        return view('livewire.facturacion.index', [
            'clientes' => \App\Models\Cliente::where('tenant_id', auth()->user()->tenant_id)->get()
        ]);
    }
}
