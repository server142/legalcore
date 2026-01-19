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
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.invoice', compact('factura', 'tenant'));
        
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
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.expediente', compact('expediente', 'tenant'));
        
        $filename = str_replace(['/', '\\'], '-', $expediente->numero);
        return $pdf->download("reporte-expediente-{$filename}.pdf");
    }
}
