<?php

use Illuminate\Support\Facades\Http;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$date = '10-01-2024'; // d-m-Y
$url = "https://sidof.segob.gob.mx/dof/sidof/notas/{$date}";

echo "Trying URL: $url\n";

try {
    $response = Http::withOptions(['verify' => false])->timeout(10)->get($url);
    
    echo "Status: " . $response->status() . "\n";
    echo "Body Preview: " . substr($response->body(), 0, 500) . "...\n";
    
    $json = $response->json();
    if ($json) {
        echo "JSON Decoded Keys: " . implode(', ', array_keys($json)) . "\n";
    } else {
        echo "Failed to decode JSON.\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
