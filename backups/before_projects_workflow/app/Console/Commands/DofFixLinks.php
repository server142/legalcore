<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DofPublication;

class DofFixLinks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dof:fix-links';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Regenerate PDF links for existing DOF publications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Starting link repair...");

        $query = DofPublication::query(); // Process all records to be safe, or whereNull('link_pdf')
        $count = $query->count();
        $bar = $this->output->createProgressBar($count);
        $bar->start();

        $updated = 0;

        $query->chunkById(100, function ($publications) use ($bar, &$updated) {
            foreach ($publications as $pub) {
                if ($pub->cod_nota && $pub->fecha_publicacion) {
                    $fechaFormatted = $pub->fecha_publicacion->format('d/m/Y');
                    $newLink = "https://dof.gob.mx/nota_detalle.php?codigo={$pub->cod_nota}&fecha={$fechaFormatted}#gsc.tab=0";
                    
                    if ($pub->link_pdf !== $newLink) {
                        $pub->link_pdf = $newLink;
                        $pub->save();
                        $updated++;
                    }
                }
                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine();
        $this->info("Done! Updated links for {$updated} publications.");
    }
}
