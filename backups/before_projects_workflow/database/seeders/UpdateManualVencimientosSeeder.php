<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ManualPage;
use Illuminate\Support\Str;

class UpdateManualVencimientosSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Update existing "Gestión de Expedientes" to mention Kanban and Search
        $expedientesPage = ManualPage::where('title', 'Gestión de Expedientes')->first();
        if ($expedientesPage) {
            $expedientesPage->update([
                'content' => 'El módulo de **Expedientes** permite centralizar toda la información de sus casos judiciales con herramientas visuales modernas.

**Funcionalidades clave:**
1.  **Vistas Duales:**
    *   **Modo Lista:** Ideal para revisiones rápidas y gestión masiva.
    *   **Tablero Kanban:** Una vista visual por columnas que permite mover expedientes entre estados procesales simplemente arrastrándolos (*Drag & Drop*).
2.  **Buscador Inteligente:** Localice expedientes por número, título o nombre del cliente de forma instantánea tanto en la lista como en el tablero.
3.  **Seguimiento de Actividad:** Cada tarjeta en el tablero le indica la materia, la fecha de la última modificación (**MOD**) y el vencimiento legal (**FATAL**).

**Ejemplo de uso:** Use el Tablero Kanban para arrastrar un expediente de "Radicación" a "Notificaciones" cuando cambie su estado. El sistema registrará el movimiento automáticamente.'
            ]);
        }

        // 2. Create or Update "Control de Vencimientos Fatales" (Replacing or supplementing terms)
        ManualPage::updateOrCreate(
            ['title' => 'Vencimientos y Alertas Proactivas'],
            [
                'slug' => Str::slug('Vencimientos y Alertas Proactivas'),
                'order' => 7,
                'content' => 'La gestión del tiempo es el corazón de la práctica legal. Diogenes incluye un sistema de **Vencimientos Fatales** diseñado para que nunca se pierda un término legal.

### **¿Qué es la Fecha Fatal?**
Es el límite máximo legal para realizar una actuación. A diferencia de un evento común, la Fecha Fatal genera una cadena de alertas críticas.

### **Alertas Automáticas**
Usted no necesita revisar el sistema constantemente; el sistema le avisará vía correo electrónico:
*   **5 Días Antes:** Aviso preventivo.
*   **3 Días Antes (Naranja):** Alerta de urgencia media.
*   **24 Horas Antes (Rojo):** Alerta de máxima prioridad.
*   **12 Horas Antes:** Aviso final de emergencia.

### **Reporte Semanal de los Lunes**
Todos los lunes a las 8:00 AM, el sistema envía un **Reporte Maestro** de vencimientos:
*   **Administradores:** Reciben un resumen de todo el despacho para supervisar la carga de trabajo.
*   **Abogados:** Reciben su lista personal de términos pendientes para la semana.

**Configuración:** Al crear o editar un expediente, asegúrese de capturar la "Fecha Fatal" en el campo de vencimiento. El sistema se encargará del resto.',
                'image_path' => 'manual/vencimientos-alertas.png'
            ]
        );

        // 3. Create "Visor de Documentos Maximizable"
        ManualPage::updateOrCreate(
            ['title' => 'Visor de Archivos y Seguridad'],
            [
                'slug' => Str::slug('Visor de Archivos y Seguridad'),
                'order' => 8,
                'content' => 'Revisar pruebas y documentos es ahora mucho más cómodo con nuestro **Visor Pro**.

### **Maximizar para Lectura Profunda**
Junto al botón de cerrar de cualquier documento (PDF o Imagen), encontrará un icono de **Expandir**. Al presionarlo:
*   El documento ocupará **toda la pantalla**.
*   Podrá scrollear documentos largos de forma fluida.
*   Los controles se mantienen siempre visibles en la parte superior.

### **Seguridad de la Información**
Para proteger la integridad de sus expedientes, hemos implementado una política de eliminación estricta:
*   **Solo el autor** (quien subió el archivo) puede eliminarlo.
*   Los **Administradores** del despacho mantienen control total para eliminar cualquier archivo.
*   Los demás colaboradores pueden visualizar y descargar, pero no borrar contenido ajeno.

**Consejo:** Use el visor maximizado para leer expedientes en PDF directamente desde su tablet o computadora sin distracciones.',
                'image_path' => 'manual/visor-seguridad.png'
            ]
        );

        // Adjust order of subsequent pages
        $remaining = ManualPage::whereNotIn('title', [
            'Introducción a LegalCore', 
            'Panel de Control (Dashboard)', 
            'Gestión de Expedientes',
            'Administración de Clientes',
            'Agenda Judicial',
            'Sincronización con Google Calendar',
            'Vencimientos y Alertas Proactivas',
            'Visor de Archivos y Seguridad'
        ])->orderBy('order')->get();

        $startOrder = 9;
        foreach($remaining as $page) {
            $page->update(['order' => $startOrder++]);
        }
    }
}
