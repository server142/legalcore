<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LegalDocument;

class DebugContractContent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'debug:contract-content';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Muestra el contenido crudo del contrato en la base de datos para depuración';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Obtener la plantilla de contrato de servicios
        // Suponemos que es la primera o filtramos si es necesario
        $template = LegalDocument::where('tipo', 'CONTRATO_SERVICIOS')->first();

        if (!$template) {
            $this->error('No se encontró ninguna plantilla de CONTRATO_SERVICIOS.');
            return 1;
        }

        $this->info("ID: " . $template->id);
        $this->info("Versión: " . $template->version);
        $this->info("Contenido Crudo (Primeros 1000 caracteres):");
        $this->line("--------------------------------------------------");
        
        // Imprimir crudo, mostrando caracteres invisibles si es posible
        $content = $template->contenido;
        
        // Vamos a resaltar caracteres especiales
        $debugContent = str_replace(["\n", "\r", "\t"], ['[LF]', '[CR]', '[TAB]'], $content);
        
        $this->line(substr($debugContent, 0, 1000));
        $this->line("--------------------------------------------------");
        
        // Análisis de Hex para ver BOM
        $this->info("Primeros 20 Bytes en Hex (Busca EF BB BF):");
        $hex = bin2hex(substr($content, 0, 20));
        // Formatear hex para lectura fácil
        $formattedHex = chunk_split($hex, 2, ' ');
        $this->line($formattedHex);

        $this->info("Análisis de Etiquetas HTML encontradas:");
        preg_match_all('/<[^>]+>/', $content, $matches);
        $tags = array_unique($matches[0]);
        foreach ($tags as $tag) {
            $this->line(" - " . htmlspecialchars($tag));
        }

        return 0;
    }
}
