<?php

namespace App\Livewire\Expedientes;

use Livewire\Component;

use App\Models\Expediente;
use App\Models\Actuacion;
use App\Models\Documento;
use App\Models\AuditLog;
use App\Models\EstadoProcesal;
use App\Models\Materia;
use App\Models\Juzgado;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\On;

class Show extends Component
{
    public Expediente $expediente;
    public $activeTab = 'actuaciones';
    public $showAddActuacion = false;
    public $showAddEvent = false;
    public $selectedDoc = null;
    public $showViewer = false;
    public $showEditModal = false;
    public $viewingNote = null;

    // Edit fields
    public $numero, $titulo, $materia, $juzgado, $estado_procesal, $estado_procesal_id, $nombre_juez, $fecha_inicio, $cliente_id, $abogado_responsable_id;
    public $honorarios_totales, $saldo_pendiente, $anticipo = 0;

    // Payment fields
    public $monto_pago;
    public $concepto_pago = 'Abono a honorarios';
    public $breakdown_iva = true;
    public $iva_amount = 0;
    public $subtotal_amount = 0;

    public function mount(Expediente $expediente)
    {
        $user = auth()->user();
        if ($user->hasRole('abogado') && !$user->can('view all expedientes')) {
            $isAssigned = $expediente->assignedUsers()->where('users.id', $user->id)->exists();
            if ($expediente->abogado_responsable_id !== $user->id && !$isAssigned) {
                abort(403);
            }
        }
        
        $this->expediente = $expediente->load(['cliente', 'abogado', 'actuaciones', 'documentos', 'eventos', 'comentarios.user', 'aiNotes.user']);

        // Check IVA setting
        $settings = $user->tenant->settings ?? [];
        // Default to true if not set, or check both keys for backward compatibility
        if (isset($settings['billing_apply_iva'])) {
            $this->breakdown_iva = (bool)$settings['billing_apply_iva'];
        } else {
            $this->breakdown_iva = $settings['asesorias_billing_apply_iva'] ?? true;
        }
    }

    public function updatedMontoPago($value)
    {
        $value = (float)$value;
        if ($this->breakdown_iva) {
            $this->subtotal_amount = round($value / 1.16, 2);
            $this->iva_amount = round($value - $this->subtotal_amount, 2);
        } else {
            $this->subtotal_amount = $value;
            $this->iva_amount = 0;
        }
    }

    public function updatedBreakdownIva($value)
    {
        $monto = (float)$this->monto_pago;
        if ($value) {
            $this->subtotal_amount = round($monto / 1.16, 2);
            $this->iva_amount = round($monto - $this->subtotal_amount, 2);
        } else {
            $this->subtotal_amount = $monto;
            $this->iva_amount = 0;
        }
    }

    public function updatedIvaAmount($value)
    {
        // If user manually changes IVA, we recalculate Total (Monto Pago) keeping Subtotal fixed?
        // Or Recalculate Subtotal keeping Total fixed? 
        // User requested: "se calcule el total si lo quita o cambia" -> Calculate Total.
        // Assuming Subtotal is the base.
        // But wait, if user just typed the Total previously, Subtotal was derived.
        // Let's assume Subtotal is the 'real' value and we add tax on top.
        $this->monto_pago = (float)$this->subtotal_amount + (float)$value;
    }

    public function updatedSubtotalAmount($value)
    {
        // If user manually changes Subtotal
        $this->monto_pago = (float)$value + (float)$this->iva_amount;
    }

    #[On('actuacion-added')]
    public function refreshActuaciones()
    {
        $this->expediente->load('actuaciones');
        $this->showAddActuacion = false;
    }

    #[On('document-uploaded')]
    public function refreshDocumentos()
    {
        $this->expediente->load('documentos');
    }

    #[On('event-added')]
    public function refreshEventos()
    {
        $this->expediente->load('eventos');
        $this->showAddEvent = false;
    }

    public function toggleAddActuacion()
    {
        $this->showAddActuacion = !$this->showAddActuacion;
    }

