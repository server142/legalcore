#!/bin/bash

# Script de Aprovisionamiento Automático para Laravel en Ubuntu 22.04/24.04
# Uso: ./provision.sh midominio.com

DOMAIN=$1

if [ -z "$DOMAIN" ]; then
    echo "Por favor proporciona el nombre de dominio."
    echo "Uso: ./provision.sh midominio.com"
    exit 1
fi

echo "🚀 Iniciando aprovisionamiento para $DOMAIN..."

# 1. Actualizar sistema
echo "📦 Actualizando paquetes..."
apt-get update && apt-get upgrade -y

# 2. Instalar dependencias básicas
echo "🛠 Instaland herramientas básicas..."
apt-get install -y git curl zip unzip software-properties-common supervisor

# 3. Instalar PHP 8.2 y extensiones
echo "🐘 Instalando PHP 8.2..."
add-apt-repository ppa:ondrej/php -y
apt-get update
apt-get install -y php8.2-fpm php8.2-cli php8.2-common php8.2-mysql php8.2-zip php8.2-gd php8.2-mbstring php8.2-curl php8.2-xml php8.2-bcmath php8.2-intl

# 4. Instalar MySQL
echo "🐬 Instalando MySQL..."
apt-get install -y mysql-server
# Configuración segura básica (se recomienda ejecutar mysql_secure_installation manualmente después si se desea más seguridad)

# 5. Instalar Composer
echo "🎼 Instalando Composer..."
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer

# 6. Instalar Node.js y NPM
echo "🟢 Instalando Node.js..."
curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
apt-get install -y nodejs

# 7. Instalar Nginx
echo "🌐 Instalando Nginx..."
apt-get install -y nginx

# 8. Configurar Nginx
echo "⚙️ Configurando Nginx..."
cat > /etc/nginx/sites-available/$DOMAIN <<EOF
server {
    listen 80;
    server_name $DOMAIN www.$DOMAIN;
    root /var/www/$DOMAIN/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
EOF

ln -s /etc/nginx/sites-available/$DOMAIN /etc/nginx/sites-enabled/
rm /etc/nginx/sites-enabled/default
nginx -t && systemctl restart nginx

# 9. Crear directorio y permisos
echo "📂 Creando directorios..."
mkdir -p /var/www/$DOMAIN
chown -R www-data:www-data /var/www/$DOMAIN
chmod -R 775 /var/www/$DOMAIN

# 10. Crear base de datos y usuario
echo "🗄 Creando base de datos..."
DB_NAME="despacho_db"
DB_USER="despacho_user"
DB_PASS=$(openssl rand -base64 12)

mysql -e "CREATE DATABASE ${DB_NAME};"
mysql -e "CREATE USER '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';"
mysql -e "GRANT ALL PRIVILEGES ON ${DB_NAME}.* TO '${DB_USER}'@'localhost';"
mysql -e "FLUSH PRIVILEGES;"

echo "✅ Base de datos creada."
echo "DB: $DB_NAME"
echo "User: $DB_USER"
echo "Pass: $DB_PASS"

# 11. Instalar Certbot (SSL)
# Detectar si es una IP para saltar SSL
if [[ "$DOMAIN" =~ ^[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+$ ]]; then
    echo "⚠️  Se detectó una dirección IP ($DOMAIN). Saltando configuración SSL (Certbot)."
    echo "   Podrás acceder vía http://$DOMAIN"
else
    echo "🔒 Instalando Certbot..."
    apt-get install -y certbot python3-certbot-nginx
    # No ejecutamos certbot automáticamente para evitar bloqueos si el DNS no ha propagado
    echo "ℹ️  Para activar HTTPS, ejecuta manualmente cuando el DNS haya propagado:"
    echo "   certbot --nginx -d $DOMAIN"
fi

echo "-----------------------------------------------------"
echo "🎉 ¡Servidor Aprovisionado!"
echo "-----------------------------------------------------"
echo "Siguientes pasos:"
echo "1. Clona tu repo en /var/www/$DOMAIN"
echo "2. Copia .env.example a .env y configura DB y URL"
echo "3. Ejecuta: composer install --no-dev --optimize-autoloader"
echo "4. Ejecuta: npm install && npm run build"
echo "5. Ejecuta: php artisan migrate --seed --force"
echo "6. Ejecuta: php artisan storage:link"
echo "7. Configura permisos: chown -R www-data:www-data /var/www/$DOMAIN"
if [[ ! "$DOMAIN" =~ ^[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+$ ]]; then
    echo "8. Activa SSL: certbot --nginx -d $DOMAIN"
fi
echo "-----------------------------------------------------"
