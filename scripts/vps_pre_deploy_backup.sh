#!/bin/bash

# Configuration
BACKUP_DIR="/root/backups_pre_deploy"
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
DB_NAME="despacho" # Verify your DB name
APP_PATH="/var/www/legalcore"

echo "🚀 Iniciando proceso de respaldo de seguridad..."

# 1. Create backup directory
mkdir -p $BACKUP_DIR

# 2. Database Backup
echo "📦 Respaldando base de datos..."
mysqldump $DB_NAME > "$BACKUP_DIR/db_backup_$TIMESTAMP.sql"

if [ $? -eq 0 ]; then
    echo "✅ Base de datos respaldada en $BACKUP_DIR/db_backup_$TIMESTAMP.sql"
else
    echo "❌ ERROR: Falló el respaldo de la base de datos."
    exit 1
fi

# 3. Code State (Git Snapshot)
echo "📂 Creando punto de restauración en Git..."
cd $APP_PATH
git add .
git commit -m "Pre-deploy backup snapshot $TIMESTAMP"
git branch "backup_$TIMESTAMP"

echo "✅ Punto de restauración creado: rama backup_$TIMESTAMP"

# 4. Instructions for reversal
echo "----------------------------------------------------"
echo "🆘 PARA REVERTIR SI ALGO SALE MAL:"
echo "1. Base de Datos: mysql $DB_NAME < $BACKUP_DIR/db_backup_$TIMESTAMP.sql"
echo "2. Código: git checkout main && git reset --hard backup_$TIMESTAMP"
echo "----------------------------------------------------"
echo "✅ Respaldo completado. Puedes proceder con el despliegue."
