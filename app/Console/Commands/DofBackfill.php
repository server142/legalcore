<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DofBackfill extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dof:backfill {year? : The year to backfill, default is 2024} {--end-year= : The end year}';

    protected $description = 'Massively download DOF publications from a starting year until an end year';

    public function handle(\App\Services\DofService $dofService)
    {
        $startYear = (int)($this->argument('year') ?: 2024);
        $startDate = \Carbon\Carbon::createFromDate($startYear, 1, 1);
        
        $endYear = $this->option('end-year');
        $endDate = $endYear ? \Carbon\Carbon::createFromDate((int)$endYear, 12, 31) : now();
        
        if ($endDate->isFuture()) $endDate = now();

        $this->info("Starting massive backfill from {$startDate->toDateString()} to {$endDate->toDateString()}");
        $this->info("This process may take a while. Grab a coffee. ☕");

        $bar = $this->output->createProgressBar($startDate->diffInDays($endDate) + 1);
        $bar->start();

        $totalImported = 0;
        $errors = 0;

        // Iterate day by day
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            // Skip future dates if any drift occurs
            if ($date->isFuture()) break;

            // Skip weekends usually? No, DOF publishes on weekends sometimes or extra editions.
            
            try {
                $count = $dofService->fetchDailyPublications($date);
                $totalImported += $count;
            } catch (\Exception $e) {
                $errors++;
                // Quietly log error but don't stop
                \Illuminate\Support\Facades\Log::error("Backfill error on {$date->toDateString()}: " . $e->getMessage());
            }

            $bar->advance();
            
            // Be nice to the API
            usleep(200000); // 200ms sleep
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("Backfill completed!");
        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Days Processed', $startDate->diffInDays($endDate)],
                ['Total Documents Imported', $totalImported],
                ['Errors/Empty Days', $errors],
            ]
        );
    }
}
