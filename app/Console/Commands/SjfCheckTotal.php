<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SjfCheckTotal extends Command
{
    protected $signature = 'sjf:check-total';
    protected $description = 'Check total available tesis in SCJN';

    public function handle()
    {
        $apiUrl = 'https://sjf2.scjn.gob.mx/services/sjftesismicroservice/api/public/tesis';
        
        $this->info("Checking SCJN API for total count...");

        $payload = [
            "classifiers" => [],
            "searchTerms" => [],
            "bFacet" => true,
            "ius" => [],
            "idApp" => "SJFAPP2020",
            "lbSearch" => [],
            "filterExpression" => ""
        ];

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ])
                ->post($apiUrl . "?page=1&size=1", $payload);

            if ($response->successful()) {
                $json = $response->json();
                $total = $json['totalElements'] ?? $json['total_elements'] ?? $json['total'] ?? 'Unknown';
                $this->info("Total Elements reported by SCJN: " . $total);
                
                if (is_numeric($total) && $total > 30000) {
                    $this->warn("The SCJN has more than 30,000 items. If your sync stopped, it might be due to a technical error.");
                } else {
                    $this->info("The current count in your VPS seems close to the total available or the specific filter limit.");
                }
            } else {
                $this->error("API returned error: " . $response->status());
            }
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
        }
    }
}
