# 🔐 GUÍA COMPLETA: Verificación de Google API para SaaS

## ⚠️ IMPORTANTE
Como LegalCore es un SaaS (múltiples clientes), **DEBES** verificar tu aplicación con Google. No hay alternativa.

---

## 📋 Paso 1: Publicar Documentos Legales

### 1.1 Subir cambios a tu servidor
```bash
git add .
git commit -m "add: privacy policy and terms of service for Google verification"
git push
```

### 1.2 En tu VPS
```bash
cd /ruta/a/legalcore
git pull origin main
php artisan view:clear
php artisan config:clear
```

### 1.3 Verificar que las URLs funcionan
Abre en tu navegador:
- `https://diogenes.com.mx/privacy`
- `https://diogenes.com.mx/terms`

**✅ Deben mostrarse correctamente antes de continuar**

---

## 📋 Paso 2: Preparar la Solicitud de Verificación

### 2.1 Información que Google te pedirá:

| Campo | Valor |
|-------|-------|
| **Nombre de la aplicación** | LegalCore |
| **URL del sitio web** | https://diogenes.com.mx |
| **Política de Privacidad** | https://diogenes.com.mx/privacy |
| **Términos de Servicio** | https://diogenes.com.mx/terms |
| **Logo de la app** | (Necesitas subirlo - 120x120px mínimo) |
| **Correo de soporte** | soporte@legalcore.com |

### 2.2 Descripción del uso de Google Calendar API

**Copia y pega esto en el formulario:**

```
LegalCore es una plataforma SaaS para gestión de despachos jurídicos en México. 

Usamos Google Calendar API para sincronizar automáticamente eventos legales (audiencias, términos procesales y citas) con los calendarios de los abogados.

Alcance solicitado:
- https://www.googleapis.com/auth/calendar

Justificación:
Los abogados necesitan recibir recordatorios de audiencias y términos legales directamente en sus dispositivos móviles. La sincronización con Google Calendar permite:
1. Notificaciones push en tiempo real
2. Recordatorios automáticos de eventos críticos
3. Acceso multiplataforma (web, móvil, smartwatch)

Uso de datos:
- Solo creamos, leemos, actualizamos y eliminamos eventos creados por nuestra plataforma
- NO accedemos a eventos personales del usuario
- Los datos se usan exclusivamente para sincronización de calendario
- NO compartimos datos con terceros
- Los usuarios pueden desconectar su cuenta en cualquier momento

Cumplimiento:
- Cumplimos con la Ley Federal de Protección de Datos Personales (México)
- Implementamos encriptación SSL/TLS
- Los tokens se almacenan de forma segura
```

---

## 📋 Paso 3: Solicitar la Verificación

### 3.1 Ir a Google Cloud Console
1. Ve a https://console.cloud.google.com/
2. Selecciona tu proyecto: **diogenes-485019**
3. Ve a **APIs y servicios** → **Pantalla de consentimiento de OAuth**

### 3.2 Cambiar a Producción
1. En "Estado de publicación", haz clic en **"PUBLICAR APLICACIÓN"**
2. Confirma que quieres publicar
3. Verás un banner que dice "Verificación necesaria"

### 3.3 Iniciar Verificación
1. Haz clic en **"PREPARAR PARA VERIFICACIÓN"**
2. Completa el formulario con la información del Paso 2
3. Sube tu logo (mínimo 120x120px, formato PNG o JPG)
4. Agrega las URLs de privacidad y términos
5. Haz clic en **"ENVIAR PARA VERIFICACIÓN"**

---

## 📋 Paso 4: Crear Video Demostrativo (Opcional pero Recomendado)

Google puede pedirte un video mostrando cómo usas la API. Graba un video de 2-3 minutos mostrando:

1. **Login al sistema** (0:00-0:15)
2. **Ir a Perfil** (0:15-0:30)
3. **Conectar Google Calendar** (0:30-1:00)
   - Mostrar el botón "Conectar"
   - Mostrar la pantalla de autorización de Google
   - Mostrar confirmación exitosa
4. **Crear un evento** (1:00-1:45)
   - Crear una audiencia con expediente
   - Mostrar que se sincroniza
5. **Ver en Google Calendar** (1:45-2:30)
   - Abrir Google Calendar en otra pestaña
   - Mostrar que el evento aparece
   - Mostrar que se puede editar desde el sistema
6. **Desconectar** (2:30-3:00)
   - Mostrar cómo desconectar la cuenta

**Herramientas recomendadas:**
- OBS Studio (gratis)
- Loom (gratis hasta 5 min)
- Camtasia (pago)

Sube el video a YouTube (puede ser no listado) y proporciona el enlace a Google.

---

## ⏱️ Tiempos de Espera

| Etapa | Tiempo Estimado |
|-------|-----------------|
| Revisión inicial | 3-5 días hábiles |
| Solicitud de información adicional | Variable |
| Aprobación final | 1-2 semanas |
| **TOTAL** | **2-4 semanas** |

---

## 🚨 Mientras Esperas la Verificación

### Opción Temporal: Modo de Prueba

Mientras Google revisa tu solicitud, puedes usar el modo de prueba:

1. Ve a **Pantalla de consentimiento de OAuth**
2. En **"Usuarios de prueba"**, agrega correos de tus clientes piloto
3. Límite: 100 usuarios

**Importante:** Esto es solo temporal. Una vez verificado, todos podrán conectarse.

---

## ✅ Checklist Pre-Solicitud

Antes de enviar la solicitud, verifica:

- [ ] URLs de privacidad y términos funcionan públicamente
- [ ] Logo de la aplicación preparado (120x120px mínimo)
- [ ] Correo de soporte configurado y funcionando
- [ ] Descripción del uso de API preparada
- [ ] Video demostrativo grabado (opcional)
- [ ] Aplicación publicada (no en modo borrador)

---

## 📞 Contacto con Google

Si Google te pide más información:

1. **Responde rápido** (dentro de 48 horas)
2. **Sé específico** sobre el uso de datos
3. **Proporciona capturas** de pantalla si te las piden
4. **Menciona cumplimiento** con políticas de datos

Email de soporte de Google: oauth-verification@google.com

---

## 🎯 Después de la Aprobación

Una vez aprobado:

1. **Notifica a tus clientes** que ya pueden conectar Google Calendar
2. **Actualiza el manual** si es necesario
3. **Monitorea los logs** para detectar problemas
4. **Mantén actualizados** los documentos legales

---

## 🆘 Si te Rechazan

Google puede rechazar si:
- Las URLs no funcionan
- La descripción es vaga
- Falta información de contacto
- El logo no cumple requisitos

**Solución:** Corrige lo que te indiquen y vuelve a enviar. No hay límite de intentos.

---

**Fecha:** 27 de enero de 2026  
**Versión:** 1.0  
**Próxima revisión:** Después de la aprobación de Google
