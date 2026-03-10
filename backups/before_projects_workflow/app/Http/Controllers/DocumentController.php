<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Documento;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function show(Documento $documento)
    {
        // El trait BelongsToTenant ya filtra por tenant si está activo, 
        // pero por seguridad extra podemos verificar aquí.
        if ($documento->tenant_id !== auth()->user()->tenant_id) {
            abort(403);
        }

        if (!Storage::disk('local')->exists($documento->path)) {
            abort(404);
        }

        AuditLog::create([
            'user_id' => auth()->id(),
            'accion' => 'view',
            'modulo' => 'documentos',
            'descripcion' => "Consultó el archivo: {$documento->nombre}",
            'metadatos' => ['documento_id' => $documento->id],
            'ip_address' => request()->ip(),
        ]);

        $safeToInline = in_array(strtolower($documento->extension), ['pdf', 'jpg', 'jpeg', 'png', 'gif']);
        
        $headers = [
            'Content-Security-Policy' => "default-src 'none'; style-src 'unsafe-inline'; img-src 'self' data:; shadow-root 'none';",
            'X-Content-Type-Options' => 'nosniff',
        ];

        if ($safeToInline) {
            return response()->file(Storage::disk('local')->path($documento->path), $headers);
        }

        return response()->download(Storage::disk('local')->path($documento->path), $documento->nombre, $headers);
    }
}
