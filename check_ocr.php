<?php
// check_ocr.php
require __DIR__ . '/vendor/autoload.php';

echo "\n=== Verificación de OCR Local (Diogenes) ===\n\n";

$isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
echo "Sistema detectado: " . ($isWindows ? "Windows" : "Linux/Unix") . "\n";

// 1. Verificar Binario Tesseract
if ($isWindows) {
    // Rutas comunes en Windows
    $possiblePaths = [
        'C:\Program Files\Tesseract-OCR\tesseract.exe',
        'C:\Program Files (x86)\Tesseract-OCR\tesseract.exe',
        getenv('TESSERACT_PATH')
    ];
    $bin = null;
    foreach ($possiblePaths as $p) {
        if ($p && file_exists($p)) {
            $bin = $p;
            break;
        }
    }
} else {
    // Linux
    $bin = '/usr/bin/tesseract';
}

if ($bin && file_exists($bin) || (!$isWindows && trim(shell_exec("which tesseract")) != '')) {
    echo "[PASS] ✅ Binario Tesseract encontrado";
    if ($bin) echo " en: $bin";
    echo "\n";
    // Version check
    $cmd = $isWindows ? "\"$bin\" --version" : "tesseract --version";
    echo "       " . explode("\n", shell_exec($cmd . " 2>&1"))[0] . "\n";
} else {
    echo "[FAIL] ❌ NO se encontró Tesseract.\n";
    if ($isWindows) {
        echo "       Solución: Instala Tesseract desde https://github.com/UB-Mannheim/tesseract/wiki\n";
    } else {
        echo "       Solución: Ejecuta 'sudo apt-get install tesseract-ocr'\n";
    }
}

// 2. Verificar Idioma Español
// En Windows es difícil saber la ruta de datos sin consultar el binario, probamos listado.
$cmdLangs = $isWindows ? "\"$bin\" --list-langs" : "tesseract --list-langs";
$checkLang = shell_exec($cmdLangs . " 2>&1");

if (strpos($checkLang, 'spa') !== false) {
    echo "[PASS] ✅ Idioma Español instalado.\n";
} else {
    echo "[FAIL] ❌ Falta idioma Español (spa).\n";
    if (!$isWindows) echo "       Solución: Ejecuta 'sudo apt-get install tesseract-ocr-spa'\n";
}

// 3. Verificar Imagick
if (extension_loaded('imagick')) {
    echo "[PASS] ✅ Extensión Imagick cargada (Necesaria para PDFs multipágina).\n";
} else {
    echo "[WARN] ⚠️ Imagick NO cargada. El OCR funcionará en IMÁGENES, pero podría fallar en PDFs multipágina.\n";
}

// 4. Verificar Librería PHP
if (class_exists('thiagoalessio\TesseractOCR\TesseractOCR')) {
    echo "[PASS] ✅ Librería PHP TesseractOCR instalada correctamente.\n";
} else {
    echo "[FAIL] ❌ Librería PHP NO encontrada. Ejecuta: composer require thiagoalessio/tesseract_ocr\n";
}

echo "\n=== Fin del Diagnóstico ===\n";
