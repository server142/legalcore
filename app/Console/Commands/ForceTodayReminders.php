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

            $expedientes = $query->get();

            if ($expedientes->isEmpty()) {
                continue;
            }

            $this->info(" --- Tenant: {$tenant->name} ({$expedientes->count()} expedientes) ---");

            foreach ($expedientes as $expediente) {
                $this->info("Procesando Exp: {$expediente->numero} - Vence: {$expediente->vencimiento_termino}");

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
                    continue;
                }

                foreach ($recipients as $recipient) {
                    // Force send "URGENTE: Vence Hoy"
                    try {
                        Mail::to($recipient->email)->queue(new ExpedienteDeadlineReminder($expediente, $recipient, 'URGENTE: VENCE HOY'));
                        $this->info("   -> Enviado a: {$recipient->email}");
                    } catch (\Exception $e) {
                        $this->error("   -> Error enviando a {$recipient->email}: " . $e->getMessage());
                    }
                }
            }
        }

        $this->info('Proceso forzado completado.');
    }
}
