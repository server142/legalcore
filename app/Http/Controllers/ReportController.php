<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use App\Models\Expediente;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function invoice(Factura $factura)
    {
        if ($factura->tenant_id !== auth()->user()->tenant_id) {
            abort(403);
        }

        if (!auth()->user()->can('manage billing')) {
            abort(403);
        }

        $tenant = auth()->user()->tenant;
        $factura->load('cliente');

        // Convert logo to base64 for better compatibility
        $logoBase64 = null;
        if (isset($tenant->settings['logo_path'])) {
            $path = storage_path('app/public/' . $tenant->settings['logo_path']);
            if (file_exists($path)) {
                $type = pathinfo($path, PATHINFO_EXTENSION);
                $data = file_get_contents($path);
                $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            }
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.invoice', compact('factura', 'tenant', 'logoBase64'));
        
        return $pdf->download("factura-{$factura->id}.pdf");
    }

    public function expediente(Expediente $expediente)
    {
        if ($expediente->tenant_id !== auth()->user()->tenant_id) {
            abort(403);
        }

        // Check if user can view this expediente
        $user = auth()->user();
        $canView = $user->hasRole(['admin', 'super_admin']) || 
                   $expediente->abogado_responsable_id === $user->id ||
                   $expediente->assignedUsers()->where('users.id', $user->id)->exists();

        if (!$canView) {
            abort(403);
        }

        $tenant = auth()->user()->tenant;
        $expediente->load(['cliente', 'abogado', 'actuaciones', 'documentos', 'eventos']);
        
        // Convert logo to base64 for better compatibility
        $logoBase64 = null;
        if (isset($tenant->settings['logo_path'])) {
            $path = storage_path('app/public/' . $tenant->settings['logo_path']);
            if (file_exists($path)) {
                $type = pathinfo($path, PATHINFO_EXTENSION);
                $data = file_get_contents($path);
                $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            }
        }
        
        try {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.expediente', compact('expediente', 'tenant', 'logoBase64'));
            
            $filename = str_replace(['/', '\\'], '-', $expediente->numero);
            return $pdf->download("reporte-expediente-{$filename}.pdf");
        } catch (\Exception $e) {
            \Log::error('Error generando PDF de expediente: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            return response()->json(['error' => 'Error interno al generar el PDF: ' . $e->getMessage()], 500);
        }
    }
}
