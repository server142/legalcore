# Guía de Despliegue en VPS (Producción)

Dado que hemos implementado nuevas funcionalidades críticas (Colas de Trabajo y Parsing de PDFs), es vital seguir estos pasos al desplegar en tu servidor VPS.

## 1. Requisitos del Servidor
Asegúrate de que tu VPS tenga instaladas las extensiones necesarias para procesar PDFs:
```bash
sudo apt-get update
sudo apt-get install php-zip unzip -y
```

## 2. Actualización de Código
```bash
git pull origin main
composer install --optimize-autoloader --no-dev
npm run build
```

## 3. Base de Datos
Ejecuta las migraciones para crear la columna de texto extraído:
```bash
php artisan migrate --force
```

## 4. Configuración del Supervisor (CRÍTICO)
Para que los Jobs de lectura de PDF funcionen en segundo plano, **debes tener corriendo el worker de Laravel**.

1. Instala Supervisor:
   ```bash
   sudo apt-get install supervisor
   ```

2. Crea la configuración: `/etc/supervisor/conf.d/legalcore-worker.conf`
   ```ini
   [program:legalcore-worker]
   process_name=%(program_name)s_%(process_num)02d
   command=php /var/www/legalcore/artisan queue:work --sleep=3 --tries=3 --max-time=3600
   autostart=true
   autorestart=true
   user=www-data
   numprocs=1
   redirect_stderr=true
   stdout_logfile=/var/www/legalcore/worker.log
   stopwaitsecs=3600
   ```

3. Inicia el worker:
   ```bash
   sudo supervisorctl reread
   sudo supervisorctl update
   sudo supervisorctl start legalcore-worker:*
   ```

## 5. Verificación
Sube un PDF y revisa el log `storage/logs/laravel.log` o `worker.log`. Deberías ver:
`Processing Document ID: X`
`Document processed successfully.`

---
**Nota:** Si no configuras Supervisor, los PDFs se subirán pero NUNCA serán leídos por la IA (se quedarán en estado 'pending').
