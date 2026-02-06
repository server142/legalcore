<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DofPublication;
use App\Services\AIService;
use Illuminate\Support\Facades\Log;

class DofGenerateEmbeddings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dof:embeddings {--limit=1000 : Number of records to process in this run} {--order=desc : Order to process records (desc for recent first, asc for oldest first)}';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Generate AI embeddings for DOF publications that don\'t have them yet.';

    /**
     * Execute the console command.
     */
    public function handle(AIService $aiService)
    {
        $limit = (int)$this->option('limit');
        $order = $this->option('order') === 'asc' ? 'asc' : 'desc';

        $totalMissing = DofPublication::whereNull('embedding_data')->count();

        if ($totalMissing === 0) {
            $this->info("¡Todos los registros del DOF ya tienen su huella de IA! ✨");
            return 0;
        }

        $this->info("Se detectaron {$totalMissing} registros sin huella de IA.");
        $this->info("Procesando los próximos {$limit} registros (Orden: {$order})...");

        $publications = DofPublication::whereNull('embedding_data')
            ->orderBy('fecha_publicacion', $order)
            ->limit($limit)
            ->get();

        if ($publications->isEmpty()) {
            $this->warn("No hay registros pendientes para procesar.");
            return 0;
        }

        $bar = $this->output->createProgressBar($publications->count());
        $bar->start();

        $success = 0;
        $failed = 0;

        foreach ($publications as $pub) {
            try {
                // We embed the title and summary
                $textToEmbed = $pub->titulo . "\n" . $pub->resumen;
                
                // Clean the text a bit
                $textToEmbed = mb_convert_encoding($textToEmbed, 'UTF-8', 'UTF-8');
                $textToEmbed = strip_tags($textToEmbed);

                $embedding = $aiService->getEmbeddings($textToEmbed);

                if ($embedding && is_array($embedding)) {
                    $pub->embedding_data = $embedding; // Model cast handles the rest
                    $pub->save();
                    $success++;
                } else {
                    $failed++;
                    Log::error("Failed to generate embedding for DOF ID {$pub->id}");
                }
            } catch (\Exception $e) {
                $failed++;
                Log::error("Embedding error DOF ID {$pub->id}: " . $e->getMessage());
            }

            $bar->advance();
            // Small pause to respect rate limits if needed
            usleep(100000); // 100ms
        }

        $bar->finish();
        $this->newLine(2);

        $this->table(
            ['Resultado', 'Cantidad'],
            [
                ['Exitosos', $success],
                ['Fallidos', $failed],
                ['Pendientes Totales', DofPublication::whereNull('embedding_data')->count()],
            ]
        );

        if ($success > 0) {
            $this->info("✅ Proceso terminado. La búsqueda semántica ahora funcionará mejor para estos registros.");
        }

        return 0;
    }
}
