<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SjfService;
use Illuminate\Support\Facades\File;

class SjfImportFile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sjf:import-file {path : Absolute or relative path to JSON file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import SJF/Tesis from a local JSON file (Plan B)';

    /**
     * Execute the console command.
     */
    public function handle(SjfService $sjfService)
    {
        $path = $this->argument('path');

        if (!File::exists($path)) {
            $this->error("File not found: $path");
            return 1;
        }

        $this->info("Reading file...");
        $content = File::get($path);
        
        // Try to decode
        $json = json_decode($content, true);

        if (!$json) {
            $this->error("Invalid JSON format.");
            return 1;
        }

        // Detect format (API or OData style)
        // OData usually has 'value' key. API usually 'result' or 'data' or direct array.
        
        $items = [];
        $source = 'api';

        if (isset($json['value']) && is_array($json['value'])) {
            $this->info("Detected OData format.");
            $items = $json['value'];
            $source = 'odata';
        } elseif (isset($json['result'])) {
             $this->info("Detected JFAPI result format.");
             $items = $json['result'];
        } elseif (isset($json['data'])) {
             $items = $json['data'];
        } elseif (is_array($json)) {
             // Direct array check
             if (isset($json[0])) {
                $this->info("Detected direct array.");
                $items = $json;
             }
        }

        if (empty($items)) {
            $this->error("Could not find array of Items in the file.");
            return 1;
        }

        $this->info("Processing " . count($items) . " items...");
        
        $count = $sjfService->processItems($items, $source);
        
        $this->info("Done! Imported: $count");
        return 0;
    }
}
