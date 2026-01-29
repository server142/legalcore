<?php

namespace App\Livewire\Asesorias;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Asesoria;
use App\Models\Factura;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $filtroEstado = '';
    public $filtroTipo = '';
    public $filtroFecha = '';

    public function generarRecibo($asesoriaId)
    {
        $user = auth()->user();
        $canManageBilling = $user->can('manage billing');
        $tenant = $user->tenant;
        $settings = $tenant?->settings ?? [];
        $billingEnabled = (bool) ($settings['asesorias_billing_enabled'] ?? false);
        $applyIva = (bool) ($settings['asesorias_billing_apply_iva'] ?? true);

        if (!$billingEnabled || !$canManageBilling) {
            $this->dispatch('notify-error', 'No tienes permisos para emitir recibos o la función no está habilitada.');
            return;
        }

        $asesoria = Asesoria::with('cliente')->findOrFail($asesoriaId);
        if (!$asesoria->pagado) {
            $this->dispatch('notify-error', 'Para emitir un recibo, primero marca la asesoría como pagada.');
            return;
        }
        if (empty($asesoria->cliente_id)) {
            $this->dispatch('notify-error', 'No se puede emitir recibo: la asesoría no tiene cliente asociado.');
            return;
        }

        try {
            DB::beginTransaction();

            if ($asesoria->factura_id) {
                $factura = Factura::find($asesoria->factura_id);
            } else {
                $factura = null;
            }

            $total = (float) $asesoria->costo;
            $subtotal = $applyIva ? ($total / 1.16) : $total;
            $iva = $applyIva ? ($total - $subtotal) : 0;

            $payload = [
                'tenant_id' => $asesoria->tenant_id,
                'cliente_id' => $asesoria->cliente_id,
                'subtotal' => $subtotal,
                'iva' => $iva,
                'total' => $total,
                'moneda' => 'MXN',
                'estado' => 'pagada',
                'conceptos' => [[
                    'descripcion' => "Asesoría {$asesoria->folio} - {$asesoria->asunto}",
                    'monto' => $total,
                ]],
                'fecha_emision' => $asesoria->fecha_pago ?? now(),
                'fecha_vencimiento' => ($asesoria->fecha_pago ?? now())->copy()->addDays(30),
                'fecha_pago' => $asesoria->fecha_pago ?? now(),
            ];

            if ($factura) {
                $factura->update($payload);
            } else {
                $factura = Factura::create($payload);
                $asesoria->update(['factura_id' => $factura->id]);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error generando recibo desde listado: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            $this->dispatch('notify-error', 'No se pudo generar el recibo.');
            return;
        }

        $this->dispatch('open-url', ['url' => route('reportes.factura', $factura->id)]);
    }

    public function compartirTarjeta($asesoriaId)
    {
        $asesoria = Asesoria::findOrFail($asesoriaId);

        if (empty($asesoria->public_token)) {
            $asesoria->update(['public_token' => Str::random(48)]);
        }

        $url = route('asesorias.public', $asesoria->public_token);
        $this->dispatch('open-url', ['url' => $url]);
    }

    public function compartirTarjetaWhatsApp($asesoriaId)
    {
        $asesoria = Asesoria::findOrFail($asesoriaId);

        if (empty($asesoria->telefono)) {
            $this->dispatch('notify-error', 'La asesoría no tiene teléfono para compartir por WhatsApp.');
            return;
        }

        if (empty($asesoria->public_token)) {
            $asesoria->update(['public_token' => Str::random(48)]);
        }

        $phone = preg_replace('/\D+/', '', $asesoria->telefono);
        $url = route('asesorias.public', $asesoria->public_token);
        $msg = "Hola, aquí está el comprobante de tu cita de asesoría ({$asesoria->folio}): {$url}";
        $wa = "https://wa.me/{$phone}?text=" . urlencode($msg);

        $this->dispatch('open-url', ['url' => $wa]);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $user = auth()->user();
        $isAdmin = $user->hasRole(['admin', 'super_admin']);
        $canManageBilling = $user->can('manage billing');
        $tenant = $user->tenant;
        $settings = $tenant?->settings ?? [];
        $billingEnabled = (bool) ($settings['asesorias_billing_enabled'] ?? false);
        
        $query = Asesoria::query()
            ->with(['cliente', 'abogado']);

        // Filtros de búsqueda
        if ($this->search) {
            $query->where(function($q) {
                $q->where('nombre_prospecto', 'like', '%' . $this->search . '%')
                  ->orWhere('folio', 'like', '%' . $this->search . '%')
                  ->orWhere('asunto', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filtroEstado) {
            $query->where('estado', $this->filtroEstado);
        }

        if ($this->filtroTipo) {
            $query->where('tipo', $this->filtroTipo);
        }

        if ($this->filtroFecha) {
            if ($this->filtroFecha == 'hoy') {
                $query->whereDate('fecha_hora', Carbon::today());
            } elseif ($this->filtroFecha == 'semana') {
                $query->whereBetween('fecha_hora', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
            } elseif ($this->filtroFecha == 'mes') {
                $query->whereMonth('fecha_hora', Carbon::now()->month);
            }
        }

        // Permisos: Admin ve todas. Los demás solo ven asesorías asignadas a ellos.
        if (!$isAdmin) {
            $query->where('abogado_id', $user->id);
        }

        $asesorias = $query->orderBy('fecha_hora', 'desc')->paginate(10);

        // Estadísticas para tarjetas superiores
        $statsQuery = Asesoria::query();
        if (!$isAdmin) {
            $statsQuery->where('abogado_id', $user->id);
        }

        $stats = [
            'hoy' => (clone $statsQuery)->whereDate('fecha_hora', Carbon::today())->where('estado', 'agendada')->count(),
            'pendientes' => (clone $statsQuery)->where('estado', 'agendada')->count(),
            'realizadas_mes' => (clone $statsQuery)->where('estado', 'realizada')->whereMonth('fecha_hora', Carbon::now()->month)->count(),
        ];

        return view('livewire.asesorias.index', [
            'asesorias' => $asesorias,
            'stats' => $stats,
            'canManageBilling' => $canManageBilling,
            'asesoriasBillingEnabled' => $billingEnabled,
        ]);
    }
}
