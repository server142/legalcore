<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Evento;
use App\Models\User;
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
    protected $description = 'Check for upcoming events and send email reminders (5d, 3d, 24h, 12h).';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Consultando eventos próximos a vencer...');

        $reminders = [
            ['label' => '5 días', 'hours' => 120],
            ['label' => '3 días', 'hours' => 72],
            ['label' => '24 horas', 'hours' => 24],
            ['label' => '12 horas', 'hours' => 12],
        ];

        foreach ($reminders as $reminder) {
            $this->checkThreshold($reminder['hours'], $reminder['label']);
        }

        $this->info('Proceso de recordatorios finalizado.');
        return 0;
    }

    protected function checkThreshold(int $hours, string $label)
    {
        $start = Carbon::now()->addHours($hours);
        $end = Carbon::now()->addHours($hours)->addHour(); // Captura ventana de 1 hora

        $events = Evento::with(['expediente.assignedUsers', 'expediente.abogado'])
            ->whereBetween('start_time', [$start, $end])
            ->get();

        foreach ($events as $event) {
            $expediente = $event->expediente;
            if (!$expediente) continue;

            $recipients = collect();
            
            // Abogado Responsable
            if ($expediente->abogado_responsable_id) {
                $recipients->push(User::find($expediente->abogado_responsable_id));
            }

            // Colaboradores
            foreach ($expediente->assignedUsers as $user) {
                $recipients->push($user);
            }

            $recipients = $recipients->unique('id')->filter();

            foreach ($recipients as $recipient) {
                Mail::to($recipient->email)->queue(new AgendaReminder($event, $recipient, $label));
                $this->info("Notificación de {$label} enviada a {$recipient->email} para el evento: {$event->titulo}");
            }
        }
    }
}
