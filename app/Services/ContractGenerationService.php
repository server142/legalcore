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

        // Use DOMDocument to ensure valid XHTML for PhpWord
        if (!empty($content)) {
            $dom = new \DOMDocument();
             // Suppress warnings for HTML5 tags or minor errors
            libxml_use_internal_errors(true);
            
            // Load HTML with UTF-8 encoding hack
            // We strip any existing html/body tags to avoid duplication before loading if needed,
            // but DOMDocument handles it well if we just search for body.
            // Using mb_convert_encoding is safer for special chars.
            $dom->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
            libxml_clear_errors();

            // We need the content INSIDE the body, compatible with XML (e.g. <br/> not <br>)
            $body = $dom->getElementsByTagName('body')->item(0);
            
            if ($body) {
                $xmlContent = '';
                foreach ($body->childNodes as $child) {
                    // saveXML creates valid XHTML (self-closing tags)
                    $xmlContent .= $dom->saveXML($child);
                }
                $content = $xmlContent;
            } else {
                // If no body tag found (fragment), save the whole thing as XML
                $content = $dom->saveXML();
            }
        }

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
