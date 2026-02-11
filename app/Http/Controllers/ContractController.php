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
                
                // Add HTML content to Word
                \PhpOffice\PhpWord\Shared\Html::addHtml($section, $htmlContent, false, false);

                $filename = "Contrato-Servicios-Exp-{$safeNumero}.docx";
                
                // Save to temporary file
                $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
                $tempFile = tempnam(sys_get_temp_dir(), 'contract');
                $objWriter->save($tempFile);

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
