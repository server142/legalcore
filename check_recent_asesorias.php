<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$items = \App\Models\Asesoria::orderBy('id', 'desc')->take(5)->get();

if ($items->count() > 0) {
    foreach ($items as $item) {
        echo "ID: " . $item->id . " | Folio: " . $item->folio . "\n";
        echo "Nombre: " . $item->nombre_prospecto . "\n";
        echo "Fecha: " . $item->fecha_hora . "\n";
        echo "Estado: " . $item->estado . "\n";
        echo "Tenant ID: " . $item->tenant_id . "\n";
        echo "Abogado ID: " . $item->abogado_id . " (" . ($item->abogado?->name ?? 'N/A') . ")\n";
        echo "-------------------------------------------\n";
    }
} else {
    echo "No hay asesorías en la base de datos.";
}
