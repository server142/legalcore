# 🚀 INSTALACIÓN DEL SISTEMA DE GESTIÓN DE PROVEEDORES DE IA

## ✅ Archivos Creados:

1. **Migración:** `database/migrations/2026_02_13_185500_create_ai_providers_table.php`
2. **Modelo:** `app/Models/AiProvider.php`
3. **Controlador Livewire:** `app/Livewire/Admin/AiProviders/Index.php`
4. **Vista:** `resources/views/livewire/admin/ai-providers/index.blade.php`
5. **Seeder:** `database/seeders/MigrateAiSettingsSeeder.php`
6. **AIService actualizado:** `app/Services/AIService.php`

---

## 📋 PASOS DE INSTALACIÓN:

### 1. Agregar la ruta (MANUAL)

Abre `routes/web.php` y agrega esta línea después de la línea 92:

```php
Route::get('/admin/ai-providers', \\App\\Livewire\\Admin\\AiProviders\\Index::class)->name('admin.ai-providers')->middleware('can:manage tenants');
```

Debe quedar así:

```php
Route::get('/admin/global-settings', \\App\\Livewire\\Admin\\GlobalSettings::class)->name('admin.global-settings')->middleware('can:manage tenants');
Route::get('/admin/ai-providers', \\App\\Livewire\\Admin\\AiProviders\\Index::class)->name('admin.ai-providers')->middleware('can:manage tenants');  // ← NUEVA LÍNEA
Route::get('/admin/announcements', \\App\\Livewire\\Admin\\Announcements::class)->name('admin.announcements')->middleware('can:manage tenants');
```

### 2. Ejecutar la migración

```bash
php artisan migrate
```

### 3. Migrar tu configuración actual

```bash
php artisan db:seed --class=MigrateAiSettingsSeeder
```

Esto tomará tu configuración actual de OpenAI y la migrará al nuevo sistema.

### 4. Agregar enlace en el menú (OPCIONAL)

Si quieres un enlace en el sidebar, edita `resources/views/livewire/layout/navigation-links.blade.php` y agrega:

```blade
<x-sidebar-link href="{{ route('admin.ai-providers') }}" :active="request()->routeIs('admin.ai-providers')" icon="cpu">
    Proveedores de IA
</x-sidebar-link>
```

---

## 🎯 CÓMO USAR:

### Acceder a la gestión:
```
http://tudominio.com/admin/ai-providers
```

### Agregar Claude (Anthropic):
1. Ve a https://console.anthropic.com
2. Crea una API key
3. En tu sistema, click en "Agregar Proveedor"
4. Llena:
   - Nombre: `Anthropic (Claude)`
   - Slug: `anthropic` (se genera automático)
   - Modelo: `claude-3-5-sonnet-20241022`
   - API Key: `sk-ant-api03-...`
5. Guardar
6. Click en "Activar" para usarlo

### Cambiar entre proveedores:
- Solo haz click en "Activar" en el proveedor que quieras usar
- El sistema cambiará automáticamente
- Todas tus keys quedan guardadas

---

## 🔒 SEGURIDAD:

- ✅ Las API keys se guardan **encriptadas** en la BD
- ✅ Solo se muestran parcialmente en la UI (ej: `sk-proj-***`)
- ✅ Solo usuarios con permiso `manage tenants` pueden acceder

---

## 🧪 PROBAR:

1. Agrega tu proveedor actual (OpenAI)
2. Prueba la conexión (botón "Probar")
3. Agrega Claude
4. Prueba la conexión de Claude
5. Activa Claude
6. Ve al asistente de expedientes y prueba una consulta
7. Compara las respuestas entre ambos

---

## ⚠️ NOTAS IMPORTANTES:

- El sistema tiene **retrocompatibilidad**: Si no hay proveedores en la nueva tabla, usará `global_settings` como antes
- Puedes mantener ambos sistemas activos temporalmente
- Una vez migrado, puedes eliminar `ai_provider`, `ai_api_key` y `ai_model` de `global_settings` (opcional)

---

## 🐛 TROUBLESHOOTING:

**Error: "Call to undefined method"**
- Ejecuta: `composer dump-autoload`

**No aparece el menú**
- Verifica que tengas permiso `manage tenants`
- Limpia cache: `php artisan route:clear`

**Las keys no se guardan**
- Verifica que `APP_KEY` esté configurada en `.env`
- Ejecuta: `php artisan key:generate` (solo si es nuevo)

---

¿Listo para probar? 🚀
