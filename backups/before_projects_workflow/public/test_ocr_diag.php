<?php

echo "<h1>🔍 Diagnóstico Profundo de OCR para Laravel</h1>";
echo "<pre>";

function check($title, $command) {
    echo "<strong>$title:</strong> ";
    $output = [];
    $return_var = 0;
    exec($command . " 2>&1", $output, $return_var); // Capture stderr too
    if ($return_var === 0) {
        echo "<span style='color:green'>OK</span>\n";
        echo "   -> " . ($output[0] ?? 'Sin salida') . "\n";
    } else {
        echo "<span style='color:red'>FALLÓ (Código $return_var)</span>\n";
        echo "   -> Error: " . implode("\n   -> ", $output) . "\n";
    }
    echo "--------------------------------------------------\n";
}

// 1. Verificar Usuario y Permisos
echo "USUARIO PHP: " . exec('whoami') . "\n";
echo "RUTA ACTUAL: " . __DIR__ . "\n";
echo "PERMISOS /tmp: " . substr(sprintf('%o', fileperms('/tmp')), -4) . "\n";
echo "ESPACIO EN DISCO: " . disk_free_space("/") . " bytes libres\n";
echo "--------------------------------------------------\n";

// 2. Verificar Binarios
check("Ghostscript (gs)", "gs --version");
check("ImageMagick (convert)", "convert -version");
check("Tesseract", "tesseract --version");

// 3. Verificar Idiomas Tesseract
echo "<strong>Idiomas Tesseract Instalados:</strong>\n";
$langs = [];
exec("tesseract --list-langs", $langs);
print_r($langs);
echo "--------------------------------------------------\n";

// 4. PRUEBA DE FUEGO: Creación de Imagen Dummy
$testImage = '/tmp/test_ocr_dummy.png';
$testPdf = '/tmp/test_ocr_dummy.pdf';
$testOut = '/tmp/test_ocr_result';

// Crear una imagen con texto básico usando ImageMagick
echo "<strong>Paso 4.1: Crear Imagen de Prueba (ImageMagick):</strong>\n";
exec("convert -size 300x100 xc:white -font DejaVu-Sans -pointsize 24 -fill black -draw \"text 20,50 'Hola Mundo'\" $testImage 2>&1", $out, $ret);
if ($ret == 0 && file_exists($testImage)) {
    echo "<span style='color:green'>ÉXITO: Imagen creada en $testImage</span>\n";
    
    // Probar OCR en esa imagen simple
    echo "<strong>Paso 4.2: Tesseract sobre Imagen (Simple):</strong>\n";
    exec("tesseract $testImage $testOut -l spa 2>&1", $out2, $ret2);
    if ($ret2 == 0) {
        echo "<span style='color:green'>ÉXITO: OCR leyó la imagen.</span>\n";
        echo "Texto detectado: [" . trim(file_get_contents($testOut . ".txt")) . "]\n";
    } else {
        echo "<span style='color:red'>FALLO: Tesseract no pudo leer la imagen simple.</span>\n";
        print_r($out2);
    }

} else {
    echo "<span style='color:red'>FALLO CRÍTICO: ImageMagick no funciona ni para crear imágenes simples.</span>\n";
    print_r($out);
}
echo "--------------------------------------------------\n";

// 5. PRUEBA DE FUEGO 2: Conversión PDF -> Imagen (El punto donde falla tu app)
echo "<strong>Paso 5.1: Crear PDF Dummy (Ghostscript):</strong>\n";
// Crear PDF simple
exec("gs -sDEVICE=pdfwrite -o $testPdf -sPAPERSIZE=a4 -dFIXEDMEDIA -c \"/Helvetica findfont 24 scalefont setfont 50 700 moveto (Hola PDF) show showpage\" 2>&1", $outPDF, $retPDF);

if ($retPDF == 0 && file_exists($testPdf)) {
    echo "<span style='color:green'>ÉXITO: PDF de prueba creado.</span>\n";

    // Intentar convertir ese PDF a JPG usando ImageMagick (Aquí es donde falla la Policy)
    echo "<strong>Paso 5.2: Convertir PDF a JPG (La prueba de fuego):</strong>\n";
    $jpgOut = '/tmp/test_ocr_converted.jpg';
    exec("convert -density 150 $testPdf -quality 90 $jpgOut 2>&1", $outConv, $retConv);
    
    if ($retConv == 0 && file_exists($jpgOut)) {
        echo "<span style='color:green'>ÉXITO TOTAL: ImageMagick PUEDE leer PDFs.</span>\n";
        echo "El sistema está sano. El problema debe ser el archivo en específico.\n";
    } else {
        echo "<span style='color:red'>FALLO CRÍTICO: ImageMagick NO PUDO leer el PDF.</span>\n";
        echo "Esto confirma 100% bloqueo de Policy o falta de Ghostscript.\n";
        echo "Salida del error:\n";
        print_r($outConv);
    }

} else {
    echo "<span style='color:red'>FALLO: Ghostscript no pudo ni crear el PDF.</span>\n";
    print_r($outPDF);
}

echo "</pre>";
