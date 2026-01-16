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

        $tenant = auth()->user()->tenant;
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.invoice', compact('factura', 'tenant'));
        
        return $pdf->download("factura-{$factura->id}.pdf");
    }

    public function expediente(Expediente $expediente)
    {
        if ($expediente->tenant_id !== auth()->user()->tenant_id) {
            abort(403);
        }

        $tenant = auth()->user()->tenant;
        $expediente->load(['cliente', 'abogado', 'actuaciones', 'documentos', 'eventos']);
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.expediente', compact('expediente', 'tenant'));
        
        $filename = str_replace(['/', '\\'], '-', $expediente->numero);
        return $pdf->download("reporte-expediente-{$filename}.pdf");
    }
}
