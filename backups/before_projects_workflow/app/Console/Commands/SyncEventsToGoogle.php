<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Evento;
use App\Services\GoogleCalendarService;
use Illuminate\Support\Facades\Log;

class SyncEventsToGoogle extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'google:sync-events {--all : Incluir eventos pasados}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincroniza eventos pendientes con Google Calendar utilizando la Cuenta de Servicio';

    protected $googleService;

    public function __construct(GoogleCalendarService $googleService)
    {
        parent::__construct();
        $this->googleService = $googleService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando sincronización de eventos con Google Calendar...');

        $query = Evento::whereNull('google_event_id');

        if (!$this->option('all')) {
            $query->where('end_time', '>', now());
            $this->info('Filtrando solo eventos futuros (usa --all para incluir pasados).');
        }

        $eventos = $query->get();

        if ($eventos->isEmpty()) {
            $this->info('No hay eventos pendientes de sincronización.');
            return 0;
        }

        $this->info("Se encontraron {$eventos->count()} eventos para sincronizar.");
        $bar = $this->output->createProgressBar($eventos->count());
        $bar->start();

        $successCount = 0;
        $errorCount = 0;

        foreach ($eventos as $evento) {
            try {
                $user = $evento->user;
                
                // Lógica de asistentes similar al Observer
                $attendees = $this->getAttendeesEmails($evento);
                
                $hasServiceAccount = config('services.google.service_account_json') && file_exists(config('services.google.service_account_json'));
                
                if ($hasServiceAccount && $user && !in_array($user->calendar_email ?? $user->email, $attendees)) {
                    $attendees[] = $user->calendar_email ?? $user->email;
                }

                $eventData = [
                    'title' => $evento->titulo,
                    'description' => $evento->descripcion,
                    'start' => $evento->start_time,
                    'end' => $evento->end_time,
                    'attendees' => $attendees,
                ];

                $eventId = $this->googleService->createEvent($user, $eventData);
                
                if ($eventId) {
                    $evento->google_event_id = $eventId;
                    $evento->saveQuietly();
                    $successCount++;
                } else {
                    $errorCount++;
                }
            } catch (\Exception $e) {
                $this->error("\nError sincronizando evento #{$evento->id}: " . $e->getMessage());
                $errorCount++;
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("Sincronización terminada.");
        $this->info("Éxito: {$successCount}");
        $this->error("Errores: {$errorCount}");

        return 0;
    }

    protected function getAttendeesEmails($evento): array
    {
        $targetUsers = collect([]);

        if ($evento->expediente_id && $evento->expediente) {
            if ($evento->expediente->abogado) {
                $targetUsers->push($evento->expediente->abogado);
            }
            
            if ($evento->expediente->assignedUsers) {
                $targetUsers = $targetUsers->merge($evento->expediente->assignedUsers);
            }
        }

        if ($evento->invitedUsers) {
            $targetUsers = $targetUsers->merge($evento->invitedUsers);
        }

        return $targetUsers->unique('id')
            ->reject(function ($u) use ($evento) {
                return $u->id === $evento->user_id;
            })
            ->map(function ($u) {
                return $u->calendar_email ?? $u->email;
            })
            ->filter()
            ->toArray();
    }
}
