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

        // 3. Generate HTML
        try {
            $generator = new ContractGenerationService();
            $htmlContent = $generator->generate($template, $expediente);

            // Wrapper
            $fullHtml = '
            <html>
            <head>
                <style>
                    body { font-family: "Times New Roman", serif; line-height: 1.5; color: #000; }
                    /* Ensure tables and other elements respect PDF size */
                </style>
            </head>
            <body>
                ' . $htmlContent . '
            </body>
            </html>';

            // 4. PDF Generation
            $options = new Options();
            $options->set('isRemoteEnabled', true);
            $options->set('defaultFont', 'serif');

            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($fullHtml);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            $safeNumero = str_replace(['/', '\\'], '-', $expediente->numero);
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
