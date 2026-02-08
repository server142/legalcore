<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Expediente;
use App\Mail\WeeklyDeadlineReport;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendWeeklyDeadlineReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'agenda:weekly-report';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Send a weekly summary of upcoming and overdue deadlines to all lawyers and admins.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando generación de reportes semanales de vencimientos...');
        
        $tenants = Tenant::where('is_active', true)->get();
        $twoWeeksAway = Carbon::now()->addDays(14);

        foreach ($tenants as $tenant) {
            $this->info("Procesando despacho: {$tenant->name}");

            // 1. Get all relevant expedientes for this tenant (Overdue or in the next 14 days)
            $allRelevantExp = Expediente::with(['cliente', 'abogado'])
                ->where('tenant_id', $tenant->id)
                ->whereNotNull('vencimiento_termino')
                ->where('vencimiento_termino', '<=', $twoWeeksAway)
                ->orderBy('vencimiento_termino', 'asc')
                ->get();

            if ($allRelevantExp->isEmpty()) {
                $this->info("No hay vencimientos próximos para {$tenant->name}.");
                continue;
            }

            // 2. Get all users of this tenant
            $users = User::where('tenant_id', $tenant->id)->get();

            foreach ($users as $user) {
                if ($user->hasRole('admin')) {
                    // Admins get the full list
                    Mail::to($user->email)->queue(new WeeklyDeadlineReport($user, $allRelevantExp, true));
                    $this->info("Reporte MAESTRO enviado a Admin: {$user->email}");
                } elseif ($user->hasRole('abogado')) {
                    // Lawyers get only where they are responsible or assigned
                    $personaList = $allRelevantExp->filter(function($exp) use ($user) {
                        return $exp->abogado_responsable_id === $user->id || 
                               $exp->assignedUsers->contains('id', $user->id);
                    });

                    if ($personaList->isNotEmpty()) {
                        Mail::to($user->email)->queue(new WeeklyDeadlineReport($user, $personaList, false));
                        $this->info("Reporte PERSONAL enviado a Abogado: {$user->email}");
                    }
                }
            }
        }

        $this->info('Reportes semanales enviados a cola de correos.');
        return 0;
    }
}
