<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SmsService;

class CheckTerminosLegales extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-terminos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Revisa los términos legales próximos a vencer y envía notificaciones SMS';

    /**
     * Execute the console command.
     */
    public function handle(SmsService $smsService)
    {
        $this->info('Iniciando revisión de términos legales...');
        $smsService->checkAndSendNotifications();
        $this->info('Revisión completada.');
    }
}
