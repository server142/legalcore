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
