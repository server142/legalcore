<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ManualPage;
use Illuminate\Support\Str;

class ManualUpdateFeaturesSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Módulo de Asesorías
        ManualPage::updateOrCreate(
            ['slug' => 'modulo-de-asesorias-inteligentes'],
            [
                'title' => 'Módulo de Asesorías Inteligentes',
                'content' => '# Gestión de Asesorías y Citas
                
El **Módulo de Asesorías** permite profesionalizar la primera interacción con sus clientes. En lugar de citas informales por WhatsApp, utilice este sistema para registrar, cobrar y dar seguimiento.

## Funcionalidades Clave

### 1. Citas Públicas (Landing de Citas)
El sistema genera automáticamente una página pública para cada cita confirmada. Usted puede enviar este enlace a su cliente para que consulte:
*   Fecha y Hora.
*   Ubicación (o enlace de Zoom/Meet).
*   Monto de la asesoría.

### 2. Acceso vía Código QR
Cada asesoría genera un Código QR único. 
*   **Uso:** Al llegar el cliente al despacho, puede escanearlo para "hacer check-in" o ver los detalles de su consulta.

### 3. Conversión a Expediente
Si la asesoría resulta exitosa y el cliente decide contratarlo, no necesita volver a capturar los datos.
*   Vaya a la asesoría.
*   Haga clic en el botón **"Convertir a Expediente"**.
*   El sistema creará inmediatamente un nuevo Expediente Judicial transfiriendo toda la información del cliente y las notas de la reunión.

## Flujo de Trabajo Recomendado
1.  **Registro:** Alta de la nueva solicitud de asesoría en el calendario.
2.  **Confirmación:** Envío del enlace público o QR al cliente.
3.  **Consulta:** El abogado registra notas privadas durante la reunión.
4.  **Cierre:** Se marca como "Realizada" y, si aplica, se convierte en Expediente.
',
                'image_path' => 'manual/asesorias.png', // Placeholder
                'order' => 25, // Adjust order as needed
                'required_role' => 'admin', // Or appropriate role
            ]
        );

        // 2. Soporte y Ayuda
        ManualPage::updateOrCreate(
            ['slug' => 'centro-de-ayuda-y-soporte'],
            [
                'title' => 'Centro de Ayuda y Soporte',
                'content' => '# Soporte Técnico y Ayuda

Diogenes incluye canales directos para resolver sus dudas.

## Páginas Públicas
*   **Centro de Ayuda (/ayuda):** Una base de conocimiento con Preguntas Frecuentes (FAQ) sobre facturación, uso del sistema y problemas comunes. Incluye un buscador inteligente.
*   **Contacto (/contacto):** Formulario directo para tickets de soporte y enlaces a nuestros canales de atención.

## Canales de Atención
*   **WhatsApp Directo:** Botón integrado en la plataforma para chatear con nuestro equipo de soporte técnico en tiempo real.
*   **Correo Electrónico:** soporte@diogenes.com.mx para consultas administrativas o de pagos.

## Reportar un Error
Si encuentra un fallo en el sistema:
1.  Tome una captura de pantalla del error.
2.  Vaya a **/contacto** o use el chat de WhatsApp.
3.  Describa los pasos que realizó antes de que apareciera el error.
',
                'image_path' => 'manual/soporte.png', 
                'order' => 95, 
            ]
        );

        // 3. Actualización de Expedientes (Semaforización)
        // We look for an existing page about Expedientes to update or append, but to be safe and avoid overwriting user edits too aggressively, let's create a specific feature page.
        ManualPage::updateOrCreate(
            ['slug' => 'control-de-terminos-y-semaforizacion'],
            [
                'title' => 'Control de Términos y Semaforización',
                'content' => '# Semaforización de Términos Judiciales

El sistema utiliza un código de colores (Semáforo) para garantizar que **nunca se le pase un término fatal**.

## Código de Colores
*   🔴 **ROJO (Crítico):** El término vence **HOY**. Requiere acción inmediata.
*   🟠 **NARANJA (Preventivo):** Vence en los próximos **1 a 3 días**. Prepare su promoción.
*   🟢 **VERDE (Seguro):** Faltan **4 días o más**. Está a tiempo.
*   ⚫ **NEGRO/GRIS:** Término vencido.

## ¿Cómo funciona?
Al editar un expediente, establezca la fecha en el campo **"Vencimiento del Término (Fatal)"**.
El sistema calculará automáticamente los días restantes y actualizará el color de la tarjeta en el tablero principal y dentro del expediente.

## Alertas
Además del color visual, el sistema puede enviar recordatorios (si tiene activadas las notificaciones) cuando un expediente entra en zona Roja.
',
                'image_path' => 'manual/semaforo.png',
                'order' => 15,
            ]
        );
        
         // 4. Expediente Digital Universal
        ManualPage::updateOrCreate(
            ['slug' => 'expediente-digital-universal'],
            [
                'title' => 'Expediente Digital Universal',
                'content' => '# Expediente Digital Universal

Diogenes centraliza toda la información de un caso en una sola vista. Ya no necesita carpetas físicas ni archivos dispersos en su computadora.

## Visor de Documentos Integrado
Puede visualizar archivos directamente en el navegador sin descargarlos:
*   **Documentos:** PDF, Word (vista previa).
*   **Multimedia:** Reproductor de Audio (mp3, wav) y Video (mp4) integrado para revisar audiencias grabadas.
*   **Imágenes:** Evidencia fotográfica.

## Pestañas de Organización
1.  **Actuaciones:** Bitácora cronológica de acuerdos y promociones.
2.  **Documentos:** Repositorio de archivos.
3.  **Agenda:** Eventos y audiencias específicas de este caso.
4.  **Notas IA:** Resúmenes y análisis generados por Diogenes Intelligence.
5.  **Comentarios:** Chat interno para el equipo legal sobre este asunto.
6.  **Finanzas:** Estado de cuenta, honorarios y pagos del cliente.
',
                'image_path' => 'manual/expediente-digital.png',
                'order' => 12,
            ]
        );
    }
}
