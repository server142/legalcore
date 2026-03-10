# Instrucciones: Enlace entre el Directorio y el Sistema Diogenes

Este documento explica la arquitectura y el flujo de datos que conecta el **Sistema Diogenes** (plataforma interna de gestión legal) con el **Directorio Público de Abogados**.

## 1. Concepto General

El sistema funciona bajo una arquitectura monolítica donde:
1.  **Diogenes (Sistema Interno):** Es la plataforma segura donde los abogados gestionan sus expedientes, clientes y usan la IA.
2.  **Directorio (Página Pública):** Es el buscador visible para cualquier persona en internet que permite encontrar abogados registrados en Diogenes.

**El "Enlace"** es la base de datos compartida. Los abogados que usan Diogenes pueden "proyectar" su perfil profesional hacia el Directorio Público mediante una configuración específica en su cuenta.

---

## 2. Arquitectura Técnica y Código

La conexión se realiza principalmente a través de modelos de Eloquent y componentes Livewire.

### A. Modelos de Base de Datos (El Puente)

1.  **`User.php` (El Abogado en Diogenes)**
    *   Representa al usuario dentro del sistema seguro.
    *   Tiene una relación `hasOne` con `DirectoryProfile`.
    *   *Ubicación:* `app/Models/User.php`

2.  **`DirectoryProfile.php` (El Perfil Público)**
    *   Almacena la información que el abogado quiere mostrar al mundo (Bio, Especialidades, Foto, Contacto).
    *   Tiene una relación `belongsTo` con `User`.
    *   Este modelo incluye un campo `is_public` que actúa como el "interruptor" para aparecer o desaparecer del directorio.
    *   *Ubicación:* `app/Models/DirectoryProfile.php`

### B. Componentes Clave

1.  **Gestor de Perfil (`DirectoryManager.php`) - Lado del Abogado**
    *   Permite al usuario autenticado (dentro de Diogenes) editar su información pública.
    *   Aquí es donde el abogado decide qué información compartir y activa la opción "Perfil Público".
    *   *Ubicación:* `app/Livewire/Profile/DirectoryManager.php`
    *   *Ruta:* `/perfil-publico`

2.  **Directorio Público (`PublicDirectory.php`) - Lado del Visitante**
    *   Es el buscador que ven los clientes potenciales.
    *   Consulta la base de datos buscando `DirectoryProfile` donde `is_public = true`.
    *   Permite filtrar por Estado, Especialidad y búsqueda de texto.
    *   *Ubicación:* `app/Livewire/PublicDirectory.php`
    *   *Ruta:* `/directorio`

3.  **Diogenes AI (`PublicSalesBot.php`)**
    *   Es el asistente virtual que aparece en las páginas públicas (incluido el directorio).
    *   Su función actual es ventas y soporte básico, actuando como "recepcionista" de la plataforma.
    *   *Ubicación:* `app/Livewire/PublicSalesBot.php`

---

## 3. Flujo de Datos (Paso a Paso)

1.  **Registro:** Un abogado se registra en la plataforma Diogenes (`User`).
2.  **Configuración:** El abogado entra a su panel de control -> "Perfil Público" (`DirectoryManager`).
3.  **Publicación:**
    *   El abogado llena sus datos (Biografía, Especialidades, Redes Sociales).
    *   Activa el switch "Hacer perfil público".
    *   Guarda los cambios.
4.  **Visualización:**
    *   El sistema crea/actualiza el registro en `directory_profiles`.
    *   Inmediatamente, el perfil aparece en `/directorio` para cualquier visitante.
5.  **Interacción:**
    *   Un visitante busca un abogado en el directorio.
    *   Si necesita ayuda con la plataforma en sí, el `PublicSalesBot` (Diogenes) está disponible en la esquina para asistir.

## 4. Notas para Mantenimiento

*   **Sincronización:** No hay procesos de sincronización complejos (cron jobs) necesarios para el directorio básico; la actualización es en tiempo real al guardar el formulario.
*   **Validación:** Actualmente `DirectoryProfile` verifica que el usuario tenga un nombre y datos básicos. Se podría agregar validación de "Suscripción Activa" en el futuro para limitar el directorio a usuarios de pago (ver `DirectoryManager.php` línea 85 comentada).
*   **Búsqueda:** La búsqueda actual usa `LIKE` de SQL. Si el directorio crece mucho (miles de abogados), se recomienda migrar a *Laravel Scout* o búsqueda vectorial para mantener la velocidad.
