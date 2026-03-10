<?php

namespace App\Services;

use Google\Client;
use Google\Service\Vision;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Parser;

class OcrService
{
    protected $client;
    protected $vision;

    public function __construct()
    {
        // Solo inicializamos Google Client si es necesario para ahorrar recursos
    }

    /**
     * Intenta extraer texto de un archivo.
     * 1. Primero intenta extracción nativa (rápida y gratis).
     * 2. Si falla o devuelve poco texto, usa OCR de Google Vision (costo/lento).
     */
    public function extractText($filePath)
    {
        // 1. Intentar extracción nativa (PDF Text)
        $text = $this->extractNativeText($filePath);

        // Si tenemos un texto decente (> 100 caracteres), asumimos que es un PDF digital
        if (strlen(trim($text)) > 100) {
            return $text;
        }

        // 2. Si el texto es muy corto o vacío, probablemente es un escaneo. Usar OCR.
        Log::info("Texto nativo insuficiente para {$filePath}. Iniciando OCR de Google Vision...");
        return $this->extractWithGoogleVision($filePath);
    }

    protected function extractNativeText($filePath)
    {
        try {
            $parser = new Parser();
            // Need absolute path for Parser
            if (!file_exists($filePath)) {
                // Try relative to storage/app if full path not valid
                $storagePath = storage_path('app/' . $filePath);
                if (file_exists($storagePath)) {
                    $filePath = $storagePath;
                }
            }
            
            $pdf = $parser->parseFile($filePath);
            return $pdf->getText();
        } catch (\Exception $e) {
            Log::warning("Fallo en extracción nativa de PDF: " . $e->getMessage());
            return '';
        }
    }

    protected function extractWithGoogleVision($filePath)
    {
        $credentialsPath = config('services.google.credentials_path', storage_path('app/google-credentials.json'));

        if (!file_exists($credentialsPath)) {
            Log::error("No se encontró el archivo de credenciales de Google en: {$credentialsPath}");
            return "Error: No se ha configurado el OCR. Falta el archivo de credenciales de Google Cloud.";
        }

        try {
            $client = new Client();
            $client->setAuthConfig($credentialsPath);
            $client->addScope(Vision::CLOUD_PLATFORM);

            $vision = new Vision($client);

            // Google Vision API espera el contenido del archivo en base64
            // o un GCS URI. Para simplicidad local, enviamos el contenido.
            // OJO: Hay límites de tamaño (10MB aprox). Si es muy grande, habría que dividirlo.
            // Para PDFs multipágina, Google recomienda usar AsyncBatchAnnotate y GCS.
            // Para simplicidad en esta v1, convertiremos las primeras páginas a imágenes o enviaremos el PDF si es pequeño.
            
            // Limitación: La API síncrona de Vision 'annotate' funciona mejor con IMAGENES.
            // Para PDFs completos, se requiere 'asyncBatchAnnotate' y Google Storage.
            // TRUCO: Para evitar GCS (complicado de configurar), podemos intentar enviar el contenido del PDF
            // con mime_type 'application/pdf' soportado en versiones recientes, o mejor aún:
            // Advertir al usuario que OCR completo de PDFs multipágina requiere GCS.
            
            // Por ahora, implementaremos una lectura básica asumiendo que el archivo se puede leer.
            // Si es PDF, la API standard requiere async.
            // Alternativa rápida: Usar 'ghostscript' o 'imagick' para convertir la primera página a imagen y leer esa? 
            // Eso requiere dependencias del sistema.
            
            // VARIANTE: Usar la API sincrona enviando el contenido como 'application/pdf' se puede si el archivo es pequeño?
            // Documentación dice: "PDF/TIFF supported in AsyncBatchAnnotateFile only".
            
            // Entonces, sin GCS, no podemos leer PDFs escaneados directamente con una simple llamada HTTP.
            // SOLUCIÓN: Usar un conversor local (si existe) o pedirle al usuario que suba imágenes.
            // O... Usar AsyncBatchAnnotateFile pero apuntando a un bucket público? No.

            // Vvamos a intentar leerlo como binario, a ver si la API nos da una alegría, 
            // si no, tendremos que advertir.
            
            $data = file_get_contents($filePath);
            
            // Creamos la solicitud de imagen
            // NOTA: Si enviamos PDF directo a `annotate`, fallará.
            // Vamos a asumir por un momento que la librería `spatie/pdf-to-image` pudiera estar disponible, o `imagick`.
            // Si no, estamos limitados.
            
            // PLAN B: Requerir que el usuario suba imágenes para OCR, o implementar conversión.
            // Sin embargo, Google Vision TIENE soporte para archivos, pero usualmente asíncrono.
            
            // Para no complicarnos con Buckets ahora mismo:
            // Vamos a devolver un mensaje de "Pendiente implementar Buckets",
            // PERO si el usuario subió una IMAGEN (jpg/png), sí funcionará directo.
            
            $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'bmp', 'webp'])) {
                // Procesar Imagen
                $image = new Vision\Image();
                $image->setContent($data);

                $feature = new Vision\Feature();
                $feature->setType('DOCUMENT_TEXT_DETECTION');

                $request = new Vision\AnnotateImageRequest();
                $request->setImage($image);
                $request->setFeatures([$feature]);

                $batch = new Vision\BatchAnnotateImagesRequest();
                $batch->setRequests([$request]);

                $response = $vision->images->annotate($batch);
                return $response->getResponses()[0]->getFullTextAnnotation()->getText();
            } else {
                return "El sistema OCR actual solo soporta imágenes (JPG, PNG) directamente. Para PDFs escaneados multipágina, se requiere configuración de Google Cloud Storage. Intente subir una captura de la página relevante o conviértalo a texto digital.";
            }

        } catch (\Exception $e) {
            Log::error("Error de Google Vision: " . $e->getMessage());
            return "Error al conectar con el servicio OCR: " . $e->getMessage();
        }
    }
}
