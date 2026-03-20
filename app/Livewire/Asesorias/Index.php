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
    public $filtroOrigen = '';
    public $showCampaignModal = false;
    public $campaniaNombre = '';
    public $campaniaMes = '';
    public $campaniaPoster = '';

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

    public function edit($asesoriaId)
    {
        $user = auth()->user();
        $isAdmin = $user->hasRole(['admin', 'super_admin']);
        $asesoria = Asesoria::findOrFail($asesoriaId);

        // Solo el creador (abogado_id) o admin pueden editar
        if (!$isAdmin && $asesoria->abogado_id !== $user->id) {
            $this->dispatch('notify-error', 'No tienes permisos para editar esta asesoría.');
            return;
        }

        return redirect()->route('asesorias.edit', $asesoriaId);
    }

    public function delete($asesoriaId)
    {
        $user = auth()->user();
        $isAdmin = $user->hasRole(['admin', 'super_admin']);
        $asesoria = Asesoria::findOrFail($asesoriaId);

        // Solo el creador (abogado_id) o admin pueden eliminar
        if (!$isAdmin && $asesoria->abogado_id !== $user->id) {
            $this->dispatch('notify-error', 'No tienes permisos para eliminar esta asesoría.');
            return;
        }

        try {
            $asesoria->delete();
            $this->dispatch('notify', 'Asesoría eliminada correctamente.');
        } catch (\Exception $e) {
            $this->dispatch('notify-error', 'No se pudo eliminar la asesoría.');
        }
    }

    public function saveCampaignConfig()
    {
        $tenant = auth()->user()->tenant;
        $settings = $tenant->settings ?? [];
        
        $settings['asesorias_campania_nombre'] = $this->campaniaNombre;
        $settings['asesorias_campania_mes'] = $this->campaniaMes;
        $settings['asesorias_campania_poster'] = $this->campaniaPoster;
        
        $tenant->settings = $settings;
        $tenant->save();
        
        $this->showCampaignModal = false;
        $this->dispatch('notify', 'Configuración de campaña actualizada correctamente.');
    }

    public function openCampaignModal()
    {
        $settings = auth()->user()->tenant?->settings ?? [];
        $this->campaniaNombre = $settings['asesorias_campania_nombre'] ?? 'CAMPAÑA ABRIL 2026';
        $this->campaniaMes = $settings['asesorias_campania_mes'] ?? 'Abril 2026';
        $this->campaniaPoster = $settings['asesorias_campania_poster'] ?? 'images/landings/bjca/campaign_april_2026.jpg';
        $this->showCampaignModal = true;
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
                  ->orWhere('asunto', 'like', '%' . $this->search . '%')
                  ->orWhere('notas', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filtroOrigen) {
            if ($this->filtroOrigen === 'CAMPAÑA ABRIL 2026') {
                $query->where(function($q) {
                    $q->where('notas', 'like', '%[ORIGEN: CAMPAÑA ABRIL 2026]%')
                      ->orWhere('notas', 'like', '%AGENDADA DESDE LANDING CAMPAÑA ABRIL 2026%');
                });
            } else {
                $query->where('notas', 'like', "%[ORIGEN: " . $this->filtroOrigen . "]%");
            }
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
            'campania_total' => (clone $statsQuery)->where('notas', 'like', '%LANDING CAMPAÑA ABRIL 2026%')->count(),
        ];

        // Obtener origenes únicos para el filtro
        $origenesDisponibles = Asesoria::query()
            ->where('tenant_id', $user->tenant_id)
            ->where('notas', 'like', '%[ORIGEN: %')
            ->get()
            ->map(function($a) {
                if (preg_match('/\[ORIGEN: (.*?)\]/', $a->notas, $matches)) {
                    return $matches[1];
                }
                return null;
            })
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        // Agregar legado si existe
        if (Asesoria::where('tenant_id', $user->tenant_id)->where('notas', 'like', '%LANDING CAMPAÑA ABRIL 2026%')->exists()) {
            if (!in_array('CAMPAÑA ABRIL 2026', $origenesDisponibles)) {
                $origenesDisponibles[] = 'CAMPAÑA ABRIL 2026';
            }
        }

        return view('livewire.asesorias.index', [
            'asesorias' => $asesorias,
            'stats' => $stats,
            'canManageBilling' => $canManageBilling,
            'asesoriasBillingEnabled' => $billingEnabled,
            'isAdmin' => $isAdmin,
            'currentUserId' => $user->id,
            'origenesDisponibles' => $origenesDisponibles,
        ]);
    }
}
