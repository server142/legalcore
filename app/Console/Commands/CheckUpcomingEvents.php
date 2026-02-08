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
            }
        }

        $this->info('Proceso de recordatorios finalizado.');
        return 0;
    }

    protected function checkExpedienteDeadlinesForTenant(Tenant $tenant, int $hours, string $label)
    {
        $start = Carbon::now()->addHours($hours);
        $end = Carbon::now()->addHours($hours)->addHour();

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
                Mail::to($recipient->email)->queue(new \App\Mail\ExpedienteDeadlineReminder($expediente, $recipient, $label));
                $this->info("Notificación FATAL de {$label} enviada a [{$tenant->name}] {$recipient->email} para Exp: {$expediente->numero}");
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
            if (!$expediente) continue;

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
            }
        }
    }
}
