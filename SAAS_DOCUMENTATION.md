# Documentación del Sistema SaaS - LegalCore

Este documento detalla la implementación del modelo SaaS (Software as a Service), incluyendo la gestión de planes, suscripciones, pagos y control de acceso.

## 1. Estructura de Planes y Paquetes

Los planes se gestionan dinámicamente desde la base de datos.
- **Tabla:** `plans`
- **Gestión:** Panel de Super Admin -> SaaS -> Planes (`/admin/plans`)
- **Campos Clave:**
    - `slug`: Identificador único (ej: `trial`, `basico`, `profesional`).
    - `price`: Precio mensual.
    - `duration_in_days`: Duración del ciclo (usualmente 30 días).
    - `max_admin_users`: Límite de administradores por tenant.
    - `max_lawyer_users`: Límite de abogados (null = ilimitado).
    - `stripe_price_id`: ID del precio en Stripe (para producción).

## 2. Flujo de Registro y Suscripción

### A. Landing Page (`/`)
- Muestra los planes activos traídos de la BD.
- Al hacer clic en "Seleccionar Plan", redirige a `/register?plan={slug}`.

### B. Registro (`/register`)
- Crea el Usuario y el Tenant automáticamente.
- Asigna el plan seleccionado al Tenant.
- **Si es Trial:**
    - `subscription_status` = `trial`
    - `trial_ends_at` = +15 días (configurable).
    - Redirige al Dashboard.
- **Si es Plan de Pago:**
    - `subscription_status` = `pending_payment`
    - Redirige a `/billing/subscribe/{plan}`.

### C. Proceso de Pago (`/billing/subscribe/{plan}`)
- Muestra el resumen del pedido.
- (Simulado/Preparado para Stripe) Recolecta datos de tarjeta.
- Al confirmar pago:
    - `subscription_status` = `active`
    - `subscription_ends_at` = +30 días.
    - `is_active` = `true`.

## 3. Control de Acceso y Ciclo de Vida (Middleware)

El middleware `CheckSubscription` protege todas las rutas excepto las de facturación y perfil.

### Estados del Tenant:
1.  **Trial (Prueba):** Acceso total hasta `trial_ends_at`.
2.  **Active (Pagado):** Acceso total hasta `subscription_ends_at`.
3.  **Grace Period (Gracia):**
    - Se activa automáticamente cuando vence el Trial o la Suscripción Activa.
    - Duración configurable en `global_settings` (default: 3 días).
    - Permite acceso pero muestra advertencia roja.
4.  **Expired/Cancelled:**
    - Se activa al vencer el periodo de gracia.
    - Bloquea todo acceso y redirige a `/subscription/expired`.

## 4. Integración con Stripe (Pagos Recurrentes)

El sistema está preparado para usar **Laravel Cashier**.

### Configuración para Producción:
1.  Configurar llaves en `.env`:
    ```env
    STRIPE_KEY=pk_test_...
    STRIPE_SECRET=sk_test_...
    STRIPE_WEBHOOK_SECRET=whsec_...
    ```
2.  Crear productos y precios en el Dashboard de Stripe.
3.  Copiar los `price_id` de Stripe a la tabla `plans` en el admin del sistema.
4.  Descomentar la lógica de `createSetupIntent` y `newSubscription` en `App\Livewire\Billing\Subscribe`.
5.  Configurar el Webhook de Stripe apuntando a `/stripe/webhook` para manejar renovaciones automáticas (`invoice.payment_succeeded`) y fallos (`invoice.payment_failed`).

## 5. Alertas y Notificaciones

- **Banner Amarillo:** Aparece 3 días antes de que venza el trial o la suscripción.
- **Banner Rojo:** Aparece durante el periodo de gracia.
- **Bloqueo Total:** Pantalla de "Suscripción Expirada" con botón para renovar.

## 6. Gestión Administrativa (Super Admin)

El Super Admin tiene acceso a:
- **Tenants:** Ver lista, cambiar planes manualmente, extender trials, desactivar acceso.
- **Planes:** Crear/Editar precios y límites.
