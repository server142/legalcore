<?php

namespace App\Services;

use thiagoalessio\TesseractOCR\TesseractOCR;
use Illuminate\Support\Facades\Log;
use Smalot\PdfParser\Parser;

class OcrService
{
    public function __construct()
    {
    }

    /**
     * Intenta extraer texto de un archivo.
     * 1. Primero intenta extracción nativa (rápida).
     * 2. Si falla o es escaneado, usa Tesseract OCR local.
     */
    public function extractText($filePath)
    {
        // 1. Intentar extracción nativa (PDF Text)
        $text = $this->extractNativeText($filePath);

        // Si tenemos un texto decente (> 100 caracteres), asumimos que es un PDF digital
        if (strlen(trim($text)) > 100) {
            return $text;
        }

        // 2. Si el texto es muy corto o vacío, usamos OCR local.
        Log::info("Texto nativo insuficiente para {$filePath}. Iniciando Tesseract OCR local...");
        return $this->extractWithTesseract($filePath);
    }

    protected function extractNativeText($filePath)
    {
        try {
            // Validar existencia real para Smalot Parser
            if (!file_exists($filePath)) {
                return '';
            }
            
            // Solo intentar parser nativo si es PDF
            $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
            if ($ext !== 'pdf') {
                return ''; 
            }

            $parser = new Parser();
            $pdf = $parser->parseFile($filePath);
            return $pdf->getText();
        } catch (\Throwable $e) {
            Log::warning("Fallo en extracción nativa de PDF: " . $e->getMessage());
            return '';
        }
    }

    protected function extractWithTesseract($filePath)
    {
        try {
            $ocr = new TesseractOCR($filePath);
            
            // Configuración de ruta del binario según OS
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                // Ruta común en Windows XAMPP/Laragon setups o instalador oficial
                // Se asume que el usuario instaló Tesseract for Windows
                $possiblePaths = [
                    'C:\Program Files\Tesseract-OCR\tesseract.exe',
                    'C:\Program Files (x86)\Tesseract-OCR\tesseract.exe',
                    getenv('TESSERACT_PATH') // Opción custom por variable de entorno
                ];
                
                $found = false;
                foreach ($possiblePaths as $path) {
                    if ($path && file_exists($path)) {
                        $ocr->executable($path);
                        $found = true;
                        break;
                    }
                }
                
                if (!$found) {
                    // Intento final: asumir que está en el PATH
                    // No seteamos executable(), dejamos que el wrapper use 'tesseract' del sistema
                }
            } else {
                // Linux (Ubuntu Production)
                // Generalmente está en /usr/bin/tesseract, el wrapper lo encuentra solo
                $ocr->executable('/usr/bin/tesseract');
            }

            // Detección de Idioma (Español por defecto, luego Inglés)
            $ocr->lang('spa', 'eng');

            // Manejo especial para PDFs con Imagick (si está disponible)
            $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
            
            if ($ext === 'pdf') {
                // Tesseract puro a veces falla con PDFs multipágina directamente
                // Intentamos convertir a imagen si tenemos Imagick
                if (extension_loaded('imagick')) {
                    try {
                        $imagick = new \Imagick();
                        $imagick->setResolution(300, 300); // Buena calidad para OCR
                        $imagick->readImage($filePath);
                        
                        $text = '';
                        // Procesar cada página
                        foreach ($imagick as $page) {
                            $page->setImageFormat('jpg');
                            // Guardar tmp
                            $tmpFile = tempnam(sys_get_temp_dir(), 'ocr_') . '.jpg';
                            $page->writeImage($tmpFile);
                            
                            // OCR a la página
                            $ocrPage = new TesseractOCR($tmpFile);
                            // Replicar config de ejecutable
                            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' && isset($found) && $found) {
                                // No tenemos acceso a la variable $path del scope anterior fácil, 
                                // pero idealmente instanciamos uno nuevo.
                                // Simplificación: reusamos la lógica de detección si fuera necesario, 
                                // pero por ahora confiamos en el PATH o reconfiguramos.
                                $ocrPage->executable('C:\Program Files\Tesseract-OCR\tesseract.exe'); 
                            } elseif (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
                                $ocrPage->executable('/usr/bin/tesseract');
                            }
                            
                            $ocrPage->lang('spa', 'eng');
                            $text .= $ocrPage->run() . "\n\n";
                            
                            unlink($tmpFile);
                        }
                        
                        $imagick->clear();
                        return $text;
                        
                    } catch (\Throwable $e) {
                         Log::warning("Imagick falló, intentando Tesseract directo sobre PDF. Error: " . $e->getMessage());
                         // Guardamos el error para diagnóstico
                         $imagickError = "Fallo conversión PDF->Imagen (Imagick): " . $e->getMessage();
                    }
                } else {
                    $imagickError = "Imagick NO está instalado/cargado en PHP.";
                }
            }

            // Ejecución normal (Imagen o PDF directo si soportado)
            try {
                return $ocr->run();
            } catch (\Throwable $tesseractError) {
                // Si teníamos un error previo de Imagick, lo adjuntamos para contexto
                if (isset($imagickError)) {
                    throw new \Exception($imagickError . " | Y Tesseract directo falló: " . $tesseractError->getMessage());
                }
                throw $tesseractError;
            }

        } catch (\Throwable $e) {
            Log::error("Error local Tesseract OCR: " . $e->getMessage());
            return "Error procesando OCR local. Asegúrese de que Tesseract está instalado en el servidor. Detalles: " . $e->getMessage();
        }
    }
}
