<?php

namespace App\Http\Controllers;

use App\Models\Expediente;
use App\Models\LegalDocument;
use App\Services\ContractGenerationService;
use Illuminate\Http\Request;
use Dompdf\Dompdf;
use Dompdf\Options;

class ContractController extends Controller
{
    public function generate(Request $request, Expediente $expediente)
    {
        // 1. Authorization
        $user = auth()->user();
        if ($user->hasRole('abogado') && !$user->can('view all expedientes')) {
            $isAssigned = $expediente->assignedUsers()->where('users.id', $user->id)->exists();
            if ($expediente->abogado_responsable_id !== $user->id && !$isAssigned) {
                abort(403);
            }
        }
        
        // 2. Find Template
        $template = LegalDocument::where('tipo', 'CONTRATO_SERVICIOS')
            ->forTenant($request->user()->tenant_id)
            ->first();

        // Fallback to global
        if (!$template) {
            $template = LegalDocument::where('tipo', 'CONTRATO_SERVICIOS')
                ->whereNull('tenant_id')
                ->first();
        }

        if (!$template) {
            return response('No se encontró la plantilla del contrato. Contacte a soporte.', 404);
        }

        // 3. Generate HTML Content using Service
        $generator = new ContractGenerationService();
        $htmlContent = $generator->generate($template, $expediente);
        $safeNumero = str_replace(['/', '\\'], '-', $expediente->numero);

        // 4. Check Requested Format
        if ($request->query('format') === 'debug') {
             // Access the raw text field from DB
             $rawContent = $template->texto; 
             
             $output = "<h1>DEBUG MODE</h1>";
             $output .= "<h2>Raw Content (htmlspecialchars)</h2>";
             $output .= "<pre style='background:#f4f4f4; padding:10px; border:1px solid #ccc; white-space:pre-wrap;'>" . htmlspecialchars($rawContent) . "</pre>";
             
             // BOM Check
             $bom = substr($rawContent, 0, 3);
             $hasBom = ($bom === "\xEF\xBB\xBF") ? "YES" : "NO";
             $output .= "<h2>Has BOM? $hasBom</h2>";
             
             $output .= "<h2>First 50 Bytes (HEX)</h2>";
             $output .= "<pre>" . chunk_split(bin2hex(substr($rawContent, 0, 50)), 2, ' ') . "</pre>";

             return response($output);
        }

        if ($request->query('format') === 'word') {
            try {
                $phpWord = new \PhpOffice\PhpWord\PhpWord();
                $section = $phpWord->addSection();
                
                // MANUAL CONSTRUCTION PHASE 9: RESTORATION (STYLES + UTF-8)
                // Goal: Professional looking document (Justified, Bold headers, Spanish chars)
                
                // 1. Prepare Content
                $cleanContent = str_replace(
                    ['<br>', '<br/>', '<br />', '</p>', '</h1>', '</h2>', '</h3>', '</h4>', '</li>', '</div>', '</tr>', '</table>'], 
                    "\n", 
                    $htmlContent
                );
                // Double decode to handle &amp;nbsp; etc
                $cleanContent = html_entity_decode($cleanContent, ENT_QUOTES | ENT_XML1, 'UTF-8');
                $cleanContent = html_entity_decode($cleanContent);
                $plainText = strip_tags($cleanContent);
                
                // 2. Safe UTF-8 Cleaning (Allow Spanish, Kill Gremlins)
                // Allow Letters (L), Numbers (N), Punctuation (P), Separators (Z), Newline (\n\r)
                $finalText = preg_replace('/[^\p{L}\p{N}\p{P}\p{Z}\n\r]/u', '', $plainText);
                
                // 3. Split into lines
                $lines = explode("\n", $finalText);
                
                // 4. Define Styles
                $baseFont = 'Arial';
                $baseSize = 11;
                
                // Style Arrays
                $titleFontStyle = ['name' => $baseFont, 'size' => 12, 'bold' => true, 'color' => '000000'];
                $titleParaStyle = ['align' => 'center', 'spaceAfter' => 240]; // 12pt approx
                
                $bodyFontStyle = ['name' => $baseFont, 'size' => 11, 'color' => '000000'];
                $bodyParaStyle = ['align' => 'both', 'spaceAfter' => 120];  // 6pt approx
                
                foreach ($lines as $line) {
                    $trimLine = trim($line);
                    
                    if (!empty($trimLine)) {
                         // Heuristic for Titles
                         $isTitle = (mb_strlen($trimLine) > 5 && mb_strtoupper($trimLine) === $trimLine && !str_contains($trimLine, '. '));
                         
                         if ($isTitle || str_starts_with($trimLine, 'CONTRATO') || str_contains($trimLine, 'CLAUSULAS')) {
                              $section->addText($trimLine, $titleFontStyle, $titleParaStyle);
                         } else {
                              $section->addText($trimLine, $bodyFontStyle, $bodyParaStyle);
                         }
                    }
                }
                
                $filename = "Contrato-Servicios-Exp-{$safeNumero}.docx";
                
                // Save to temporary file
                $writer = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
                $tempFile = tempnam(sys_get_temp_dir(), 'contract');
                $writer->save($tempFile);

                return response()->download($tempFile, $filename)->deleteFileAfterSend(true);

            } catch (\Exception $e) {
                 \Log::error('Error generando contrato Word: ' . $e->getMessage());
                 return response('Error generando el archivo Word: ' . $e->getMessage(), 500);
            }
        } else {
            // PDF Generation (Default)
            try {
                // Wrapper for PDF styling
                $fullHtml = '
                <html>
                <head>
                    <style>
                        body { font-family: "Times New Roman", serif; line-height: 1.5; color: #000; }
                    </style>
                </head>
                <body>
                    ' . $htmlContent . '
                </body>
                </html>';

                $options = new Options();
                $options->set('isRemoteEnabled', true);
                $options->set('defaultFont', 'serif');

                $dompdf = new Dompdf($options);
                $dompdf->loadHtml($fullHtml);
                $dompdf->setPaper('A4', 'portrait');
                $dompdf->render();

                $filename = "Contrato-Servicios-Exp-{$safeNumero}.pdf";

                return response()->stream(
                    fn () => print($dompdf->output()),
                    200,
                    [
                        'Content-Type' => 'application/pdf',
                        'Content-Disposition' => 'inline; filename="' . $filename . '"',
                    ]
                );

            } catch (\Exception $e) {
                 \Log::error('Error generando contrato PDF Controlado: ' . $e->getMessage());
                 return response('Error generando el PDF: ' . $e->getMessage(), 500);
            }
        }
    }
}
