# 🚀 Gestión de Usuarios de Prueba de Google (100 Límite)

## ¿Qué son los Usuarios de Prueba?

Mientras tu app de Google no esté verificada, solo los usuarios que agregues manualmente a la "lista blanca" podrán conectar su Google Calendar.

**Límite:** 100 usuarios

---

## 📋 Proceso Semanal Recomendado

### Cada Lunes (5 minutos):

1. **Ejecuta el comando:**
   ```bash
   php artisan google:list-pending-users
   ```

2. **Copia los correos** que aparecen en pantalla

3. **Ve a Google Cloud Console:**
   - https://console.cloud.google.com/apis/credentials/consent
   - Proyecto: **diogenes-485019**
   - Sección: **"Usuarios de prueba"**
   - Clic en **"+ AGREGAR USUARIOS"**

4. **Pega los correos** (uno por línea)

5. **Guarda**

6. **Notifica a los usuarios** que ya pueden conectar su Google Calendar

---

## 🎯 Estrategia de Crecimiento

### Mes 1-2: Primeros 50 Usuarios
- ✅ Agrega usuarios manualmente cada semana
- ✅ Monitorea el uso y feedback
- ✅ Documenta casos de uso reales

### Mes 3: 50-80 Usuarios
- ✅ Continúa agregando usuarios
- 🚀 **INICIA SOLICITUD DE VERIFICACIÓN**
- ✅ Muestra a Google que tienes tracción real

### Mes 4: 80-100 Usuarios
- ⚠️ Acercándote al límite
- ⏳ Esperando aprobación de Google
- ✅ Prepara documentación de verificación

### Mes 5+: Verificación Aprobada
- 🎉 Sin límite de usuarios
- ✅ Cualquiera puede conectarse
- ✅ Escalabilidad ilimitada

---

## 📊 Monitoreo

### Ver Estadísticas Actuales:

```bash
php artisan tinker --execute="
echo 'Usuarios totales: ' . App\Models\User::count() . PHP_EOL;
echo 'Con Google conectado: ' . App\Models\User::whereNotNull('google_access_token')->count() . PHP_EOL;
echo 'Sin Google: ' . App\Models\User::whereNull('google_access_token')->count() . PHP_EOL;
echo 'Espacios disponibles: ' . (100 - App\Models\User::count()) . PHP_EOL;
"
```

---

## ⚠️ Cuando Llegues a 90 Usuarios

**ACCIÓN URGENTE:** Solicita la verificación inmediatamente

1. Sigue la guía: `GUIA_VERIFICACION_GOOGLE.md`
2. Muestra a Google tus métricas de uso
3. Proporciona evidencia de usuarios reales
4. Tiempo de aprobación: 2-4 semanas

---

## 🔄 Automatización (Opcional)

### Notificación Automática por Email

Cuando un usuario se registra, envíale un email:

```
Asunto: Activa tu Google Calendar en LegalCore

Hola [Nombre],

Tu cuenta está lista. Para recibir eventos en tu celular:

1. Ve a tu Perfil
2. Haz clic en "Conectar Google Calendar"
3. Autoriza el acceso

¡Listo! Los eventos aparecerán automáticamente.

Saludos,
Equipo LegalCore
```

---

## 📞 Soporte

### Si un usuario reporta error 403:

1. Verifica que su correo esté en la lista de Google Cloud
2. Si no está, agrégalo
3. Pídele que intente de nuevo en 5 minutos
4. Si persiste, revisa los logs: `storage/logs/laravel.log`

---

## 🎯 Checklist Semanal

- [ ] Ejecutar `php artisan google:list-pending-users`
- [ ] Agregar nuevos correos a Google Cloud Console
- [ ] Notificar a usuarios que ya pueden conectar
- [ ] Revisar métricas de uso
- [ ] Si estás cerca de 90 usuarios, iniciar verificación

---

## 📈 KPIs a Monitorear

| Métrica | Objetivo | Acción si no se cumple |
|---------|----------|------------------------|
| % de usuarios con Google conectado | >80% | Mejorar onboarding |
| Eventos sincronizados/día | >50 | Promover uso de agenda |
| Usuarios activos/semana | >60% | Engagement campaigns |
| Tiempo hasta conectar Google | <24h | Email de recordatorio |

---

**Última actualización:** 27 de enero de 2026  
**Próxima revisión:** Cuando llegues a 80 usuarios
