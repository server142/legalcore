# Plan de Reversión - Límite de Expedientes

## Si algo sale mal, ejecuta estos pasos:

### 1. Revertir migración (si ya la corriste)
```bash
php artisan migrate:rollback --step=1
```

### 2. Revertir código con Git
```bash
# Ver los últimos commits
git log --oneline -5

# Revertir al commit anterior a esta característica
git revert HEAD
git push
```

### 3. Limpiar caché
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

## Archivos modificados en esta característica:

1. `database/migrations/2026_01_28_040000_add_max_expedientes_to_plans_table.php` - NUEVO
2. `app/Http/Middleware/CheckExpedienteLimit.php` - NUEVO
3. `app/Models/Plan.php` - MODIFICADO
4. `app/Livewire/Expedientes/Create.php` - MODIFICADO
5. `app/Livewire/Asesorias/Form.php` - MODIFICADO

## Cómo probar que funciona:

1. Crea un plan con `max_expedientes = 2`
2. Asigna ese plan a un tenant
3. Intenta crear 3 expedientes
4. El tercero debe ser bloqueado con un mensaje de error

## Notas:
- `max_expedientes = 0` significa ilimitado
- La validación se hace ANTES de crear el expediente
- El contador incluye TODOS los expedientes del tenant (activos, archivados, etc.)
