<?php

namespace App\Services;

use thiagoalessio\TesseractOCR\TesseractOCR;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Smalot\PdfParser\Parser;

class OcrService
{
    /**
     * Intenta extraer texto de un archivo.
     * Prioridad:
     * 1. Extracción Nativa (si es PDF Texto)
     * 2. OpenAI Vision (si está configurado) -> Calidad Premium
     * 3. Tesseract Local (Fallback)
     */
    public function extractText($filePath)
    {
        // 1. Intentar extracción nativa rápida (PDF Text digital)
        $text = $this->extractNativeText($filePath);
        if (strlen(trim($text)) > 150) { 
            // Si sacamos bastante texto, probablemente no es escaneado.
            return $text;
        }

        // 2. Revisar Configuración Global
        $settings = DB::table('global_settings')
            ->whereIn('key', ['ocr_mode', 'ai_api_key', 'ai_model', 'ai_provider'])
            ->pluck('value', 'key');
            
        $ocrMode = $settings['ocr_mode'] ?? 'local';
        $apiKey = $settings['ai_api_key'] ?? '';
        $aiModel = $settings['ai_model'] ?? 'gpt-4o-mini';

        if ($ocrMode === 'off') {
             return $text . "\n[OCR desactivado]"; 
        }

        // 3. ✨ IA VISION (La solución definitiva) ✨
        // Si tienes API Key, preferimos usar la IA directamente para leer la imagen/PDF.
        // Soporta 'vision' explícito o si el modelo es capaz (gpt-4o, gpt-4-turbo)
        $isVisionCapable = str_contains($aiModel, 'gpt-4') || str_contains($aiModel, 'claude-3') || $ocrMode === 'vision';
        
        if (!empty($apiKey) && ($ocrMode === 'vision' || $isVisionCapable)) {
            Log::info("Usando VISION AI para leer documento: {$filePath}");
            try {
                $visionText = $this->extractWithVision($filePath, $apiKey, $aiModel, $settings['ai_provider'] ?? 'openai');
                if (!empty($visionText)) {
                    return $visionText;
                }
            } catch (\Exception $e) {
                Log::error("Vision AI falló, cayendo a Tesseract: " . $e->getMessage());
                // Fallback a local si falla la API
            }
        }

        // 4. Fallback: Tesseract Local
        Log::info("Vision no disponible o falló. Usando Tesseract Local para {$filePath}");
        return $this->extractWithTesseract($filePath);
    }

    protected function extractWithVision($filePath, $apiKey, $model, $provider = 'openai')
    {
        // Convertir archivo a Base64
        $mimeType = mime_content_type($filePath);
        $base64Data = base64_encode(file_get_contents($filePath));
        $dataUrl = "data:{$mimeType};base64,{$base64Data}";

        // Preparar Payload para GPT-4o / Vision
        // Nota: OpenAI no acepta PDFs nativos en Chat Completions Vision directamente a menos que sea un asistente con File Search.
        // Pero GPT-4o acepta imágenes.
        // Si es PDF, OpenAI Vision NO lo lee directo por API standard (requiere Assistant API).
        // TRUCO: Si es PDF, necesitamos la imagen.
        // Si el servidor falla convirtiendo PDF a imagen (el problema original), Vision tampoco servirá si enviamos PDF raw.
        
        // SOLUCIÓN HÍBRIDA ROBUSTA:
        // Si es imagen -> Directo a OpenAI Vision.
        // Si es PDF -> Intentar convertir primera página a imagen con GD/Imagick ligero, O enviar a un servicio externo.
        // Dado que el servidor tiene problemas con Imagick, asumimos que el usuario subirá FOTOS (.jpg, .png) de documentos.
        // Si sube PDF escaneado, dependemos de que Imagick funcione minimamente.
        
        // Mejora: Si es PDF, intentamos convertirlo rápido a JPG.
        if ($mimeType === 'application/pdf') {
             // Si Imagick está roto, esto fallará. Pero intentemos.
             try {
                if (extension_loaded('imagick')) {
                    $imagick = new \Imagick();
                    $imagick->setResolution(150, 150);
                    $imagick->readImage($filePath . '[0]'); // Solo leer portada para prueba rápida o OCR de 1ra página
                    $imagick->setImageFormat('jpg');
                    $base64Data = base64_encode($imagick->getImageBlob());
                    $mimeType = 'image/jpeg';
                    $dataUrl = "data:image/jpeg;base64,{$base64Data}";
                    $imagick->clear();
                } else {
                    return null; // No podemos convertir PDF a imagen para Vision
                }
             } catch (\Exception $e) {
                 Log::error("No se pudo convertir PDF para Vision: " . $e->getMessage());
                 return null;
             }
        }

        // Payload OpenAI Vision
        $payload = [
            'model' => $model, // gpt-4o es el mejor
            'messages' => [
                [
                    'role' => 'user',
                    'content' => [
                        [
                            'type' => 'text',
                            'text' => 'Transcribe el contenido completo de este documento legal. Sé preciso con fechas y nombres. Si hay tablas, represéntalas con Markdown. Si es ilegible, di [ILEGIBLE].'
                        ],
                        [
                            'type' => 'image_url',
                            'image_url' => [
                                'url' => $dataUrl,
                                'detail' => 'high' 
                            ]
                        ]
                    ]
                ]
            ],
            'max_tokens' => 4000
        ];

        $response = Http::withToken($apiKey)
            ->timeout(60) // Esperar hasta 60s
            ->post('https://api.openai.com/v1/chat/completions', $payload);

        if ($response->successful()) {
            return $response->json('choices.0.message.content');
        }

        throw new \Exception("OpenAI API Error: " . $response->body());
    }

    protected function extractNativeText($filePath)
    {
        try {
            if (!file_exists($filePath)) return '';
            $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
            if ($ext !== 'pdf') return ''; 
            
            $parser = new Parser();
            $pdf = $parser->parseFile($filePath);
            return $pdf->getText();
        } catch (\Throwable $e) {
            return '';
        }
    }

    protected function extractWithTesseract($filePath)
    {
        return (new LegacyTesseractService())->extract($filePath);
    }
}

// Clase interna para mover el código viejo y no borrarlo del todo
class LegacyTesseractService {
    public function extract($filePath) {
        try {
             $ocr = new \thiagoalessio\TesseractOCR\TesseractOCR($filePath);
             
             // Detect OS and set path accordingly if needed, or rely on PATH
             if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                 // Try common Windows paths or assume it's in PATH
                 // Providing a specific path only if it exists, otherwise trust PATH
                 if (file_exists('C:\\Program Files\\Tesseract-OCR\\tesseract.exe')) {
                     $ocr->executable('C:\\Program Files\\Tesseract-OCR\\tesseract.exe');
                 }
                 // If not set, it uses default 'tesseract' command which works if in PATH
             } else {
                 $ocr->executable('/usr/bin/tesseract');
             }
             
             $ocr->lang('spa', 'eng');
             return $ocr->run();
        } catch (\Throwable $e) {
            Log::error("Tesseract Error: " . $e->getMessage());
            return "Error OCR Local: No se pudo leer el documento. (Detalle: " . $e->getMessage() . ")";
        }
    }
}
