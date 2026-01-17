# Guía de Despliegue a Producción (DigitalOcean)

Esta guía te llevará paso a paso para desplegar el Sistema de Despacho Legal en un servidor Ubuntu (DigitalOcean Droplet).

## Requisitos Previos
1.  Cuenta en GitHub con el código subido.
2.  Cuenta en DigitalOcean.
3.  Dominio comprado (ej. `midespacho.com`) **O** simplemente la IP del servidor.

---

## Paso 1: Crear el Droplet
1.  En DigitalOcean, crea un nuevo Droplet.
2.  **Imagen:** Ubuntu 24.04 (LTS) x64.
3.  **Plan:** Basic (CPU Regular) - $6/mes (1GB RAM) es suficiente para empezar, pero $12/mes (2GB RAM) es recomendado.
4.  **Autenticación:** SSH Key (recomendado) o Password.
5.  **Hostname:** `midespacho` (o el nombre que prefieras).

## Paso 2: Configurar DNS (Opcional)
Si tienes dominio, crea un registro **A** que apunte a la IP.
Si **NO** tienes dominio, salta este paso y usarás la IP directamente.

---

## Paso 3: Configurar el Servidor

Conéctate por SSH a tu servidor:
```bash
ssh root@tu_ip_del_droplet
```

### 3.1 Subir script de instalación
Crea un archivo llamado `provision.sh` en el servidor y pega el contenido del archivo `provision.sh` que está en este proyecto.

```bash
nano provision.sh
# Pega el contenido aquí, guarda con Ctrl+O y sal con Ctrl+X
chmod +x provision.sh
```

### 3.2 Ejecutar instalación
Ejecuta el script pasando tu dominio o tu IP:
```bash
./provision.sh midominio.com
# O si usas IP:
./provision.sh 164.90.120.55
```
*Este script instalará Nginx, PHP 8.2, MySQL, Composer, Node.js y configurará todo automáticamente.*

Al finalizar, el script te mostrará las **credenciales de la base de datos**. ¡Guárdalas!

---

## Paso 4: Desplegar la Aplicación

### 4.1 Clonar el repositorio
```bash
# Si usas dominio:
cd /var/www/midominio.com
# Si usas IP:
cd /var/www/164.90.120.55

git clone https://github.com/server142/legalcore.git .
```
*(Asegúrate de poner el punto al final para clonar en la carpeta actual)*

### 4.2 Configurar entorno
```bash
cp .env.example .env
nano .env
```
Edita las siguientes variables:
- `APP_URL=http://164.90.120.55` (o tu dominio con https)
- `APP_ENV=production`
- `APP_DEBUG=false`
- `DB_DATABASE=despacho_db`
- `DB_USERNAME=despacho_user`
- `DB_PASSWORD=LA_CONTRASEÑA_GENERADA`

### 4.3 Instalar dependencias
```bash
composer install --no-dev --optimize-autoloader
npm install
npm run build
```

### 4.4 Base de datos y almacenamiento
```bash
php artisan key:generate
php artisan migrate --seed --force
php artisan storage:link
```

### 4.5 Permisos finales
```bash
# Ajusta la ruta según uses dominio o IP
chown -R www-data:www-data /var/www/164.90.120.55
chmod -R 775 storage bootstrap/cache
```

---

## Paso 5: SSL (HTTPS)
**SOLO SI TIENES DOMINIO.** Si usas IP, salta este paso.

```bash
certbot --nginx -d midominio.com -d www.midominio.com
```
Sigue las instrucciones y selecciona la opción de redirigir todo el tráfico a HTTPS.

---

## ✅ ¡Listo!
Tu aplicación debería estar accesible en `http://164.90.120.55` (o tu dominio).

### Usuario Administrador por defecto:
- **Email:** admin@legalcore.com
- **Password:** password (¡Cámbialo inmediatamente!)
