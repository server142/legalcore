#!/bin/bash

# Este script está diseñado para ejecutarse en tu servidor de PRODUCCIÓN (Linux/Ubuntu).
# Instala las dependencias que faltan para que Tesseract pueda leer PDFs.

# Verificar si es usuario root
if [ "$EUID" -ne 0 ]
  then echo "⚠️  Por favor, ejecuta este script como root (sudo ./fix_production_ocr.sh)"
  exit
fi

echo "--- 🛠️ Iniciando Reparación de Dependencias OCR para Laravel ---"

# 1. Actualizar repositorios
echo "📦 [1/5] Actualizando lista de paquetes..."
apt-get update -y

# 2. Instalar Ghostscript (CRUCIAL: permite a Tesseract 'ver' el PDF)
echo "📄 [2/5] Instalando Ghostscript..."
apt-get install -y ghostscript

# 3. Instalar Tesseract OCR y el idioma Español
echo "👁️ [3/5] Instalando Tesseract OCR + Idioma Español..."
apt-get install -y tesseract-ocr tesseract-ocr-spa libtesseract-dev

# 4. Instalar ImageMagick y Poppler (Ayudan en la conversión de formatos)
echo "🖼️ [4/5] Instalando ImageMagick y herramientas PDF..."
apt-get install -y imagemagick poppler-utils

# 5. Corregir política de seguridad de ImageMagick 
# (Por defecto en Ubuntu, ImageMagick bloquea los PDFs por seguridad. Esto lo desbloquea.)
POLICY_FILE="/etc/ImageMagick-6/policy.xml"
if [ -f "$POLICY_FILE" ]; then
    echo "🔓 Desbloqueando permisos de PDF en ImageMagick..."
    # Cambia rights="none" a rights="read|write" para PDFs
    sed -i 's/rights="none" pattern="PDF"/rights="read|write" pattern="PDF"/g' "$POLICY_FILE"
else
    echo "ℹ️ No se encontró archivo de políticas ImageMagick, saltando este paso."
fi

echo "--- ✅ Instalación Completada ---"
echo "Las herramientas necesarias (Ghostscript, Tesseract, ImageMagick) han sido instaladas."
echo "Prueba subir el PDF nuevamente en tu sistema."
