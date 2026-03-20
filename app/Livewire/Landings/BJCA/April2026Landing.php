<?php

namespace App\Livewire\Landings\BJCA;

use Livewire\Component;
use App\Models\Asesoria;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\AsesoriaConfirmation;
use App\Mail\AsesoriaNotificationAbogado;

class April2026Landing extends Component
{
    // Tenant ID for "Bufete Jurídico & Consultores Asociados"
    const TENANT_ID = 5;

    // Form fields
    public $nombre;
    public $email;
    public $telefono;
    public $asunto;
    public $fecha;
    public $hora;
    public $success = false;
    public $message = "";

    protected $rules = [
        'nombre' => 'required|string|min:4|max:255',
        'email' => 'required|email|max:255',
        'telefono' => 'required|string|min:10|max:15',
        'asunto' => 'required|string|min:10|max:2000',
        'fecha' => 'required|date|after_or_equal:today',
        'hora' => 'required',
    ];

    public function mount()
    {
        $this->fecha = Carbon::today()->addDay()->format('Y-m-d');
        $this->hora = '10:00';
    }

    public function schedule()
    {
        $this->validate();

        $fechaHora = Carbon::parse($this->fecha . ' ' . $this->hora);

        // Assign to a lawyer of this tenant
        // Prefer Carlos Segura (ID 10) if exists, else first available
        $abogado = User::where('tenant_id', self::TENANT_ID)
            ->whereHas('roles', fn($q) => $q->where('name', 'abogado'))
            ->find(10) ?? User::where('tenant_id', self::TENANT_ID)
            ->whereHas('roles', fn($q) => $q->where('name', 'abogado'))
            ->first();

        if (!$abogado) {
             // Fallback to admin
             $abogado = User::where('tenant_id', self::TENANT_ID)->first();
        }

        try {
            DB::beginTransaction();

            $asesoria = Asesoria::create([
                'tenant_id' => self::TENANT_ID,
                'nombre_prospecto' => $this->nombre,
                'email' => $this->email,
                'telefono' => $this->telefono,
                'asunto' => $this->asunto,
                'fecha_hora' => $fechaHora,
                'duracion_minutos' => 60, // 1 hour per campaign rules
                'abogado_id' => $abogado?->id,
                'tipo' => 'presencial', 
                'estado' => 'agendada',
                'costo' => 0.00,
                'notas' => "AGENDADA DESDE LANDING CAMPAÑA ABRIL 2026 (GRATIS).\n\nDetalles adicionales:\n- Nombre: " . $this->nombre . "\n- Teléfono: " . $this->telefono . "\n- Asunto: " . $this->asunto,
            ]);

            DB::commit();

            // Notifications
            try {
                if ($asesoria->email) {
                    Mail::to($asesoria->email)->send(new AsesoriaConfirmation($asesoria));
                }
                if ($abogado && $abogado->email) {
                    Mail::to($abogado->email)->send(new AsesoriaNotificationAbogado($asesoria));
                }
            } catch (\Exception $e) {
                Log::error('Error enviando notificaciones desde Landing April 2026: ' . $e->getMessage());
            }

            $this->success = true;
            $this->message = "¡Tu asesoría ha sido agendada con éxito! Te contactaremos pronto para confirmar los detalles.";
            $this->reset(['nombre', 'email', 'telefono', 'asunto', 'fecha', 'hora']);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error en Landing April 2026: ' . $e->getMessage());
            $this->addError('general', 'Lo sentimos, hubo un error al procesar tu solicitud. Por favor intenta de nuevo.');
        }
    }

    public function render()
    {
        return view('pages.bjca.landings.campanias.asesorias.04.2026.index')
            ->layout('layouts.marketing');
    }
}
