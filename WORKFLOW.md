# Flujo de Trabajo y Mantenimiento

Este documento explica cómo actualizar tu aplicación en producción y cómo configurar un dominio personalizado.

---

## 🚀 1. Cómo subir cambios (De Local a Producción)

### Paso A: En tu computadora (Local)
1.  Realiza tus cambios en el código.
2.  Guarda y sube los cambios a GitHub:
    ```bash
    git add .
    git commit -m "Descripción breve de los cambios"
    git push origin main
    ```

### Paso B: En el servidor (Producción)
1.  Conéctate por SSH:
    ```bash
    ssh root@157.245.181.194
    ```
2.  Ve a la carpeta del proyecto:
    ```bash
    # Si usas IP:
    cd /var/www/157.245.181.194
    
    # Si ya configuraste dominio:
    # cd /var/www/midominio.com
    ```
3.  Descarga los cambios:
    ```bash
    git pull origin main
    ```

### Paso C: Comandos adicionales (Solo si es necesario)
*   **Si modificaste la base de datos (migraciones):**
    ```bash
    php artisan migrate --force
    ```
*   **Si instalaste nuevas librerías (composer):**
    ```bash
    composer install --no-dev --optimize-autoloader
    ```
*   **Si modificaste archivos CSS o JS (diseño):**
    ```bash
    npm install && npm run build
    ```
*   **Si modificaste la caché o rutas:**
    ```bash
    php artisan optimize:clear
    php artisan optimize
    ```

---

## 🌐 2. Cómo configurar tu Dominio (Futuro)

Cuando compres tu dominio (ej. `midespacho.com`), sigue estos pasos para activarlo y tener HTTPS (candado verde).

### Paso A: Configurar DNS (En tu proveedor de dominio)
1.  Entra a la administración de DNS de tu dominio.
2.  Crea un registro **A**:
    *   **Host:** `@`
    *   **Valor:** `157.245.181.194`
3.  Crea un registro **CNAME** (opcional para www):
    *   **Host:** `www`
    *   **Valor:** `midespacho.com`

*Espera unas horas a que se propague.*

### Paso B: Configurar el Servidor
1.  Conéctate por SSH.
2.  **(Opcional)** Renombra la carpeta para orden:
    ```bash
    mv /var/www/157.245.181.194 /var/www/midespacho.com
    ```
3.  Edita la configuración de Nginx:
    ```bash
    nano /etc/nginx/sites-available/157.245.181.194
    ```
    *   Cambia `server_name` a: `server_name midespacho.com www.midespacho.com;`
    *   Cambia `root` a: `root /var/www/midespacho.com/public;` (si renombraste la carpeta).
4.  Guarda (`Ctrl+O`, `Enter`) y sal (`Ctrl+X`).
5.  Reinicia Nginx:
    ```bash
    systemctl restart nginx
    ```

### Paso C: Activar SSL (HTTPS)
Ejecuta este comando y sigue las instrucciones:
```bash
certbot --nginx -d midespacho.com -d www.midespacho.com
```

### Paso D: Actualizar Laravel
Edita tu archivo `.env`:
```bash
nano /var/www/midespacho.com/.env
```
Cambia `APP_URL` a `https://midespacho.com`.

---

## 🛠 Comandos Útiles

*   **Ver logs de errores:** `tail -f /var/www/157.245.181.194/storage/logs/laravel.log`
*   **Reiniciar cola de trabajos:** `php artisan queue:restart`
*   **Reiniciar servicios:** `systemctl restart php8.2-fpm nginx`
