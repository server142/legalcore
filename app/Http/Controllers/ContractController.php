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

        // 4. Check Requested Format (PDF by default, but supports WORD)
        if ($request->query('format') === 'word') {
            try {
                $phpWord = new \PhpOffice\PhpWord\PhpWord();
                $section = $phpWord->addSection();
                
                // MANUAL CONSTRUCTION PHASE 4: DIAGNOSTIC (FIRST 5 LINES ONLY)
                // If this works, the problem is deep in the document.
                // If this fails, the problem is at the very beginning of the htmlContent.
                
                // 1. Convert structural tags to newlines
                $processedContent = str_replace(
                    ['<br>', '<br/>', '<br />', '</p>', '</h1>', '</h2>', '</h3>', '</h4>', '</li>', '</div>', '</tr>', '</table>'], 
                    "\n", 
                    $htmlContent
                );
                
                // 2. Decode entities
                $processedContent = html_entity_decode($processedContent, ENT_QUOTES | ENT_XML1, 'UTF-8');
                
                // 3. Strip tags
                $plainText = strip_tags($processedContent);
                
                // 4. NUCLEAR OPTION: Whitelist valid characters only
                $safeText = preg_replace('/[^\p{L}\p{N}\p{P}\p{Z}\n\r\t]+/u', '', $plainText);
                
                // 5. Split and Add ONLY FIRST 5 LINES
                $lines = explode("\n", $safeText);
                $lines = array_slice($lines, 0, 5); // LIMIT TO 5 LINES
                
                $titleStyle = ['bold' => true, 'size' => 12];
                $normalStyle = ['size' => 11];
                $centeredParams = ['align' => 'center', 'spaceAfter' => 200];
                $justifiedParams = ['align' => 'both', 'spaceAfter' => 100];
                
                $section->addText("DEBUG: SOLO PRIMERAS 5 LINEAS DEL CONTRATO");
                $section->addText("----------------------------------------");

                foreach ($lines as $line) {
                    $trimLine = trim($line);
                    
                    if (!empty($trimLine)) {
                        $isTitle = (mb_strlen($trimLine) > 5 && mb_strtoupper($trimLine) === $trimLine && !str_contains($trimLine, '. '));
                        
                        if ($isTitle || str_starts_with($trimLine, 'CONTRATO') || str_contains($trimLine, 'CLÁUSULAS')) {
                            $section->addText($trimLine, $titleStyle, $centeredParams);
                        } else {
                            $section->addText($trimLine, $normalStyle, $justifiedParams);
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
