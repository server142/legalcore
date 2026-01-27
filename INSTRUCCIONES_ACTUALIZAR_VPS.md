# 📋 INSTRUCCIONES PARA ACTUALIZAR EL SERVIDOR VPS

## ⚠️ IMPORTANTE: Lee todo antes de ejecutar

Este documento contiene los pasos para actualizar tu servidor con la nueva funcionalidad de sincronización de Google Calendar.

---

## 🔄 Pasos de Actualización

### 1. Conectarse al Servidor VPS
```bash
ssh tu-usuario@tu-servidor-ip
cd /ruta/a/tu/proyecto
```

### 2. Hacer Backup de la Base de Datos (CRÍTICO)
```bash
# Backup completo
php artisan db:backup

# O manualmente con mysqldump (si usas MySQL)
mysqldump -u usuario -p nombre_base_datos > backup_$(date +%Y%m%d_%H%M%S).sql

# O con sqlite (si usas SQLite)
cp database/database.sqlite database/database.sqlite.backup_$(date +%Y%m%d_%H%M%S)
```

### 3. Descargar los Cambios de GitHub
```bash
git pull origin main
```

### 4. Actualizar el Manual del Usuario
```bash
php artisan db:seed --class=ManualSeeder
```

**✅ Esto agregará la nueva sección "Sincronización con Google Calendar" al manual**

### 5. Limpiar Cachés
```bash
php artisan view:clear
php artisan cache:clear
php artisan config:clear
```

### 6. Verificar que Todo Funciona
```bash
# Verificar que el manual se actualizó
php artisan tinker --execute="echo App\Models\ManualPage::where('slug', 'sincronizacion-con-google-calendar')->count()"
```

**Debe mostrar: 1**

---

## 🚨 Si Algo Sale Mal - RESTAURACIÓN RÁPIDA

### Opción 1: Restaurar el Seeder (si el manual se ve mal)
En tu computadora local:
```bash
.\restore-manual-seeder.bat
```

Luego en el servidor:
```bash
git pull origin main
php artisan db:seed --class=ManualSeeder
```

### Opción 2: Restaurar Base de Datos Completa
```bash
# Con MySQL
mysql -u usuario -p nombre_base_datos < backup_FECHA.sql

# Con SQLite
cp database/database.sqlite.backup_FECHA database/database.sqlite
```

---

## ✅ Verificación Post-Actualización

### 1. Verificar el Manual
- Entra al sistema
- Ve a **Manual de Usuario**
- Busca la sección **"Sincronización con Google Calendar"**
- Debe aparecer entre "Agenda Judicial" y "Control de Términos Procesales"

### 2. Verificar la Tabla de Flujo
La sección debe mostrar una tabla con 4 escenarios:
- Evento SIN expediente, SIN invitados
- Evento SIN expediente, CON invitados
- Evento CON expediente, SIN invitados
- Evento CON expediente, CON invitados

### 3. Probar la Sincronización
1. Un abogado debe conectar su Google Calendar
2. Crear un evento de prueba
3. Verificar que aparezca en su Google Calendar en el celular

---

## 📊 Resumen de Cambios Aplicados

✅ **EventoObserver.php** - Ahora crea eventos en cada calendario individual  
✅ **GoogleCalendarService.php** - Lógica de sincronización optimizada  
✅ **ManualSeeder.php** - Nueva sección con guía completa  
✅ **Backup automático** - Archivo .backup creado  

---

## 🆘 Soporte

Si encuentras algún problema:

1. **Revisa los logs:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

2. **Verifica el estado de Git:**
   ```bash
   git status
   git log --oneline -5
   ```

3. **Contacta al desarrollador** con:
   - Captura de pantalla del error
   - Últimas 20 líneas del log: `tail -20 storage/logs/laravel.log`
   - Salida de `git log --oneline -3`

---

## 📝 Notas Importantes

- **NO** ejecutes `php artisan migrate:fresh` - perderás todos los datos
- **SÍ** ejecuta solo `php artisan db:seed --class=ManualSeeder`
- El backup del seeder está en: `database/seeders/ManualSeeder.php.backup`
- Los eventos existentes NO se verán afectados
- Los abogados deben conectar su Google Calendar para recibir eventos

---

## ✨ Próximos Pasos

1. Actualizar el servidor (siguiendo esta guía)
2. Compartir el instructivo con los abogados
3. Pedir a cada abogado que conecte su Google Calendar
4. Crear un evento de prueba para verificar

---

**Fecha de creación:** 27 de enero de 2026  
**Versión:** 1.0  
**Commit:** adf1614f
