<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ManualPage;
use App\Models\Plan;

class DirectorySetupSeeder extends Seeder
{
    public function run()
    {
        // 1. Create the detailed Manual Page
        $manualContent = <<<'EOT'
# Guía del Directorio Público de Abogados

## ¿Qué es el Directorio Público?
El Directorio Público es una vitrina digital diseñada para conectar a los abogados que utilizan nuestra plataforma con clientes potenciales en todo México. Al activar tu perfil, tu información profesional se vuelve visible en nuestro buscador público, permitiendo que personas que necesitan servicios legales te encuentren por **Especialidad**, **Ciudad** o **Nombre**.

![Vista Previa](https://via.placeholder.com/800x400?text=Vista+del+Directorio)

## ¿Por qué unirte?
1. **Visibilidad Gratuita**: Si ya tienes una suscripción activa, aparecer en el directorio no tiene costo adicional.
2. **Posicionamiento SEO**: Nuestras páginas están optimizadas para buscadores como Google, lo que aumenta las probabilidades de que aparezcas en búsquedas locales (ej. "Abogado Penalista en Xalapa").
3. **Contacto Directo**: Los clientes pueden contactarte directamente a tu WhatsApp, Teléfono o Correo sin intermediarios ni comisiones por lead.
4. **Respaldo Profesional**: Al ser parte de la red, transmites confianza al estar verificado por nuestra plataforma.

## Cómo Activar tu Perfil (Paso a Paso)

### 1. Accede a la Configuración
Dirígete al menú principal y selecciona **"Perfil Público"** o [Haz clic aquí para ir directamente](/perfil-publico).

### 2. Completa tu Información
Para destacar, asegúrate de llenar todos los campos:
- **Foto de Perfil**: Es vital. Los perfiles con foto reciben 4 veces más clics.
- **Titular (Headline)**: Una frase corta que te describa (Ej. "Especialista en Derecho Familiar y Divorcios").
- **Biografía**: Explica en 2-3 párrafos tu experiencia, casos de éxito y por qué deberían contratarte.
- **Especialidades**: Agrega hasta 10 etiquetas (Ej. Amparos, Civil, Mercantil). Esto es clave para que te encuentren en el buscador.
- **Ubicación**: Define tu Ciudad y Estado para aparecer en búsquedas locales.
- **Datos de Contacto**: Verifica que tu WhatsApp y teléfono sean correctos.

### 3. Activa la Visibilidad
En la parte superior derecha del formulario, encontrarás un interruptor que dice **"Perfil Oculto / Perfil Visible"**.
- Cámbialo a **VISIBLE** (se pondrá en verde).
- Haz clic en el botón **"Guardar Perfil"** al final de la página.

¡Listo! Tu tarjeta aparecerá inmediatamente en el [Directorio Público](/directorio).

## Gestión de Tu Presencia
- **Actualización**: Puedes editar tu información en cualquier momento. Los cambios se reflejan al instante.
- **Desactivación**: Si tienes exceso de trabajo o vas de vacaciones, puedes apagar el interruptor de visibilidad temporalmente sin perder tus datos.

## Soporte
Si tienes problemas para cargar tu foto o configurar tu perfil, contacta a soporte técnico desde el botón de ayuda en la esquina inferior derecha.
EOT;

        ManualPage::updateOrCreate(
            ['slug' => 'guia-directorio-publico'],
            [
                'title' => 'Cómo usar el Directorio Público',
                'content' => $manualContent,
                'order' => 15,
                'required_role' => 'abogado', // Visible to lawyers
                'image_path' => null,
            ]
        );

        // 2. Ensure "Directorio" Plan exists
        Plan::firstOrCreate(
            ['slug' => 'directory-only'],
            [
                'name' => 'Plan Directorio Exclusivo',
                'price' => 199.00, // Example price
                'stripe_price_id' => 'price_directory_placeholder', // To be updated with real ID
                'duration_in_days' => 30,
                'is_active' => true,
                'features' => [
                    'Perfil Destacado en Directorio',
                    'Enlace a WhatsApp Directo',
                    'Sin Límite de Contactos',
                    'Acceso Básico a Diogenes (Solo Perfil)',
                    'Soporte por Email'
                ],
                // Limits set to 0 or 1 to restrict full app usage if logic enforces it
                'max_expedientes' => 0, 
                'max_lawyer_users' => 1,
                'storage_limit_gb' => 0
            ]
        );
    }
}
