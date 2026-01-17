# LegalCore SAAS - Sistema de Gestión Jurídica

LegalCore SAAS es una plataforma integral diseñada para despachos jurídicos en México, permitiendo la gestión multi-tenant de expedientes, clientes, documentos y finanzas.

## Stack Tecnológico
- **Backend:** Laravel 12, PHP 8.3+
- **Frontend:** Livewire v3, TailwindCSS, Alpine.js
- **Base de Datos:** MySQL 8+ (Configurado con SQLite para demo rápida)
- **Roles:** Spatie Laravel-Permission

## Características Principales
- **Multi-tenancy:** Aislamiento total de datos por empresa/tenant.
- **Gestión de Expedientes:** Control completo de asuntos legales, materias y juzgados.
- **Actuaciones y Plazos:** Registro de movimientos procesales con cálculo de vencimientos.
- **Portal del Cliente:** Acceso restringido para que los clientes consulten sus asuntos.
- **Gestión Documental:** Carga segura de archivos por expediente.
- **Agenda Jurídica:** Calendario de audiencias y citas.
- **Facturación:** Control de honorarios y estados de pago.

## Instalación Local

1. **Clonar el repositorio y entrar al directorio:**
   ```bash
   cd Despacho
   ```

2. **Instalar dependencias de PHP:**
   ```bash
   composer install
   ```

3. **Instalar dependencias de JS:**
   ```bash
   npm install
   npm run build
   ```

4. **Configurar el entorno:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Ejecutar migraciones y seeders:**
   ```bash
   php artisan migrate:fresh --seed
   ```

6. **Iniciar el servidor:**
   ```bash
   php artisan serve
   ```

## Credenciales de Acceso (Demo)

| Rol | Email | Password |
| --- | --- | --- |
| **Super Admin** | `admin@legalcore.com` | `password` |
| **Admin Tenant** | `admin@mendez.com` | `password` |
| **Abogado** | `juan@mendez.com` | `password` |
| **Cliente** | `contacto@patito.com` | `password` |

## Estructura del Proyecto
- `app/Models`: Modelos con Trait `BelongsToTenant`.
- `app/Traits/BelongsToTenant.php`: Lógica de aislamiento de datos.
- `app/Http/Middleware/TenantMiddleware.php`: Identificación del tenant en sesión.
- `app/Livewire`: Componentes de la interfaz dinámica.
- `database/migrations`: Esquema de base de datos relacional.

## Cumplimiento Legal (México)
- Estructura preparada para **LFPDPPP** (Aviso de Privacidad).
- Estructura financiera compatible con **CFDI 4.0**.
- Manejo de términos y plazos procesales mexicanos.

---
Desarrollado por Carlos Segura Yoquigue para LegalCore.
