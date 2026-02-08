<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('dof:fetch {date?}', function (\App\Services\DofService $service, $date = null) {
    $date = $date ?? now()->format('Y-m-d');
    $this->info("Fetching DOF publications for {$date}...");
    
    $count = $service->fetchDailyPublications($date);
    
    $this->info("Done. Imported {$count} new publications.");
})->purpose('Fetch publications from Diaro Oficial de la Federación')->dailyAt('06:00');

Artisan::command('sjf:sync-daily', function () {
    $this->info("Triggering SJF daily sync...");
    Artisan::call('sjf:sync', ['--days' => 2]);
    $this->info("SJF Sync completed.");
})->purpose('Sync recent Jurisprudencia tesis from SCJN')->dailyAt('07:00');

// AUTOMATION: Generate embeddings in batches every hour to complete history
Artisan::command('dof:embeddings-auto', function () {
    $this->info("Procesando lote horario de embeddings...");
    Artisan::call('dof:embeddings', ['--limit' => 2000, '--order' => 'desc']);
})->purpose('Process DOF embeddings in background batches')->hourly();

Artisan::command('sjf:embeddings-auto', function () {
    $this->info("Procesando remanente de embeddings SJF...");
    Artisan::call('sjf:embeddings', ['--limit' => 500]);
})->purpose('Process SJF embeddings in background batches')->hourly();

// Check for upcoming agenda events every hour
\Illuminate\Support\Facades\Schedule::command('agenda:check-reminders')->hourly();

// Send weekly deadline report every Monday at 8:00 AM
\Illuminate\Support\Facades\Schedule::command('agenda:weekly-report')->mondays()->at('08:00');
