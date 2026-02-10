<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\DofPublication;
use Carbon\Carbon;
use DOMDocument;
use DOMXPath;

class FetchDofHistory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dof:fetch-history {--days=30 : Number of days to look back} {--start-date= : Start date (YYYY-MM-DD)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recover DOF history (Lightweight mode: Titles and Links only)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->option('days');
        $startDateInput = $this->option('start-date');
        
        $date = $startDateInput ? Carbon::parse($startDateInput) : Carbon::yesterday();
        
        $this->info("Starting DOF recovery for {$days} days starting from " . $date->format('Y-m-d'));

        for ($i = 0; $i < $days; $i++) {
            $currentDate = $date->copy()->subDays($i);
            
            // Skip weekends if you want, but DOF sometimes publishes
            
            $url = "https://dof.gob.mx/index.php?year={$currentDate->year}&month={$currentDate->month}&day={$currentDate->day}";
            
            $this->line("[{$currentDate->format('Y-m-d')}] Fetching index... ($url)");

            try {
                $response = Http::withUserAgent('Mozilla/5.0 (compatible; LegalCoreBot/1.0)')->get($url);
                
                if ($response->successful()) {
                    $this->parseAndStore($response->body(), $currentDate);
                } else {
                    $this->error("Failed to fetch {$url}: " . $response->status());
                }

                // Sleep to be polite
                sleep(1); 

            } catch (\Exception $e) {
                $this->error("Error fetching {$currentDate->format('Y-m-d')}: " . $e->getMessage());
            }
        }
    }

    private function parseAndStore($html, $date)
    {
        // Suppress warnings for malformed HTML
        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        $dom->loadHTML($html);
        libxml_clear_errors();
        
        $xpath = new DOMXPath($dom);
        
        // Find links to notes: href="nota_detalle.php?codigo=..."
        // The structure usually is within tables with class 'Tabla_borde' or links style 'enlaces'
        $links = $xpath->query('//a[contains(@href, "nota_detalle.php?codigo=")]');
        
        $count = 0;

        foreach ($links as $link) {
            $href = $link->getAttribute('href');
            $title =  trim($link->nodeValue);
            
            // Extract code
            parse_str(parse_url($href, PHP_URL_QUERY), $queryParams);
            $codNota = $queryParams['codigo'] ?? null;
            
            if (!$codNota || empty($title)) continue;

            // Optional: Try to find Organismo/Seccion from previous elements
            // This is tricky with pure DOM and no visual context, keeping it simple for now.
            // We save pure metadata.

            DofPublication::updateOrCreate(
                ['cod_nota' => $codNota],
                [
                    'fecha_publicacion' => $date->format('Y-m-d'),
                    'titulo' => $title,
                    'link_pdf' => "https://dof.gob.mx/nota_detalle.php?codigo={$codNota}&fecha=" . $date->format('d/m/Y'),
                    'texto_completo' => null, // CRITICAL: Save space
                    'resumen' => null,        // Save space
                    'embedding_data' => null // Save space
                ]
            );
            $count++;
        }
        
        $this->info("  -> Saved/Updated {$count} publications.");
    }
}
