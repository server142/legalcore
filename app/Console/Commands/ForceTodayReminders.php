<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;
use App\Models\Expediente;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use App\Mail\ExpedienteDeadlineReminder;

class ForceTodayReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'agenda:force-today {--id= : Expediente ID or keyword to limit search}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fuerza el envío de recordatorios para expedientes que vencen HOY, sin importar la hora.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Load custom mail settings from DB (Global Settings)
        \App\Services\MailSettingsService::applySettings();

        $this->info('Iniciando búsqueda de vencimientos para HOY (' . date('Y-m-d') . ')...');

        $tenants = Tenant::where('is_active', true)->get();
        $todayStart = Carbon::today();
        $todayEnd = Carbon::tomorrow()->subSecond(); // 23:59:59

        foreach ($tenants as $tenant) {
            $query = Expediente::with(['assignedUsers', 'abogado'])
                ->where('tenant_id', $tenant->id)
                ->whereBetween('vencimiento_termino', [$todayStart, $todayEnd]);

            // Filter specific ID if passed
            if ($this->option('id')) {
                $filter = $this->option('id');
                $query->where(function ($q) use ($filter) {
                    $q->where('id', $filter)
                      ->orWhere('numero', 'like', "%{$filter}%")
                      ->orWhere('titulo', 'like', "%{$filter}%");
                });
            }

            // 1. Check Expedientes (Existing Logic)
            $expedientes = $query->get();

            if ($expedientes->isNotEmpty()) {
                $this->info(" --- Tenant: {$tenant->name} ({$expedientes->count()} expedientes) ---");

                foreach ($expedientes as $expediente) {
                    $this->info("Procesando Exp: {$expediente->numero} - Vence: {$expediente->vencimiento_termino}");
                    $this->sendReminder($expediente, $tenant, 'URGENTE: EXPEDIENTE VENCE HOY');
                }
            }

            // 2. Check Actuaciones (Legal Terms) - NEW LOGIC
            $actuacionesQuery = \App\Models\Actuacion::with(['expediente.assignedUsers', 'expediente.abogado'])
                ->where('tenant_id', $tenant->id)
                ->where('es_plazo', true) // Only terms
                ->whereDate('fecha_vencimiento', $todayStart->toDateString()); // Only today

            // Filter specific ID if passed (searches related expediente number)
            if ($this->option('id')) {
                $filter = $this->option('id');
                $actuacionesQuery->whereHas('expediente', function ($q) use ($filter) {
                    $q->where('id', $filter)
                      ->orWhere('numero', 'like', "%{$filter}%");
                });
            }

            $actuaciones = $actuacionesQuery->get();

            if ($actuaciones->isNotEmpty()) {
                $this->info(" --- Tenant: {$tenant->name} ({$actuaciones->count()} actuaciones/términos) ---");
                
                foreach ($actuaciones as $actuacion) {
                    $this->info("Procesando Término: '{$actuacion->titulo}' (Exp: {$actuacion->expediente->numero}) - Vence: {$actuacion->fecha_vencimiento->format('Y-m-d')}");
                    // Reusing the same mail logic, passing the actuacion context
                    $this->sendReminder($actuacion->expediente, $tenant, "URGENTE: TÉRMINO '{$actuacion->titulo}' VENCE HOY");
                }
            }
        }

        $this->info('Proceso forzado completado.');
    }

    protected function sendReminder($expediente, $tenant, $subject)
    {
        $recipients = collect();
        
        if ($expediente->abogado_responsable_id) {
            $responsible = User::find($expediente->abogado_responsable_id);
            if ($responsible && $responsible->tenant_id === $tenant->id) {
                $recipients->push($responsible);
            }
        }

        foreach ($expediente->assignedUsers as $user) {
            if ($user->tenant_id === $tenant->id) {
                $recipients->push($user);
            }
        }

        $recipients = $recipients->unique('id')->filter();
        
        if ($recipients->isEmpty()) {
            $this->warn("   -> Sin destinatarios válidos.");
            return;
        }

        if ($recipients->isEmpty()) {
            $this->warn("   -> Sin destinatarios válidos.");
            return;
        }

        foreach ($recipients as $recipient) {
            try {
                // Use the working Mailable class approach
                Mail::to($recipient->email)->send(new ExpedienteDeadlineReminder($expediente, $recipient, $subject));
                $this->info("   -> Enviado a: {$recipient->email}");
                
                // Rate Limit Protection for Resend (Free Tier: 2 req/sec)
                sleep(1); 
            } catch (\Exception $e) {
                $this->error("   -> ERROR SMTP enviando a {$recipient->email}: " . $e->getMessage());
                \Illuminate\Support\Facades\Log::error("Mail Send Error: " . $e->getMessage());
            }
        }
    }
}
