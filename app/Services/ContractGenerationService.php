<?php

namespace App\Services;

use App\Models\LegalDocument;
use App\Models\Expediente;
use Illuminate\Support\Str;

class ContractGenerationService
{
    /**
     * Genera un contrato en HTML reemplazando las variables con los datos del expediente.
     *
     * @param LegalDocument $template El documento plantilla (debe ser tipo CONTRATO_SERVICIOS)
     * @param Expediente $expediente El expediente del cual tomar los datos
     * @return string El HTML procesado
     */
    public function generate(LegalDocument $template, Expediente $expediente): string
    {
        $content = $template->texto;
        $variables = $this->getVariables($expediente);

        foreach ($variables as $key => $value) {
            // Reemplazar {{VARIABLE}} y {{ VARIABLE }}
            $content = str_replace('{{' . $key . '}}', $value, $content);
            $content = str_replace('{{ ' . $key . ' }}', $value, $content);
        }

        // Manual cleanup to avoid DOMDocument errors with malformed fragments
        // 1. Extract body content if present
        if (preg_match('/<body[^>]*>(.*?)<\/body>/is', $content, $matches)) {
            $content = $matches[1];
        }

        // 2. Fix common void tags for XML compatibility (XHTML)
        // PhpWord requires <br/>, <hr/>, <img> to be closed.
        $content = preg_replace('/<br\s*\/?>/i', '<br/>', $content);
        $content = preg_replace('/<hr\s*\/?>/i', '<hr/>', $content);
        $content = preg_replace('/<img([^>]+)(?<!\/)>/i', '<img$1/>', $content);

        // 3. Fix Entities
        $content = str_replace('&nbsp;', ' ', $content);
        
        // 4. Decode HTML entities to their corresponding characters
        // This prevents &aacute; from breaking XML parsers if the DTD isn't loaded
        // We exclude standard XML entities: &amp;, &lt;, &gt;, &quot;, &apos;
        $content = html_entity_decode($content, ENT_QUOTES | ENT_XML1, 'UTF-8');
        
        // 5. Remove 'style' attributes as PhpWord often fails to parse complex CSS inline
        // This is the #1 cause of "Word detected an error" with valid HTML structure
        $content = preg_replace('/\s+style="[^"]*"/', '', $content);
        $content = preg_replace("/\s+style='[^']*'/", '', $content);

        // 6. Strip potentially dangerous tags
        $allowedTags = '<p><a><ul><ol><li><b><strong><i><em><u><br><h1><h2><h3><h4><h5><h6><table><thead><tbody><tr><td><th><img><div><span><hr>';
        $content = strip_tags($content, $allowedTags);

        // Re-apply void tag fixes after strip_tags just in case
        $content = preg_replace('/<br\s*\/?>/i', '<br/>', $content);
        $content = preg_replace('/<hr\s*\/?>/i', '<hr/>', $content);
        // Simple img fix - ensure it ends with />
        $content = preg_replace('/<img([^>]+)(?<!\/)>/i', '<img$1/>', $content);

        return $content;
    }

    /**
     * Obtiene el mapa de variables y sus valores para un expediente dado.
     */
    public function getVariables(Expediente $expediente): array
    {
        $cliente = $expediente->cliente;
        $abogado = $expediente->abogado;

        return [
            // Datos del Cliente
            'CLIENTE_NOMBRE' => $cliente ? $cliente->nombre : '____________________',
            'CLIENTE_RFC' => $cliente ? ($cliente->rfc ?? 'N/A') : '____________________',
            'CLIENTE_EMAIL' => $cliente ? ($cliente->email ?? 'N/A') : '____________________',
            'CLIENTE_DIRECCION' => $cliente ? ($cliente->direccion ?? 'Domicilio Conocido') : '____________________',

            // Datos del Expediente
            'EXPEDIENTE_FOLIO' => $expediente->numero,
            'EXPEDIENTE_TITULO' => $expediente->titulo,
            'EXPEDIENTE_MATERIA' => $expediente->materia ?? 'General',
            'EXPEDIENTE_JUZGADO' => $expediente->juzgado ?? 'No Asignado',
            'EXPEDIENTE_JUICIO' => $expediente->descripcion ?? 'Servicios Legales',
            
            // Datos Financieros
            'HONORARIOS_TOTALES' => number_format($expediente->honorarios_totales ?? 0, 2),
            'FECHA_INICIO' => $expediente->fecha_inicio ? $expediente->fecha_inicio->format('d/m/Y') : date('d/m/Y'),
            
            // Datos del Despacho/Abogado
            'ABOGADO_RESPONSABLE' => $abogado ? $abogado->name : 'El Despacho',
            'FECHA_ACTUAL' => date('d/m/Y'),
            
            // Datos Institucionales del Despacho (Tenant)
            'DESPACHO_NOMBRE' => $expediente->tenant ? 
                ($expediente->tenant->name) : 'El Despacho',
            
            'DESPACHO_TITULAR' => $expediente->tenant && $expediente->tenant->settings ? 
                ($expediente->tenant->settings['titular'] ?? 'Representante Legal') : 'Representante Legal',

            'DESPACHO_RFC' => $expediente->tenant && $expediente->tenant->settings ? 
                ($expediente->tenant->settings['rfc'] ?? 'RFC No Definido') : 'RFC No Definido',

            'DESPACHO_DIRECCION' => $expediente->tenant && $expediente->tenant->settings ? 
                ($expediente->tenant->settings['direccion'] ?? 'Domicilio del Despacho') : 'Domicilio del Despacho',
                
            'DESPACHO_EMAIL' => $expediente->tenant && $expediente->tenant->settings ? 
                ($expediente->tenant->settings['email_contacto'] ?? '') : '',
                
            'CIUDAD_FIRMA' => $expediente->tenant && $expediente->tenant->settings ? 
                ($expediente->tenant->settings['ciudad'] ?? 'Ciudad de México') : 'Ciudad de México',
        ];
    }

    /**
     * Retorna la lista de variables disponibles para mostrar en la UI de ayuda.
     */
    public static function getAvailableVariables(): array
    {
        return [
            'CLIENTE_NOMBRE',
            'CLIENTE_RFC',
            'CLIENTE_EMAIL',
            'CLIENTE_DIRECCION',
            'EXPEDIENTE_FOLIO',
            'EXPEDIENTE_TITULO',
            'EXPEDIENTE_MATERIA',
            'EXPEDIENTE_JUZGADO',
            'EXPEDIENTE_JUICIO',
            'HONORARIOS_TOTALES',
            'FECHA_INICIO',
            'ABOGADO_RESPONSABLE',
            'FECHA_ACTUAL',
            'CIUDAD_FIRMA',
            'DESPACHO_NOMBRE',
            'DESPACHO_TITULAR',
            'DESPACHO_RFC',
            'DESPACHO_DIRECCION',
            'DESPACHO_EMAIL'
        ];
    }
}
