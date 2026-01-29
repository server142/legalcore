<?php

namespace App\Livewire\Asesorias;

use Livewire\Component;
use App\Models\Asesoria;
use App\Models\Cliente;
use App\Models\Expediente;
use App\Models\EstadoProcesal;
use App\Models\Evento;
use App\Models\Factura;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class Form extends Component
{
    public ?Asesoria $asesoria = null;
    public $modoEdicion = false;

    // Campos del formulario
    public $nombre_prospecto;
    public $telefono;
    public $email;
    public $cliente_id;
    public $asunto;
    public $notas;
    public $fecha;
    public $hora;
    public $duracion_minutos = 30;
    public $tipo = 'presencial';
    public $abogado_id;
    public $costo = 0;
    public $link_videoconferencia;
    
    // Campos de seguimiento
    public $estado = 'agendada';
    public $motivo_cancelacion;
    public $motivo_no_atencion;
    public $resumen;
    public $prospecto_acepto;
    public $pagado = false;
    public $fecha_pago;

    public $canManageBilling = false;
    public $asesoriasBillingEnabled = false;
    public $asesoriasBillingApplyIva = true;

    public $suggested_fecha;
    public $suggested_hora;

    // Conversión a cliente/expediente
    public $crear_cliente = false;
    public $crear_expediente = false;

    // Modal: Nuevo Cliente (igual a Expedientes)
    public $showClienteModal = false;
    public $newClienteNombre;
    public $newClienteEmail;
    public $newClienteTelefono;

    public function mount($asesoria = null)
    {
        $tenant = auth()->user()->tenant;
        $settings = $tenant?->settings ?? [];
        $this->asesoriasBillingEnabled = (bool) ($settings['asesorias_billing_enabled'] ?? false);
        $this->asesoriasBillingApplyIva = (bool) ($settings['asesorias_billing_apply_iva'] ?? true);
        $this->canManageBilling = auth()->user()->can('manage billing');

        if ($asesoria) {
            $this->asesoria = $asesoria;
            $this->modoEdicion = true;
            $this->cargarDatos();
        } else {
            $this->abogado_id = auth()->id();
            $this->fecha = Carbon::today()->format('Y-m-d');
            $this->hora = Carbon::now()->addHour()->format('H:00');
        }

    }

    public function applySuggestedSlot()
    {
        if ($this->suggested_fecha && $this->suggested_hora) {
            $this->fecha = $this->suggested_fecha;
            $this->hora = $this->suggested_hora;
            $this->suggested_fecha = null;
            $this->suggested_hora = null;
        }
    }

    private function hasAgendaConflict(Carbon $start, int $durationMinutes, int $abogadoId): bool
    {
        $end = (clone $start)->addMinutes($durationMinutes);

        $query = Evento::query()
            ->where('tenant_id', auth()->user()->tenant_id)
            ->where('user_id', $abogadoId)
            ->where('start_time', '<', $end)
            ->where('end_time', '>', $start);

        if ($this->asesoria) {
            $query->where(function ($q) {
                $q->whereNull('asesoria_id')
                  ->orWhere('asesoria_id', '!=', $this->asesoria->id);
            });
        }

        return $query->exists();
    }

    private function suggestNextAvailableSlot(Carbon $requestedStart, int $durationMinutes, int $abogadoId): ?Carbon
    {
        $tenant = auth()->user()->tenant;
        $settings = $tenant?->settings ?? [];

        $workStart = $settings['asesorias_working_hours_start'] ?? '09:00';
        $workEnd = $settings['asesorias_working_hours_end'] ?? '18:00';
        $businessDays = $settings['asesorias_business_days'] ?? ['mon', 'tue', 'wed', 'thu', 'fri'];
        $slotMinutes = (int) ($settings['asesorias_slot_minutes'] ?? 15);

        $candidate = (clone $requestedStart);
        $candidate->second(0);

        if ($slotMinutes > 0) {
            $minute = (int) $candidate->format('i');
            $rounded = (int) (ceil($minute / $slotMinutes) * $slotMinutes);
            if ($rounded >= 60) {
                $candidate->addHour()->minute(0);
            } else {
                $candidate->minute($rounded);
            }
        }

        for ($i = 0; $i < 2000; $i++) {
            $dayKey = strtolower($candidate->format('D'));
            $dayKey = substr($dayKey, 0, 3);

            if (!in_array($dayKey, $businessDays, true)) {
                $candidate->addDay()->setTimeFromTimeString($workStart);
                continue;
            }

            $startLimit = (clone $candidate)->setTimeFromTimeString($workStart);
            $endLimit = (clone $candidate)->setTimeFromTimeString($workEnd);

            if ($candidate->lt($startLimit)) {
                $candidate = $startLimit;
            }

            $candidateEnd = (clone $candidate)->addMinutes($durationMinutes);
            if ($candidateEnd->gt($endLimit)) {
                $candidate->addDay()->setTimeFromTimeString($workStart);
                continue;
            }

            if (!$this->hasAgendaConflict($candidate, $durationMinutes, $abogadoId)) {
                return $candidate;
            }

            $candidate->addMinutes(max(5, $slotMinutes));
        }

        return null;
    }

    private function syncAsesoriaToAgenda(): void
    {
        if (!$this->asesoria) {
            Log::info('syncAsesoriaToAgenda: No hay asesoría');
            return;
        }

        Log::info('syncAsesoriaToAgenda: Iniciando para asesoría ' . $this->asesoria->id . ', estado: ' . $this->asesoria->estado);

        $evento = Evento::where('asesoria_id', $this->asesoria->id)->first();

        if ($this->asesoria->estado !== 'agendada') {
            Log::info('syncAsesoriaToAgenda: Estado no agendada, eliminando evento si existe');
            if ($evento) {
                $evento->delete();
            }
            return;
        }

        $start = $this->asesoria->fecha_hora;
        $end = (clone $start)->addMinutes((int) $this->asesoria->duracion_minutos);

        Log::info('syncAsesoriaToAgenda: Creando evento del ' . $start->format('Y-m-d H:i:s') . ' al ' . $end->format('Y-m-d H:i:s'));

        $descripcion = $this->asesoria->asunto;
        if ($this->asesoria->telefono) {
            $descripcion .= "\nTel: " . $this->asesoria->telefono;
        }
        if ($this->asesoria->email) {
            $descripcion .= "\nEmail: " . $this->asesoria->email;
        }
        if ($this->asesoria->tipo === 'videoconferencia' && $this->asesoria->link_videoconferencia) {
            $descripcion .= "\nVideollamada: " . $this->asesoria->link_videoconferencia;
        }

        $data = [
            'tenant_id' => $this->asesoria->tenant_id,
            'titulo' => "Asesoría {$this->asesoria->folio} - {$this->asesoria->nombre_prospecto}",
            'descripcion' => $descripcion,
            'start_time' => $start,
            'end_time' => $end,
            'tipo' => 'cita',
            'user_id' => $this->asesoria->abogado_id,
            'expediente_id' => $this->asesoria->expediente_id,
            'asesoria_id' => $this->asesoria->id,
        ];

        try {
            if ($evento) {
                $evento->update($data);
                Log::info('syncAsesoriaToAgenda: Evento actualizado ID: ' . $evento->id);
            } else {
                $evento = Evento::create($data);
                Log::info('syncAsesoriaToAgenda: Evento creado ID: ' . $evento->id);
            }
        } catch (\Exception $e) {
            Log::error('syncAsesoriaToAgenda: Error creando evento: ' . $e->getMessage());
        }
    }

    public function cargarDatos()
    {
        $this->nombre_prospecto = $this->asesoria->nombre_prospecto;
        $this->telefono = $this->asesoria->telefono;
        $this->email = $this->asesoria->email;
        $this->cliente_id = $this->asesoria->cliente_id;
        $this->asunto = $this->asesoria->asunto;
        $this->notas = $this->asesoria->notas;
        $this->fecha = $this->asesoria->fecha_hora->format('Y-m-d');
        $this->hora = $this->asesoria->fecha_hora->format('H:i');
        $this->duracion_minutos = $this->asesoria->duracion_minutos;
        $this->tipo = $this->asesoria->tipo;
        $this->abogado_id = $this->asesoria->abogado_id;
        $this->costo = $this->asesoria->costo;
        $this->link_videoconferencia = $this->asesoria->link_videoconferencia;
        $this->estado = $this->asesoria->estado;
        $this->motivo_cancelacion = $this->asesoria->motivo_cancelacion;
        $this->motivo_no_atencion = $this->asesoria->motivo_no_atencion;
        $this->resumen = $this->asesoria->resumen;
        $this->prospecto_acepto = $this->asesoria->prospecto_acepto;
        $this->pagado = $this->asesoria->pagado;
        $this->fecha_pago = $this->asesoria->fecha_pago?->format('Y-m-d');
    }

    public function updatedTipo($value)
    {
        if ($value === 'videoconferencia' && empty($this->link_videoconferencia)) {
            // Generar link simulado o dejar vacío para que el usuario lo llene
            // En el futuro aquí se podría integrar con Google Meet / Zoom API
        }
    }

    public function updatedEstado($value)
    {
        // Limpiar campos irrelevantes según el estado
        if ($value === 'agendada') {
            $this->motivo_cancelacion = null;
            $this->motivo_no_atencion = null;
            $this->resumen = null;
        } elseif ($value === 'realizada') {
            $this->motivo_cancelacion = null;
            $this->motivo_no_atencion = null;
        }
    }

    public function updatedPagado($value)
    {
        if ($value && empty($this->fecha_pago)) {
            $this->fecha_pago = Carbon::today()->format('Y-m-d');
        }
    }

    public function updatedClienteId($value)
    {
        if (empty($value)) {
            return;
        }

        $cliente = Cliente::where('tenant_id', auth()->user()->tenant_id)->find($value);
        if (!$cliente) {
            return;
        }

        $this->nombre_prospecto = $cliente->nombre;
        $this->telefono = $cliente->telefono;
        $this->email = $cliente->email;
    }

    public function createCliente()
    {
        $this->validate([
            'newClienteNombre' => 'required|string|max:255',
            'newClienteEmail' => 'nullable|email',
            'newClienteTelefono' => 'nullable|string|max:255',
        ]);

        $cliente = Cliente::create([
            'tenant_id' => auth()->user()->tenant_id,
            'nombre' => $this->newClienteNombre,
            'email' => $this->newClienteEmail,
            'telefono' => $this->newClienteTelefono,
            'tipo' => 'persona_fisica',
            'origen' => 'asesoria',
        ]);

        $this->cliente_id = $cliente->id;
        $this->showClienteModal = false;
        $this->reset(['newClienteNombre', 'newClienteEmail', 'newClienteTelefono']);
    }

    private function syncFacturaFromPago(): void
    {
        if (!$this->asesoria || !$this->asesoriasBillingEnabled) {
            return;
        }

        if (!$this->canManageBilling) {
            return;
        }

        $this->asesoria->refresh();

        if (empty($this->asesoria->cliente_id)) {
            throw new \RuntimeException('No se puede generar el recibo porque la asesoría no tiene cliente asociado.');
        }

        $factura = null;
        if ($this->asesoria->factura_id) {
            $factura = Factura::where('tenant_id', $this->asesoria->tenant_id)->find($this->asesoria->factura_id);
        }

        if (!$this->asesoria->pagado) {
            if ($factura) {
                $factura->update(['estado' => 'pendiente']);
            }
            return;
        }

        $total = (float) $this->asesoria->costo;
        $subtotal = $this->asesoriasBillingApplyIva ? ($total / 1.16) : $total;
        $iva = $this->asesoriasBillingApplyIva ? ($total - $subtotal) : 0;

        $payload = [
            'tenant_id' => $this->asesoria->tenant_id,
            'cliente_id' => $this->asesoria->cliente_id,
            'subtotal' => $subtotal,
            'iva' => $iva,
            'total' => $total,
            'moneda' => 'MXN',
            'estado' => 'pagada',
            'conceptos' => [[
                'descripcion' => "Asesoría {$this->asesoria->folio} - {$this->asesoria->asunto}",
                'monto' => $total,
            ]],
            'fecha_emision' => $this->asesoria->fecha_pago ?? now(),
            'fecha_vencimiento' => ($this->asesoria->fecha_pago ?? now())->copy()->addDays(30),
            'fecha_pago' => $this->asesoria->fecha_pago ?? now(),
        ];

        if ($factura) {
            $factura->update($payload);
        } else {
            $factura = Factura::create($payload);
            $this->asesoria->update(['factura_id' => $factura->id]);
        }
    }

    public function guardar()
    {
        $user = auth()->user();
        $isAdmin = $user->hasRole(['admin', 'super_admin']);

        $rules = [
            'cliente_id' => [
                'required',
                Rule::exists('clientes', 'id')->where(function ($q) use ($user) {
                    $q->where('tenant_id', $user->tenant_id);
                }),
            ],
            'nombre_prospecto' => 'required|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'asunto' => 'required|string|max:1000',
            'fecha' => 'required|date',
            'hora' => 'required',
            'duracion_minutos' => 'required|integer|min:5',
            'tipo' => 'required|in:presencial,telefonica,videoconferencia',
            'abogado_id' => ['nullable'],
            'costo' => 'required|numeric|min:0',
            'estado' => 'required|in:agendada,realizada,cancelada,no_atendida',
        ];

        if ($isAdmin) {
            $rules['abogado_id'][] = Rule::exists('users', 'id')->where(function ($q) use ($user) {
                $q->where('tenant_id', $user->tenant_id);
            });
        }

        if ($this->estado === 'cancelada') {
            $rules['motivo_cancelacion'] = 'required|string';
        }

        if ($this->estado === 'no_atendida') {
            $rules['motivo_no_atencion'] = 'required|string';
        }

        if ($this->tipo === 'videoconferencia') {
            $rules['link_videoconferencia'] = 'nullable|url';
        }

        $this->validate($rules);

        $fechaHora = Carbon::parse($this->fecha . ' ' . $this->hora);

        $effectiveAbogadoId = $this->abogado_id ?: $user->id;
        if (!$isAdmin) {
            $effectiveAbogadoId = $user->id;
        }

        $tenant = auth()->user()->tenant;
        $settings = $tenant?->settings ?? [];
        $enforceAvailability = $settings['asesorias_enforce_availability'] ?? true;
        $syncToAgenda = $settings['asesorias_sync_to_agenda'] ?? true;

        Log::info('guardar: syncToAgenda=' . ($syncToAgenda ? 'true' : 'false') . ', enforceAvailability=' . ($enforceAvailability ? 'true' : 'false'));

        if ($this->estado === 'agendada' && $enforceAvailability) {
            $conflict = $this->hasAgendaConflict($fechaHora, (int) $this->duracion_minutos, (int) $effectiveAbogadoId);
            if ($conflict) {
                $suggestion = $this->suggestNextAvailableSlot($fechaHora, (int) $this->duracion_minutos, (int) $effectiveAbogadoId);
                if ($suggestion) {
                    $this->suggested_fecha = $suggestion->format('Y-m-d');
                    $this->suggested_hora = $suggestion->format('H:i');
                    $this->addError('hora', 'El abogado no tiene disponibilidad en el horario seleccionado. Te proponemos el siguiente horario disponible.');
                } else {
                    $this->addError('hora', 'El abogado no tiene disponibilidad en el horario seleccionado.');
                }
                return;
            }
        }

        $datos = [
            'cliente_id' => $this->cliente_id,
            'nombre_prospecto' => $this->nombre_prospecto,
            'telefono' => $this->telefono,
            'email' => $this->email,
            'asunto' => $this->asunto,
            'notas' => $this->notas,
            'fecha_hora' => $fechaHora,
            'duracion_minutos' => $this->duracion_minutos,
            'tipo' => $this->tipo,
            'abogado_id' => $effectiveAbogadoId,
            'costo' => $this->costo,
            'link_videoconferencia' => $this->link_videoconferencia,
            'estado' => $this->estado,
            'motivo_cancelacion' => $this->motivo_cancelacion,
            'motivo_no_atencion' => $this->motivo_no_atencion,
            'resumen' => $this->resumen,
            'prospecto_acepto' => $this->prospecto_acepto,
        ];

        if ($this->asesoriasBillingEnabled && $this->canManageBilling) {
            $datos['pagado'] = (bool) $this->pagado;
            $datos['fecha_pago'] = $this->pagado ? ($this->fecha_pago ?? now()) : null;
        }

        $facturaUrl = null;
        $shouldOfferReceipt = false;

        try {
            DB::beginTransaction();

            // Crear o Actualizar
            if ($this->modoEdicion) {
                $this->asesoria->update($datos);
                $mensaje = 'Asesoría guardada correctamente.';
            } else {
                $datos['tenant_id'] = auth()->user()->tenant_id;
                $this->asesoria = Asesoria::create($datos);
                $mensaje = 'Asesoría guardada correctamente.';
                $this->modoEdicion = true; // Cambiar a modo edición
            }

            if ($syncToAgenda && $this->asesoria) {
                $this->syncAsesoriaToAgenda();
            }

            if ($this->asesoriasBillingEnabled && $this->canManageBilling) {
                $this->syncFacturaFromPago();
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error guardando asesoría/recibo: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            $this->dispatch('notify-error', 'No se pudo guardar la asesoría o generar el recibo. Revisa los datos e inténtalo de nuevo.');
            return;
        }

        $this->asesoria->refresh();
        if ($this->asesoriasBillingEnabled && $this->canManageBilling && $this->asesoria->pagado && $this->asesoria->factura_id) {
            $shouldOfferReceipt = true;
            $facturaUrl = route('reportes.factura', $this->asesoria->factura_id);
        }

        // Lógica de conversión a Cliente / Expediente
        if ($this->crear_cliente && !$this->asesoria->cliente_id) {
            $this->convertirACliente();
            $mensaje .= ' Se creó el cliente.';
        }

        if ($this->crear_expediente && $this->asesoria->cliente_id && !$this->asesoria->expediente_id) {
            $this->convertirAExpediente();
            $mensaje .= ' Se creó el expediente.';
        }

        if ($shouldOfferReceipt && $facturaUrl) {
            $this->dispatch('asesoria-saved-receipt', [
                'message' => $mensaje,
                'facturaUrl' => $facturaUrl,
                'redirectUrl' => route('asesorias.index'),
            ]);
            return;
        }

        $this->dispatch('notify', $mensaje);
        return redirect()->route('asesorias.index');
    }

    public function convertirACliente()
    {
        $cliente = Cliente::create([
            'tenant_id' => auth()->user()->tenant_id,
            'nombre' => $this->nombre_prospecto,
            'telefono' => $this->telefono,
            'email' => $this->email,
            'tipo' => 'persona_fisica', // Default
            'origen' => 'asesoria',
        ]);

        $this->asesoria->update(['cliente_id' => $cliente->id]);
        $this->crear_cliente = false; // Reset
    }

    public function crearExpedienteDesdeAsesoria()
    {
        if (!$this->asesoria || !$this->asesoria->cliente_id || $this->asesoria->expediente_id) {
            $this->dispatch('notify-error', 'No se puede crear el expediente. La asesoría debe tener un cliente vinculado y no tener un expediente existente.');
            return;
        }

        $this->convertirAExpediente();
        $this->dispatch('notify-success', 'Expediente creado correctamente. Se ha vinculado a esta asesoría.');
    }

    public function convertirAExpediente()
    {
        // Check expediente limit
        $tenant = auth()->user()->tenant;
        
        if ($tenant && $tenant->plan_id) {
            $plan = \App\Models\Plan::find($tenant->plan_id);
            
            if ($plan && !$plan->canAddExpediente($tenant)) {
                $limit = $plan->max_expedientes;
                $this->dispatch('notify-error', "Has alcanzado el límite de {$limit} expedientes de tu plan. Actualiza tu suscripción para crear más.");
                $this->crear_expediente = false;
                return;
            }
        }

        $count = Expediente::where('tenant_id', auth()->user()->tenant_id)->count() + 1;
        $numero = 'EXP-' . date('Y') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);

        $tramite = EstadoProcesal::where('nombre', 'Radicación/Inicio')->first();

        $expediente = Expediente::create([
            'tenant_id' => auth()->user()->tenant_id,
            'numero' => $numero,
            'titulo' => 'Asunto derivado de Asesoría ' . $this->asesoria->folio,
            'cliente_id' => $this->asesoria->cliente_id,
            'abogado_responsable_id' => $this->asesoria->abogado_id,
            'descripcion' => $this->asesoria->asunto . "\n\nResumen Asesoría: " . $this->asesoria->resumen,
            'fecha_inicio' => now(),
            'estado_procesal' => 'Radicación/Inicio',
            'estado_procesal_id' => $tramite?->id,
            'materia' => 'Por definir', // Se debe editar después
            'juzgado' => 'Por definir',
        ]);

        $this->asesoria->update(['expediente_id' => $expediente->id]);
        $this->crear_expediente = false;
    }

    public function render()
    {
        return view('livewire.asesorias.form', [
            'abogados' => User::where('tenant_id', auth()->user()->tenant_id)->role('abogado')->get(),
            'clientes' => Cliente::where('tenant_id', auth()->user()->tenant_id)->orderBy('nombre')->get(),
            'isAdmin' => auth()->user()->hasRole(['admin', 'super_admin']),
            'canManageBilling' => auth()->user()->can('manage billing'),
            'asesoriasBillingEnabled' => (bool) (auth()->user()->tenant?->settings['asesorias_billing_enabled'] ?? false),
        ]);
    }
}
