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

        return response()->file(Storage::disk('local')->path($documento->path));
    }
}
