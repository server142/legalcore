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
        // AJUSTES DE ESTABILIDAD
        set_time_limit(300); 
        ini_set('memory_limit', '512M'); // 512MB es más seguro si el servidor es pequeño
        
        putenv('MAGICK_THREAD_LIMIT=1');
        putenv('OMP_NUM_THREADS=1');

        try {
            $ocr = new TesseractOCR($filePath);
            
            // Configuración de ruta del binario según OS
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                $possiblePaths = [
                    'C:\\Program Files\\Tesseract-OCR\\tesseract.exe',
                    'C:\\Program Files (x86)\\Tesseract-OCR\\tesseract.exe',
                    getenv('TESSERACT_PATH')
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

            // --- MANEJO OPTIMIZADO DE PDF ---
            $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
            
            if ($ext === 'pdf') {
                if (extension_loaded('imagick')) {
                    try {
                        // 1. Contar páginas sin cargar el PDF entero (usando pingImage o identify)
                        // Como Imagick a veces falla con ping en PDFs complejos, intentamos una carga ligera
                        // O simplemente, iteramos hasta que falle.
                        
                        $text = '';
                        $pageIndex = 0;
                        $maxPages = 50; // Límite de seguridad
                        
                        while ($pageIndex < $maxPages) {
                            try {
                                // Cargar SOLO una página a la vez: "archivo.pdf[0]"
                                $imagick = new \Imagick();
                                $imagick->setResolution(200, 200); // Bajamos a 200 DPI para ahorrar RAM (suficiente para OCR)
                                $imagick->readImage($filePath . '[' . $pageIndex . ']'); 
                                
                                $imagick->setImageFormat('jpg');
                                $imagick->setImageCompressionQuality(80);
                                
                                $tmpFile = tempnam(sys_get_temp_dir(), 'ocr_pg' . $pageIndex . '_') . '.jpg';
                                $imagick->writeImage($tmpFile);
                                
                                // Liberar memoria INMEDIATAMENTE
                                $imagick->clear();
                                $imagick->destroy();
                                unset($imagick);

                                // Procesar OCR de esta página
                                $ocrPage = new TesseractOCR($tmpFile);
                                // (Replicar config ejecutable...)
                                if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' && isset($found) && $found) {
                                    $ocrPage->executable('C:\\Program Files\\Tesseract-OCR\\tesseract.exe'); 
                                } elseif (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
                                    $ocrPage->executable('/usr/bin/tesseract');
                                }
                                $ocrPage->lang('spa', 'eng');
                                
                                $pageText = $ocrPage->run();
                                $text .= $pageText . "\n\n";
                                
                                unlink($tmpFile);
                                $pageIndex++;
                                
                            } catch (\ImagickException $e) {
                                // Si falla al leer la página X, asumimos que se acabaron las páginas
                                break;
                            }
                        }
                        
                        if (empty($text) && $pageIndex === 0) {
                             throw new \Exception("No se pudo leer ninguna página del PDF.");
                        }
                        
                        return $text;
                        
                    } catch (\Throwable $e) {
                         Log::warning("Imagick Page-by-Page falló: " . $e->getMessage());
                         $imagickError = "Error Imagick: " . $e->getMessage();
                    }
                } else {
                    $imagickError = "Imagick NO está instalado.";
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
