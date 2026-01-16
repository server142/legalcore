# Guía de Verificación - Nuevas Funcionalidades

## ✅ Correcciones Realizadas

### 1. Dashboard para Abogados
**Problema:** El abogado no podía ver el dashboard
**Solución:** Actualizado `resources/views/dashboard.blade.php` para permitir acceso a abogados

**Cómo verificar:**
1. Iniciar sesión como: `juan@mendez.com` / `password`
2. Ir a: `http://localhost:8000/dashboard`
3. **Resultado esperado:** Debe mostrar:
   - Expedientes Activos (solo los asignados a Juan)
   - Vencimientos Próximos
   - Total Clientes (en lugar de estadísticas financieras)
   - Últimos Expedientes (solo los de Juan)
   - Términos Urgentes (solo de expedientes de Juan, a menos que tenga permiso "view all terminos")

---

## 🆕 Nuevas Funcionalidades

### 2. Sistema de Mensajería Interna

**Ubicación:** `/mensajes`

**Cómo verificar:**
1. Iniciar sesión como abogado
2. Observar el **ícono de sobre** en la esquina superior derecha (junto al nombre de usuario)
3. Si hay mensajes no leídos, mostrará un **badge rojo** con el número
4. Hacer clic en el ícono o ir a `/mensajes`
5. **Funcionalidades:**
   - ✅ Ver bandeja de entrada
   - ✅ Enviar nuevo mensaje (botón "+ Nuevo Mensaje")
   - ✅ Seleccionar destinatario de la lista de usuarios del despacho
   - ✅ Ver mensajes recibidos y enviados
   - ✅ Marcar como leído automáticamente al abrir
   - ✅ Registro en bitácora de cada mensaje enviado

**Prueba:**
```
1. Como abogado, enviar mensaje a admin
2. Cerrar sesión
3. Iniciar sesión como admin
4. Verificar que aparece badge con "1"
5. Abrir mensajes y leer
6. Verificar que el badge desaparece
7. Ir a /bitacora y buscar el registro del mensaje
```

---

### 3. Gestión de Asignaciones de Expedientes

**Ubicación:** Dentro de cada expediente (botón "Gestionar Asignaciones")

**Cómo verificar:**
1. Iniciar sesión como **admin** (no como abogado)
2. Ir a cualquier expediente: `/expedientes/{id}`
3. Buscar el botón **"Gestionar Asignaciones"** (con ícono de usuarios)
4. Hacer clic para ir a `/expedientes/{id}/asignaciones`

**Funcionalidades:**
- ✅ **Cambiar Abogado Responsable:** Seleccionar nuevo abogado principal
- ✅ **Asignar Múltiples Abogados:** Marcar checkboxes para trabajo colaborativo
- ✅ **Registro en Bitácora:** Cada cambio queda registrado
- ✅ **Protección:** Solo usuarios con permiso "manage users" ven este botón

**Prueba:**
```
1. Como admin, ir a un expediente
2. Clic en "Gestionar Asignaciones"
3. Cambiar el abogado responsable
4. Asignar 2-3 abogados adicionales
5. Guardar cambios
6. Ir a /bitacora y verificar el registro
7. Cerrar sesión e iniciar como el nuevo abogado responsable
8. Verificar que ahora ve ese expediente en su lista
```

---

### 4. Permiso "Ver Todos los Términos"

**Ubicación:** Módulo de Roles (`/admin/roles`)

**Cómo verificar:**
1. Iniciar sesión como **admin**
2. Ir a: `/admin/roles`
3. Editar el rol "abogado"
4. Buscar en la lista de permisos: **"view all terminos"**
5. Marcar/desmarcar según necesidad

**Comportamiento:**
- ✅ **SIN permiso:** El abogado solo ve términos de SUS expedientes asignados
- ✅ **CON permiso:** El abogado ve TODOS los términos del despacho

**Prueba:**
```
1. Como admin, ir a /admin/roles
2. Editar rol "abogado"
3. DESMARCAR "view all terminos"
4. Guardar
5. Cerrar sesión e iniciar como abogado
6. Ir a /terminos
7. Verificar que solo ve términos de sus expedientes
8. Cerrar sesión, volver como admin
9. Editar rol "abogado" y MARCAR "view all terminos"
10. Guardar
11. Cerrar sesión e iniciar como abogado
12. Ir a /terminos
13. Verificar que ahora ve TODOS los términos
```

---

## 📊 Resumen de Permisos del Rol Abogado

Por defecto, el rol "abogado" tiene:
- ✅ `manage own expedientes` - Gestionar sus expedientes
- ✅ `upload documents` - Subir documentos
- ✅ `view agenda` - Ver agenda
- ✅ `view terminos` - Ver términos

**Administrable desde /admin/roles:**
- 🔧 `view all terminos` - Ver todos los términos (no solo los suyos)
- 🔧 `view all expedientes` - Ver todos los expedientes (no solo los suyos)

---

## 🔍 Verificación de Integridad

**Funcionalidades existentes que NO deben verse afectadas:**
- ✅ Login/Logout
- ✅ Expedientes (crear, ver, editar)
- ✅ Clientes (crear, ver)
- ✅ Agenda (calendario FullCalendar)
- ✅ Términos (filtros por estado)
- ✅ Facturación (solo admin)
- ✅ Bitácora (solo admin)
- ✅ Manual de usuario
- ✅ Configuración del despacho

---

## 🚨 Problemas Conocidos Resueltos

1. ✅ **Dashboard no visible para abogados** - RESUELTO
2. ✅ **Permiso "view all terminos" no aparecía en roles** - RESUELTO
3. ✅ **Error FullCalendar en agenda** - RESUELTO

---

## 📝 Notas Importantes

1. **Mensajes:** El badge de notificaciones se actualiza automáticamente al marcar como leído
2. **Asignaciones:** Los cambios de asignación NO afectan el historial del expediente
3. **Bitácora:** Todos los mensajes y cambios de asignación quedan registrados
4. **Seguridad:** Las asignaciones solo pueden ser gestionadas por usuarios con permiso "manage users"

---

## 🔐 Credenciales de Prueba

**Abogado:**
- Email: `juan@mendez.com`
- Password: `password`

**Admin:**
- Email: `admin@legalcore.com`
- Password: `password`

---

Fecha: 2026-01-16
Versión: 1.0
