<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Evento;
use App\Models\User;
use App\Models\Tenant;
use App\Mail\AgendaReminder;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class CheckUpcomingEvents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'agenda:check-reminders';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Check for upcoming events and send email reminders based on per-tenant settings.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Load custom mail settings from DB (Global Settings)
        \App\Services\MailSettingsService::applySettings();

        $this->info('Consultando eventos próximos a vencer por Tenant...');

        // Defaults if no settings are present
        $defaultReminders = [
            ['label' => '5 días', 'hours' => 120],
            ['label' => '3 días', 'hours' => 72],
            ['label' => '24 horas', 'hours' => 24],
            ['label' => '12 horas', 'hours' => 12],
        ];

        $tenants = Tenant::where('is_active', true)->get();

        foreach ($tenants as $tenant) {
            $this->info("Procesando Tenant: {$tenant->name} (ID: {$tenant->id})");
            
            // Get tenant specific reminders or use defaults
            $reminders = data_get($tenant->settings, 'reminder_intervals', $defaultReminders);

            foreach ($reminders as $reminder) {
                $this->checkThresholdForTenant($tenant, $reminder['hours'], $reminder['label']);
                $this->checkExpedienteDeadlinesForTenant($tenant, $reminder['hours'], $reminder['label']);
                $this->checkActuacionDeadlinesForTenant($tenant, $reminder['hours'], $reminder['label']);
            }
        }

        $this->info('Proceso de recordatorios finalizado.');
        \Illuminate\Support\Facades\Log::info('Agenda Reminder Check Completed', [
            'server_time_now' => Carbon::now()->toDateTimeString(),
            'timezone' => config('app.timezone'),
        ]);
        return 0;
    }

    protected function checkActuacionDeadlinesForTenant(Tenant $tenant, int $hours, string $label)
    {
        // Calcular el día objetivo
        $targetDate = Carbon::now()->addHours($hours);
        $start = $targetDate->copy()->startOfDay();
        $end = $targetDate->copy()->endOfDay();

        \Illuminate\Support\Facades\Log::debug("Checking Actuaciones (Terms) for Tenant {$tenant->id} ({$label})", [
            'target_day' => $targetDate->toDateString()
        ]);

        // Buscar actuaciones que sean plazos y venzan en ese día
        $actuaciones = \App\Models\Actuacion::with(['expediente.assignedUsers', 'expediente.abogado'])
            ->where('tenant_id', $tenant->id)
            ->where('es_plazo', true)
            ->whereBetween('fecha_vencimiento', [$start, $end])
            ->get();

        foreach ($actuaciones as $actuacion) {
            $expediente = $actuacion->expediente;
            if (!$expediente) continue;

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

            foreach ($recipients as $recipient) {
                $cacheKey = "actuacion_reminder_{$actuacion->id}_{$label}_{$targetDate->toDateString()}_{$recipient->id}";
                
                if (!\Illuminate\Support\Facades\Cache::has($cacheKey)) {
                    // Usamos el mismo Mailable pero adaptando el asunto/mensaje
                    // O creamos uno nuevo. Por simplicidad, reusamos ExpedienteDeadlineReminder
                    // pasando un Label modificado para que se entienda que es un TÉRMINO específico.
                    
                    $customLabel = "TÉRMINO: '{$actuacion->titulo}' ({$label})";
                    
                    Mail::to($recipient->email)->queue(new \App\Mail\ExpedienteDeadlineReminder($expediente, $recipient, $customLabel));
                    
                    \Illuminate\Support\Facades\Cache::put($cacheKey, true, now()->addHours(24));
                    $this->info("   [DEBUG] Notificación de TÉRMINO {$label} enviada a {$recipient->email} (Exp: {$expediente->numero})");
                    
                    sleep(1); // Rate Limit
                } else {
                     $this->info("   [DEBUG] Skipping Term {$label} (Already sent)");
                }
            }
        }
    }

    protected function checkExpedienteDeadlinesForTenant(Tenant $tenant, int $hours, string $label)
    {
        // Calcular el día objetivo basado en las horas de anticipación
        // Ejemplo: Si hours=24, target es mañana. Si hours=72, target es en 3 días.
        $targetDate = Carbon::now()->addHours($hours);
        
        // Ventana de todo el día (00:00:00 a 23:59:59)
        $start = $targetDate->copy()->startOfDay();
        $end = $targetDate->copy()->endOfDay();

        \Illuminate\Support\Facades\Log::debug("Checking Expedientes for Tenant {$tenant->id} ({$label})", [
            'target_date' => $targetDate->toDateString(),
            'window_start' => $start->toDateTimeString(),
            'window_end' => $end->toDateTimeString()
        ]);

        $expedientes = \App\Models\Expediente::with(['assignedUsers', 'abogado'])
            ->where('tenant_id', $tenant->id)
            ->whereBetween('vencimiento_termino', [$start, $end])
            ->get();

        foreach ($expedientes as $expediente) {
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

            foreach ($recipients as $recipient) {
                // Evitar duplicados diarios: Podrías usar cache key aquí, pero por ahora confiamos en que el schedule corre 1 vez/día o el usuario sabe que puede recibir doble si corre manual.
                // Idealmente: Cache::add("reminder_{$expediente->id}_{$label}_{$targetDate->toDateString()}", true, 86400)
                
                $cacheKey = "expediente_reminder_{$expediente->id}_{$label}_{$targetDate->toDateString()}_{$recipient->id}";
                
                if (!\Illuminate\Support\Facades\Cache::has($cacheKey)) {
                    Mail::to($recipient->email)->queue(new \App\Mail\ExpedienteDeadlineReminder($expediente, $recipient, $label));
                    \Illuminate\Support\Facades\Cache::put($cacheKey, true, now()->addHours(24)); // Evitar re-envío por 24h
                    $this->info("   [DEBUG] Notificación de {$label} enviada a [{$tenant->name}] {$recipient->email} para Exp: {$expediente->numero}");
                    
                    // Rate Limit Protection for Resend (Free Tier: 2 req/sec)
                    sleep(1);
                } else {
                     $this->info("   [DEBUG] Skipping {$label} for Exp: {$expediente->numero} (Already sent today)");
                }
            }
        }
    }

    protected function checkThresholdForTenant(Tenant $tenant, int $hours, string $label)
    {
        $start = Carbon::now()->addHours($hours);
        $end = Carbon::now()->addHours($hours)->addHour(); // Window of 1 hour

        // SECURITY: We only fetch events belonging to THIS tenant
        $events = Evento::with(['expediente.assignedUsers', 'expediente.abogado'])
            ->where('tenant_id', $tenant->id)
            ->whereBetween('start_time', [$start, $end])
            ->get();

        foreach ($events as $event) {
            $expediente = $event->expediente;

            // CASO 1: Evento vinculado a Expediente
            if ($expediente) {
                // DOUBLE SECURITY CHECK: Ensure expediente tenant matches tenant being processed
                if ($expediente->tenant_id !== $tenant->id) {
                    continue;
                }

                $recipients = collect();
                
                // Abogado Responsable
                if ($expediente->abogado_responsable_id) {
                    $responsible = User::find($expediente->abogado_responsable_id);
                    // Ensure recipient belongs to the same tenant
                    if ($responsible && $responsible->tenant_id === $tenant->id) {
                        $recipients->push($responsible);
                    }
                }

                // Colaboradores
                foreach ($expediente->assignedUsers as $user) {
                    if ($user->tenant_id === $tenant->id) {
                        $recipients->push($user);
                    }
                }

                $recipients = $recipients->unique('id')->filter();

                foreach ($recipients as $recipient) {
                    Mail::to($recipient->email)->queue(new AgendaReminder($event, $recipient, $label));
                    $this->info("Notificación de {$label} enviada a [{$tenant->name}] {$recipient->email} para: {$event->titulo}");
                    sleep(1);
                }
            } 
            // CASO 2: Evento de Asesoría (Sin Expediente aún) o Evento Personal
            else {
                 $recipient = null;
                 if ($event->user_id) {
                    $recipient = User::find($event->user_id);
                 }

                 if ($recipient && $recipient->tenant_id === $tenant->id) {
                    Mail::to($recipient->email)->queue(new AgendaReminder($event, $recipient, $label));
                    $this->info("Notificación de {$label} enviada a [{$tenant->name}] {$recipient->email} para: {$event->titulo}");
                    sleep(1);
                 }
            }
        }
    }
}
