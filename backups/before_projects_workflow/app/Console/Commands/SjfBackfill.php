<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SjfService;

class SjfBackfill extends Command
{
    protected $signature = 'sjf:backfill {pages=10}';
    protected $description = 'Fetch multiple pages of SJF Jurisprudencia (50 items per page)';

    public function handle(SjfService $service)
    {
        $pages = $this->argument('pages');
        $this->info("Starting backfill for {$pages} pages...");

        for ($i = 1; $i <= $pages; $i++) {
            $this->info("Processing page {$i}...");
            $result = $service->syncPage($i, 50);
            
            if (is_numeric($result)) {
                $this->info("Imported {$result} items from page {$i}.");
            } else {
                $this->error("Error on page {$i}: " . $result);
            }
            
            // Sleep a bit to avoid hitting rate limits
            sleep(1);
        }

        $this->info('Backfill completed!');
    }
}
