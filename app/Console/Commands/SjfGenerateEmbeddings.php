<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SjfPublication;
use App\Services\AIService;
use Illuminate\Support\Facades\Log;

class SjfGenerateEmbeddings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sjf:embeddings {--limit=1000 : Number of records to process in this run}';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Generate AI embeddings for SJF publications that don\'t have them yet.';

    /**
     * Execute the console command.
     */
    public function handle(AIService $aiService)
    {
        $limit = (int)$this->option('limit');
        $totalMissing = SjfPublication::whereNull('embedding_data')->count();

        if ($totalMissing === 0) {
            $this->info("¡Todos los registros del SJF ya tienen su huella de IA! ✨");
            return 0;
        }

        $this->info("Se detectaron {$totalMissing} registros de Jurisprudencia sin huella de IA.");
        $publications = SjfPublication::whereNull('embedding_data')->limit($limit)->get();

        $bar = $this->output->createProgressBar($publications->count());
        $bar->start();

        foreach ($publications as $pub) {
            try {
                $textToEmbed = $pub->rubro . "\n" . $pub->texto;
                $embedding = $aiService->getEmbeddings($textToEmbed);
                if ($embedding) {
                    $pub->embedding_data = $embedding;
                    $pub->save();
                }
            } catch (\Exception $e) {
                Log::error("SJF Embedding Error {$pub->id}: " . $e->getMessage());
            }
            $bar->advance();
            usleep(100000);
        }

        $bar->finish();
        $this->info("\nProceso terminado.");
        return 0;
    }
}
