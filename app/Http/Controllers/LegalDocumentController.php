<?php

namespace App\Http\Controllers;

use App\Models\LegalDocument;
use Illuminate\Http\Request;

class LegalDocumentController extends Controller
{
    public function show($type)
    {
        $tenantId = auth()->check() ? auth()->user()->tenant_id : null;
        
        $query = LegalDocument::where('activo', true);

        // Si es un tipo enum
        if (in_array(strtoupper($type), ['PRIVACIDAD', 'TERMINOS', 'COOKIES', 'CONTRATO_SAAS'])) {
            $document = (clone $query)->where('tipo', strtoupper($type))
                ->where('tenant_id', $tenantId)
                ->orderBy('version', 'desc')
                ->first();

            // Sifallback a global
            if (!$document && $tenantId) {
                $document = (clone $query)->where('tipo', strtoupper($type))
                    ->whereNull('tenant_id')
                    ->orderBy('version', 'desc')
                    ->first();
            }
        } else {
            // Búsqueda por ID o slug de nombre
            $document = (clone $query)->where(function($q) use ($type) {
                $q->where('id', $type)->orWhere('nombre', $type);
            })->firstOrFail();
        }

        if (!$document) {
            abort(404, 'Documento no encontrado.');
        }

        return view('legal.document', [
            'title' => $document->nombre,
            'content' => $document->texto,
            'version' => $document->version,
            'date' => $document->fecha_publicacion
        ]);
    }
}
