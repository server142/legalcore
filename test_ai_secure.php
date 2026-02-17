<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\AIService;

echo "🤖 Conectando a IA Segura...\n";

$ai = new AIService();
$res = $ai->ask([
    ['role' => 'user', 'content' => 'Di exactamente: Sistema Protegido']
]);

if (isset($res['success']) && $res['success']) {
    echo "✅ RESPUESTA IA: " . $res['content'] . "\n";
    echo "💰 Costo registrado (tokens): " . json_encode($res['usage']) . "\n";
} else {
    echo "❌ ERROR IA: " . json_encode($res) . "\n";
}
