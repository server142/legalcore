# Guía de Despliegue y Rollback - Integración Google Calendar

## 1. Despliegue en VPS

Sigue estos pasos para subir los cambios y configurar el servidor.

### A. Actualizar Código
Conéctate a tu VPS y navega a la carpeta del proyecto:
```bash
cd /ruta/a/tu/proyecto
git pull origin main
```

### B. Actualizar Base de Datos
Como hay una nueva migración para los tokens de Google, ejecútala:
```bash
php artisan migrate --force
```

### C. Configurar Variables de Entorno (.env)
Edita el archivo `.env`:
```bash
nano .env
```
Agrega al final (usando tus credenciales reales):
```env
GOOGLE_CLIENT_ID=tu_client_id
GOOGLE_CLIENT_SECRET=tu_client_secret
GOOGLE_REDIRECT_URI=https://www.diogenes.com.mx/auth/google/callback
```
Guarda con `Ctrl+O`, `Enter`, y sal con `Ctrl+X`.

### D. Limpiar Caché
Para que Laravel lea las nuevas variables del .env y la configuración:
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 2. Plan de Rollback (Emergencia)

Si algo falla y necesitas volver atrás rápidamente:

### A. Revertir Base de Datos
Esto eliminará las columnas de Google de la tabla `users`:
```bash
php artisan migrate:rollback --step=1
```

### B. Revertir Código
Vuelve al commit anterior (antes de estos cambios):
```bash
# Regresa al estado anterior (HEAD~1 significa "un paso atrás")
git reset --hard HEAD~1
```
*Nota: Si ya hiciste varios commits intermedios, usa `git log` para ver el hash del commit seguro y usa `git reset --hard <hash>`.*

### C. Limpiar Caché de nuevo
```bash
php artisan config:cache
```
