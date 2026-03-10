<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SjfService;

class SjfSync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sjf:sync {--days=7 : Days to look back}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch latest Tesis from Semanario Judicial (SCJN)';

    /**
     * Execute the console command.
     */
    public function handle(SjfService $sjfService)
    {
        $this->info("Connecting to SCJN (Semanario)...");
        
        $days = (int) $this->option('days');
        
        // Logic to attempt fetch
        try {
            $result = $sjfService->syncRecent($days);
            
            if (is_numeric($result)) {
                $this->info("Success! Imported {$result} items.");
                return 0;
            } else {
                // It returned an error string or false
                $this->error("Sync Failed: " . ($result ?: 'Unknown Error'));
                return 1;
            }
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}