    public function toggleAddEvent()
    {
        $this->showAddEvent = !$this->showAddEvent;
    }

    public function openViewer($docId)
    {
        $this->selectedDoc = Documento::find($docId);
        $this->showViewer = true;
    }

    public function closeViewer()
    {
        $this->showViewer = false;
        $this->selectedDoc = null;
    }

    public function deleteDocument($docId)
    {
        $doc = Documento::find($docId);
        if ($doc) {
            $nombre = $doc->nombre;
            Storage::disk('local')->delete($doc->path);
            $doc->delete();

            AuditLog::create([
                'user_id' => auth()->id(),
                'accion' => 'delete',
                'modulo' => 'documentos',
                'descripcion' => "Eliminó el archivo: {$nombre}",
                'metadatos' => ['documento_id' => $docId],
                'ip_address' => request()->ip(),
            ]);

            $this->expediente->load('documentos');
        }
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function edit()
    {
        $this->resetErrorBag();
        $this->numero = $this->expediente->numero;
        $this->titulo = $this->expediente->titulo;
        $this->materia = $this->expediente->materia;
        $this->juzgado = $this->expediente->juzgado;
        $this->estado_procesal = $this->expediente->estado_procesal;
        $this->estado_procesal_id = $this->expediente->estado_procesal_id;
        $this->nombre_juez = $this->expediente->nombre_juez;
        $this->fecha_inicio = $this->expediente->fecha_inicio?->format('Y-m-d');
        $this->cliente_id = $this->expediente->cliente_id;
        $this->abogado_responsable_id = $this->expediente->abogado_responsable_id;
        $this->honorarios_totales = $this->expediente->honorarios_totales;
        $this->saldo_pendiente = $this->expediente->saldo_pendiente;
        $this->anticipo = 0; // Reset anticipo on edit
        
        $this->showEditModal = true;
    }

    public function update()
    {
        $this->validate([
            'numero' => 'required|string|max:255',
            'titulo' => 'required|string|max:255',
            'materia' => 'required|string|max:255',
            'juzgado' => 'nullable|string|max:255',
            'estado_procesal_id' => 'nullable|exists:estados_procesales,id',
            'cliente_id' => 'required|exists:clientes,id',
            'abogado_responsable_id' => 'required|exists:users,id',
        ]);

        $estado = $this->estado_procesal_id ? EstadoProcesal::find($this->estado_procesal_id) : null;

        $this->expediente->update([
            'numero' => $this->numero,
            'titulo' => $this->titulo,
            'materia' => $this->materia,
            'juzgado' => $this->juzgado,
            'estado_procesal' => $estado?->nombre ?? $this->expediente->estado_procesal,
            'estado_procesal_id' => $this->estado_procesal_id,
            'nombre_juez' => $this->nombre_juez,
            'fecha_inicio' => $this->fecha_inicio,
            'cliente_id' => $this->cliente_id,
            'abogado_responsable_id' => $this->abogado_responsable_id,
            'honorarios_totales' => $this->honorarios_totales,
        ]);

        $oldHonorarios = $this->expediente->getOriginal('honorarios_totales');
        $honorariosChanged = $this->honorarios_totales != $oldHonorarios;

        if ($this->anticipo > 0) {
            // 1. Create paid factura (Receipt)
            \App\Models\Factura::create([
                'tenant_id' => auth()->user()->tenant_id,
                'cliente_id' => $this->cliente_id,
                'expediente_id' => $this->expediente->id,
                'subtotal' => $this->anticipo / 1.16,
                'iva' => $this->anticipo - ($this->anticipo / 1.16),
                'total' => $this->anticipo,
                'estado' => 'pagada',
                'fecha_pago' => now(),
                'conceptos' => [['descripcion' => "Anticipo/Abono honorarios - Exp: {$this->numero}", 'monto' => $this->anticipo]],
                'fecha_emision' => now(),
            ]);

            // 2. Deduct from existing pending invoices if any
            $this->deductFromPendingFacturas($this->anticipo);
            
            // 3. Force saldo recalculation
            $honorariosChanged = true;
        }

        if ($honorariosChanged) {
            $pagado = $this->expediente->facturas()->where('estado', 'pagada')->sum('total');
            $nuevoSaldo = (float)$this->honorarios_totales - $pagado;
            $this->expediente->update(['saldo_pendiente' => $nuevoSaldo > 0 ? $nuevoSaldo : 0]);

            // If it's the first time setting honorarios, create a pending invoice for the remainder
            if ($oldHonorarios == 0 && $nuevoSaldo > 0) {
                \App\Models\Factura::create([
                    'tenant_id' => auth()->user()->tenant_id,
                    'cliente_id' => $this->cliente_id,
                    'expediente_id' => $this->expediente->id,
                    'subtotal' => $nuevoSaldo / 1.16,
                    'iva' => $nuevoSaldo - ($nuevoSaldo / 1.16),
                    'total' => $nuevoSaldo,
                    'estado' => 'pendiente',
                    'fecha_vencimiento' => now()->addDays(30),
                    'conceptos' => [['descripcion' => "Saldo pendiente honorarios - Exp: {$this->numero}", 'monto' => $nuevoSaldo]],
                    'fecha_emision' => now(),
                ]);
            }
        }

        $this->showEditModal = false;
        $this->expediente->refresh();
        $this->dispatch('notify', 'Expediente actualizado exitosamente');
    }

    protected function deductFromPendingFacturas($amount)
    {
        $remainingToDeduct = $amount;
        $pendingInvoices = \App\Models\Factura::where('expediente_id', $this->expediente->id)
            ->where('estado', 'pendiente')
            ->orderBy('created_at', 'asc')
            ->get();

        foreach ($pendingInvoices as $invoice) {
            if ($remainingToDeduct <= 0) break;

            if ($invoice->total <= $remainingToDeduct) {
                $deducted = $invoice->total;
                $invoice->delete(); 
                $remainingToDeduct -= $deducted;
            } else {
                $newTotal = $invoice->total - $remainingToDeduct;
                $newSubtotal = $newTotal / 1.16;
                $newIva = $newTotal - $newSubtotal;

                $invoice->update([
                    'total' => $newTotal,
                    'subtotal' => $newSubtotal,
                    'iva' => $newIva,
                ]);
                $remainingToDeduct = 0;
            }
        }
    }

    public function recordPayment()
    {
        $this->validate([
            'monto_pago' => 'required|numeric|min:0.01',
            'concepto_pago' => 'required|string|max:255',
        ]);

        if ($this->monto_pago > $this->expediente->saldo_pendiente) {
             $this->addError('monto_pago', 'El monto no puede ser mayor al saldo pendiente.');
             return;
        }

        $user = auth()->user();

        // 1. Create Receipt Invoice (Income)
        // Use calculated breakdown
        $subtotal = $this->subtotal_amount > 0 ? $this->subtotal_amount : ($this->monto_pago / 1.16);
        $iva = $this->iva_amount >= 0 ? $this->iva_amount : ($this->monto_pago - $subtotal);
        // Fallback safety if 0
        if ($this->breakdown_iva && $this->iva_amount == 0 && $this->monto_pago > 0) {
             // User explicitly set 0? If so, respect it.
             // If implicit from code, respect logic.
             // Our updatedMontoPago logic handles this.
        }

        \App\Models\Factura::create([
            'tenant_id' => $user->tenant_id,
            'cliente_id' => $this->expediente->cliente_id,
            'expediente_id' => $this->expediente->id,
            'subtotal' => $this->subtotal_amount,
            'iva' => $this->iva_amount,
            'total' => $this->monto_pago,
            'estado' => 'pagada',
            'fecha_pago' => now(),
            'conceptos' => [['descripcion' => $this->concepto_pago . " - Exp: {$this->expediente->numero}", 'monto' => $this->monto_pago]],
            'fecha_emision' => now(),
        ]);

        // 2. Reduce Pending Invoices (Accounts Receivable)
        $remainingToDeduct = $this->monto_pago;
        
        $pendingInvoices = \App\Models\Factura::where('expediente_id', $this->expediente->id)
            ->where('estado', 'pendiente')
            ->orderBy('created_at', 'asc') // Pay oldest debt first
            ->get();

        foreach ($pendingInvoices as $invoice) {
            if ($remainingToDeduct <= 0) break;

            if ($invoice->total <= $remainingToDeduct) {
                // This invoice is fully paid off
                $deducted = $invoice->total;
                $invoice->delete(); 
                $remainingToDeduct -= $deducted;
            } else {
                // Partial payment of this invoice
                $newTotal = $invoice->total - $remainingToDeduct;
                $newSubtotal = $newTotal / 1.16;
                $newIva = $newTotal - $newSubtotal;

                $invoice->update([
                    'total' => $newTotal,
                    'subtotal' => $newSubtotal,
                    'iva' => $newIva,
                ]);
                $remainingToDeduct = 0;
            }
        }

        // 3. Update Expediente Saldo
        $newSaldo = (float)$this->expediente->saldo_pendiente - (float)$this->monto_pago;
        $this->expediente->update(['saldo_pendiente' => $newSaldo > 0 ? $newSaldo : 0]);

        $this->reset(['monto_pago', 'iva_amount', 'subtotal_amount']);
        $this->expediente->refresh();
        $this->dispatch('notify', 'Pago registrado exitosamente');
    }

    public function cancelPayment($facturaId)
    {
        $factura = \App\Models\Factura::find($facturaId);

        if (!$factura || $factura->estado != 'pagada') {
            return;
        }

        // 1. Mark as cancelled
        $factura->update(['estado' => 'cancelada']);

        // 2. Increase Expediente Saldo (Restore Debt)
        $montoCancelado = $factura->total;
        $newSaldo = (float)$this->expediente->saldo_pendiente + (float)$montoCancelado;
        $this->expediente->update(['saldo_pendiente' => $newSaldo]);

        // 3. Create a new Pending Invoice to represent the debt that came back
        // Since we don't know exactly which pending invoices were paid by this specific payment
        // (because we deleted them or reduced them), the safest bet is to create a new
        // pending invoice for this amount.
        \App\Models\Factura::create([
            'tenant_id' => $factura->tenant_id,
            'cliente_id' => $factura->cliente_id,
            'expediente_id' => $factura->expediente_id,
            'subtotal' => $factura->subtotal,
            'iva' => $factura->iva,
            'total' => $factura->total,
            'estado' => 'pendiente', // Back to pending
            'fecha_emision' => now(),
            'fecha_vencimiento' => now()->addDays(7), // Give it a short due date as it's a reversed payment
            'conceptos' => [['descripcion' => "Saldo pendiente (Cancelación de pago #{$factura->id}) - Exp: {$this->expediente->numero}", 'monto' => $factura->total]],
        ]);

        $this->expediente->refresh();
        $this->dispatch('notify', 'Pago cancelado y saldo actualizado correchamente');
    }

    public function viewNote($noteId)
    {
        $this->viewingNote = \App\Models\AiNote::find($noteId);
    }

    public function closeNote()
    {
        $this->viewingNote = null;
    }

    public function deleteAiNote($noteId)
    {
        $note = \App\Models\AiNote::find($noteId);
        if ($note) {
            $note->delete();
            $this->expediente->load('aiNotes.user');
            $this->dispatch('notify', 'Nota eliminada correctamente');
        }
    }

    public function render()
    {
        $tenantId = $this->expediente->tenant_id;

        $materias = Materia::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenantId)
            ->orderBy('nombre')
            ->get();

        $juzgados = Juzgado::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenantId)
            ->orderBy('nombre')
            ->get();

        return view('livewire.expedientes.show', [
            'clientes' => \App\Models\Cliente::all(),
            'abogados' => \App\Models\User::role('abogado')->get(),
            'materias' => $materias,
            'juzgados' => $juzgados,
            'estadosProcesales' => EstadoProcesal::orderBy('nombre')->get(),
        ]);
    }
}
