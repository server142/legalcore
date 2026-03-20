<?php

namespace App\Livewire\Landings\BJCA;

use Livewire\Component;
use App\Models\Asesoria;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Evento;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\AsesoriaConfirmation;
use App\Mail\AsesoriaNotificationAbogado;
use App\Models\CampaignVisit;
use Illuminate\Support\Facades\Request;

class April2026Landing extends Component
{
    const TENANT_ID = 5;
    const LAWYER_ID = 10;
    const DURATION = 60;

    public $nombre;
    public $email;
    public $telefono;
    public $asunto;
    public $fecha;
    public $hora;
    public $success = false;
    public $message = "";
    public $availableSlots = [];
    public $campaniaNombre = '';
    public $campaniaMes = '';
    public $campaniaPoster = '';
    public $favicon;

    protected $rules = [
        'nombre' => 'required|string|min:4|max:255',
        'email' => 'required|email|max:255',
        'telefono' => 'required|string|min:10|max:15',
        'asunto' => 'required|string|min:10|max:2000',
        'fecha' => 'required|date|after_or_equal:2026-04-01|before_or_equal:2026-04-30',
        'hora' => 'required',
    ];

    public function mount()
    {
        $settings = auth()->user()->tenant?->settings ?? [];
        $this->campaniaNombre = $settings['asesorias_campania_nombre'] ?? 'CAMPAÑA ABRIL 2026';
        $this->campaniaMes = $settings['asesorias_campania_mes'] ?? 'Abril 2026';
        $this->campaniaPoster = $settings['asesorias_campania_poster'] ?? 'images/landings/bjca/campaign_april_2026.jpg';

        // Favicon from Tenant Logo
        $tenantLogo = $settings['logo_path'] ?? null;
        $this->favicon = $tenantLogo ? asset('storage/' . $tenantLogo) : asset('favicon.ico');

        $this->fecha = '2026-04-01';
        $this->refreshSlots();

        // Local Statistics Tracking
        try {
            CampaignVisit::create([
                'campaign_name' => $this->campaniaNombre,
                'tenant_id' => self::TENANT_ID,
                'ip_address' => Request::ip(),
                'user_agent' => Request::header('User-Agent'),
                'referer' => Request::header('referer'),
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("Error saving campaign visit: " . $e->getMessage());
        }
    }

    public function updatedFecha()
    {
        $this->refreshSlots();
    }

    public function refreshSlots()
    {
        if (empty($this->fecha)) {
            $this->availableSlots = [];
            return;
        }

        $date = Carbon::parse($this->fecha);
        $this->availableSlots = $this->getAvailableSlots($date);
        
        // Auto-select first slot if current is not available
        if (!in_array($this->hora, array_column($this->availableSlots, 'value'))) {
            $this->hora = $this->availableSlots[0]['value'] ?? null;
        }
    }

    private function getAvailableSlots(Carbon $date): array
    {
        $tenant = Tenant::find(self::TENANT_ID);
        $settings = $tenant?->settings ?? [];

        $workStart = $settings['asesorias_working_hours_start'] ?? '09:00';
        $workEnd = $settings['asesorias_working_hours_end'] ?? '18:00';
        $businessDays = $settings['asesorias_business_days'] ?? ['mon', 'tue', 'wed', 'thu', 'fri'];

        $dayKey = strtolower($date->format('D'));
        $dayKey = substr($dayKey, 0, 3);

        if (!in_array($dayKey, $businessDays, true)) {
            return [];
        }

        $startLimit = (clone $date)->setTimeFromTimeString($workStart);
        $endLimit = (clone $date)->setTimeFromTimeString($workEnd);
        
        // If date is today, start from current time + 2 hours buffer
        if ($date->isToday()) {
            $nowPlusBuffer = now()->addHours(2);
            if ($startLimit->lt($nowPlusBuffer)) {
                $startLimit = $nowPlusBuffer;
                // Round up to next hour
                $startLimit->minute(0)->second(0);
                if ($startLimit->lt($nowPlusBuffer)) {
                    $startLimit->addHour();
                }
            }
        }

        $slots = [];
        $candidate = (clone $startLimit);

        while ($candidate->format('H:i') < $workEnd) {
            $candidateEnd = (clone $candidate)->addMinutes(self::DURATION);
            
            if ($candidateEnd->gt($endLimit)) {
                break;
            }

            if (!$this->hasConflict($candidate, $candidateEnd)) {
                $slots[] = [
                    'label' => $candidate->format('h:i A'),
                    'value' => $candidate->format('H:i'),
                ];
            }

            $candidate->addMinutes(self::DURATION);
        }

        return $slots;
    }

    private function hasConflict(Carbon $start, Carbon $end): bool
    {
        return Evento::where('user_id', self::LAWYER_ID)
            ->where('start_time', '<', $end)
            ->where('end_time', '>', $start)
            ->exists();
    }

    public function selectSlot($value)
    {
        $this->hora = $value;
    }

    public function schedule()
    {
        $this->validate();

        $fechaHora = Carbon::parse($this->fecha . ' ' . $this->hora);
        $end = (clone $fechaHora)->addMinutes(self::DURATION);

        // Re-check conflict to avoid race conditions
        if ($this->hasConflict($fechaHora, $end)) {
            $this->addError('hora', 'Lo sentimos, este horario acaba de ser ocupado. Por favor selecciona otro.');
            $this->refreshSlots();
            return;
        }

        $abogado = User::find(self::LAWYER_ID);

        try {
            DB::beginTransaction();

            $asesoria = Asesoria::create([
                'tenant_id' => self::TENANT_ID,
                'nombre_prospecto' => $this->nombre,
                'email' => $this->email,
                'telefono' => $this->telefono,
                'asunto' => $this->asunto,
                'fecha_hora' => $fechaHora,
                'duracion_minutos' => self::DURATION,
                'abogado_id' => self::LAWYER_ID,
                'tipo' => 'presencial', 
                'estado' => 'agendada',
                'costo' => 0.00,
                'notas' => "[ORIGEN: {$this->campaniaNombre}]\n\n" . 
                          "Detalles adicionales:\n- Nombre: " . $this->nombre . "\n- Teléfono: " . $this->telefono . "\n- Asunto: " . $this->asunto,
            ]);

            // Sync with internal Agenda
            Evento::create([
                'tenant_id' => self::TENANT_ID,
                'user_id' => self::LAWYER_ID,
                'asesoria_id' => $asesoria->id,
                'titulo' => 'Asesoría Gratis Abril (Landing) - ' . $this->nombre,
                'descripcion' => $this->asunto,
                'start_time' => $fechaHora,
                'end_time' => $end,
                'tipo' => 'cita',
                'color' => '#6366f1',
            ]);

            DB::commit();

            try {
                if ($asesoria->email) {
                    Mail::to($asesoria->email)->send(new AsesoriaConfirmation($asesoria));
                }
                if ($abogado && $abogado->email) {
                    Mail::to($abogado->email)->send(new AsesoriaNotificationAbogado($asesoria));
                }
            } catch (\Exception $e) {
                Log::error('Error enviando notificaciones desde Landing: ' . $e->getMessage());
            }

            $this->success = true;
            $this->message = "¡Tu asesoría ha sido agendada con éxito para el " . $fechaHora->format('d/m/Y') . " a las " . $fechaHora->format('H:i') . "! Te contactaremos pronto.";
            $this->reset(['nombre', 'email', 'telefono', 'asunto', 'fecha', 'hora']);
            $this->refreshSlots();

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error en Landing: ' . $e->getMessage());
            $this->addError('general', 'No se pudo agendar. Por favor revisa los datos.');
        }
    }

    public function render()
    {
        return view('pages.bjca.landings.campanias.asesorias.04.2026.index')
            ->layout('layouts.marketing');
    }
}
