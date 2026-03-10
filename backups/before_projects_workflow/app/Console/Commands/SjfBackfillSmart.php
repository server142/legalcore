<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SjfService;

class SjfBackfillSmart extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'sjf:backfill-smart {year? : Target year} {--all : Loop through years 2010 to 2026}';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Fetch SJF Jurisprudencia filtered by year to bypass the 600-page limit';

    /**
     * Execute the console command.
     */
    public function handle(SjfService $service)
    {
        $years = [];
        
        if ($this->option('all')) {
            $years = range(2026, 2010); // Sync backwards from today
        } elseif ($this->argument('year')) {
            $years = [(int)$this->argument('year')];
        } else {
            $this->error("Please specify a year or use --all");
            return 1;
        }

        foreach ($years as $year) {
            $this->info("=== Starting Sync for Year: {$year} ===");
            
            // The SCJN API uses expression for filtering. 
            // Based on reverse engineering, year filter often looks like this in their internal expression:
            $filters = [
                'expression' => "anio:{$year}" // This is a common pattern for their ElasticSearch backend
            ];

            // Alternatively, use search terms if expression doesn't work
            // $filters = ['search_terms' => ["anio:{$year}"]];

            $page = 1;
            $maxPages = 600; // Safe limit per filter
            $hasMore = true;

            while ($hasMore && $page <= $maxPages) {
                $this->info("Processing Year {$year} | Page {$page}...");
                
                $result = $service->syncPage($page, 50, $filters);
                
                if (is_numeric($result)) {
                    $this->info("Success: Imported {$result} items.");
                    
                    // If we got 0 items on page 1, the filter might be wrong or no data for that year
                    if ($result == 0 && $page == 1) {
                        $this->warn("No items found for year {$year}. Check filter format.");
                        $hasMore = false;
                    }
                    
                    // If we got less than the page size, we reached the end for this year
                    if ($result < 50) {
                        $this->info("Reached end of data for year {$year}.");
                        $hasMore = false;
                    }
                } else {
                    $this->error("Error on Year {$year} | Page {$page}: " . $result);
                    $hasMore = false;
                }

                $page++;
                sleep(1); // Anti-block delay
            }
        }

        $this->info('Smart Backfill completed!');
        return 0;
    }
}
