<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ManualPage;
use Illuminate\Support\Str;

class ManualSeeder extends Seeder
{
    public function run(): void
    {
        ManualPage::truncate();

        $pages = [
            [
                'title' => 'Introducción a LegalCore',
                'content' => 'Bienvenido a **LegalCore**, la plataforma integral diseñada para la gestión eficiente de su despacho jurídico. Este sistema ha sido desarrollado pensando en las necesidades críticas de los profesionales del derecho, permitiendo un control total sobre expedientes, términos procesales, agenda y facturación.

En este manual encontrará una guía detallada de cada módulo, con ejemplos prácticos y recomendaciones para optimizar su flujo de trabajo diario.',
                'image_path' => null,
                'order' => 1,
            ],
            [
                'title' => 'Panel de Control (Dashboard)',
                'content' => 'El **Dashboard** es su centro de mando. Al iniciar sesión, visualizará de forma inmediata el estado actual de su despacho:

*   **Expedientes Activos:** Número total de casos en curso.
*   **Vencimientos Próximos:** Alertas sobre términos que requieren atención inmediata.
*   **Indicadores Financieros:** Resumen de montos cobrados y pendientes de cobro.
*   **Últimos Expedientes:** Acceso rápido a los casos consultados recientemente.
*   **Términos Urgentes:** Listado prioritario de plazos legales por vencer.

**Ejemplo de uso:** Al iniciar su jornada, revise la sección de "Términos Urgentes" para priorizar las actuaciones del día.',
                'image_path' => 'manual/dashboard.png',
                'order' => 2,
            ],
            [
                'title' => 'Gestión de Expedientes',
                'content' => 'El módulo de **Expedientes** permite centralizar toda la información de sus casos judiciales.

**Funcionalidades clave:**
1.  **Registro de Nuevo Expediente:** Capture el número de expediente, título, materia (Civil, Penal, Laboral, etc.), juzgado y abogado responsable.
2.  **Seguimiento Procesal:** Actualice el estado del caso (En proceso, Ejecución, Suspendido, Cerrado).
3.  **Búsqueda Avanzada:** Localice rápidamente cualquier expediente por su número o nombre de las partes.

**Ejemplo de uso:** Para registrar un nuevo caso, haga clic en "Nuevo Expediente", complete los datos del juzgado y asigne el cliente correspondiente. Esto creará una ficha única donde podrá consultar toda la historia del proceso.',
                'image_path' => 'manual/expedientes.png',
                'order' => 3,
            ],
            [
                'title' => 'Administración de Clientes',
                'content' => 'Mantenga una base de datos organizada de sus representados en el módulo de **Clientes**.

**Opciones disponibles:**
*   **Personas Físicas y Morales:** El sistema permite diferenciar entre individuos y empresas.
*   **Datos de Contacto:** Almacene correos electrónicos, teléfonos y RFC para fines de facturación.
*   **Historial de Casos:** Desde el perfil del cliente, podrá visualizar todos los expedientes asociados a él.

**Ejemplo de uso:** Antes de iniciar un nuevo expediente, asegúrese de registrar al cliente. Si es una empresa, capture el RFC correctamente para facilitar la emisión de facturas posteriores.',
                'image_path' => 'manual/clientes.png',
                'order' => 4,
            ],
            [
                'title' => 'Agenda Judicial',
                'content' => 'La **Agenda** es una herramienta visual (calendario) para coordinar las actividades del despacho.

*   **Audiencias:** Registre fechas de audiencias con alertas automáticas.
*   **Citas:** Gestione reuniones con clientes o contrapartes.
*   **Colores por Tipo:** Identifique rápidamente la naturaleza del evento (Rojo para audiencias, Naranja para términos, Azul para citas).

**Ejemplo de uso:** Al recibir una notificación de audiencia, regístrela en la agenda seleccionando el expediente relacionado. Esto permitirá que todos los abogados asignados al caso estén informados.',
                'image_path' => 'manual/agenda.png',
                'order' => 5,
            ],
            [
                'title' => 'Sincronización con Google Calendar',
                'content' => '## ¿Por qué conectar mi Google Calendar?

Al conectar tu cuenta de Google Calendar, **todos los eventos del despacho aparecerán automáticamente en tu celular, computadora y smartwatch**, sin que tengas que hacer nada más. Recibirás notificaciones push y recordatorios en tiempo real.

---

## Pasos para Conectar (Solo 1 vez - 30 segundos)

### 1. Entra a tu Perfil
- Haz clic en tu nombre (arriba a la derecha)
- Selecciona **"Perfil"**

### 2. Conecta Google Calendar
- Busca la sección **"Google Calendar"**
- Haz clic en el botón **"Conectar Google Calendar"**
- Se abrirá una ventana de Google

### 3. Autoriza el Acceso
- Selecciona tu cuenta de Google (la que usas en tu celular)
- Haz clic en **"Permitir"** cuando te pida acceso a tu calendario
- **¡Listo!** Ya estás conectado

---

## ¿Qué eventos recibiré automáticamente?

El sistema detecta inteligentemente qué eventos debes recibir según tu rol en cada caso:

| Escenario | Quién recibe el evento | Ejemplo |
|-----------|------------------------|---------|
| **Evento SIN expediente, SIN invitados** | Solo el creador | Cita personal del abogado |
| **Evento SIN expediente, CON invitados** | Creador + Invitados seleccionados | Reunión de estrategia con 3 abogados |
| **Evento CON expediente, SIN invitados** | Creador + Responsable + Asignados al expediente | Audiencia del Exp. 123/2024 |
| **Evento CON expediente, CON invitados** | Creador + Responsable + Asignados + Invitados | Audiencia importante con todo el equipo |

---

## Ejemplos Prácticos

### Ejemplo 1: Audiencia de un Expediente
**Situación:** El expediente 456/2024 tiene como responsable a Juan y como asignados a María y Pedro.

**Acción:** Alguien crea una audiencia para el 15 de febrero a las 10:00 AM.

**Resultado:** Juan, María y Pedro ven la audiencia automáticamente en sus celulares con recordatorio 30 minutos antes.

---

### Ejemplo 2: Reunión sin Expediente
**Situación:** El administrador necesita reunirse con 2 abogados específicos.

**Acción:** Crea un evento tipo "Cita", NO selecciona expediente, pero marca los checkboxes de Ana y Luis.

**Resultado:** Solo el administrador, Ana y Luis reciben el evento en sus calendarios.

---

### Ejemplo 3: Término Legal Urgente
**Situación:** Expediente con responsable (Carlos) + asignado (Diana).

**Acción:** Se registra un término de vencimiento para el 20 de enero.

**Resultado:** Carlos y Diana reciben el recordatorio en sus celulares. El sistema les notifica 3 días antes, 1 día antes y el mismo día.

---

## Indicadores Visuales en la Agenda

Para identificar rápidamente qué eventos están sincronizados con Google:

*   **Icono de Nube (☁️):** Aparece junto al título del evento en el calendario si ya está en Google.
*   **Badge Verde:** Al abrir un evento, verás un badge que dice "Sincronizado con Google" si la conexión fue exitosa.
*   **Badge Gris:** Si dice "No sincronizado", significa que el abogado no ha conectado su cuenta de Google.

---

## Ventajas de la Sincronización

✅ **Notificaciones Push:** Recibes alertas en tu celular aunque no estés en el sistema.  
✅ **Sincronización Instantánea:** Los cambios se reflejan en segundos.  
✅ **Multiplataforma:** Funciona en iPhone, Android, Web y smartwatches.  
✅ **Recordatorios Inteligentes:** Google te avisará 30 minutos antes de cada evento.  
✅ **Sin duplicados:** El sistema evita crear eventos repetidos.

---

## Preguntas Frecuentes

**¿Puedo desconectar mi calendario?**  
Sí, en cualquier momento puedes ir a tu perfil y hacer clic en "Desconectar Google Calendar". Los eventos ya creados permanecerán en tu calendario de Google hasta que los elimines manualmente.

**¿Qué pasa si no conecto mi Google?**  
Podrás seguir usando la agenda del sistema normalmente, pero NO recibirás notificaciones en tu celular ni verás los eventos fuera del sistema.

**¿Los eventos se eliminan si los borro del sistema?**  
Sí, si eliminas un evento en LegalCore, también se eliminará automáticamente de tu Google Calendar.

**¿Puedo editar eventos desde Google Calendar?**  
No se recomienda. Los cambios deben hacerse desde LegalCore para mantener la sincronización correcta con todos los abogados involucrados.

---

## Solución de Problemas

**No me llegan los eventos:**  
1. Verifica que conectaste tu cuenta correctamente (debe aparecer un badge verde en tu perfil).
2. Revisa que estés usando la misma cuenta de Google en tu celular.
3. Asegúrate de que las notificaciones de Google Calendar estén activadas en tu dispositivo.

**Los eventos aparecen duplicados:**  
Esto puede ocurrir si conectaste y desconectaste varias veces. Elimina los duplicados manualmente desde Google Calendar.

**Quiero usar otro correo para el calendario:**  
Ve a tu perfil, sección "Correo para Calendario", y actualiza el correo. Luego desconecta y vuelve a conectar con la nueva cuenta.',
                'image_path' => 'manual/google-calendar.png',
                'order' => 6,
            ],
            [
                'title' => 'Control de Términos Procesales',
                'content' => 'El módulo de **Términos** es crítico para evitar la preclusión de derechos y asegurar la vigencia de las actuaciones legales.

**¿Cómo se registra un término?**
El registro se realiza siempre vinculado a un expediente específico para mantener la trazabilidad:
1.  **Desde el Expediente:** Ingrese al caso y utilice el componente "Agregar Actuación".
2.  **Definición:** Capture el título (ej. "Presentar Recurso de Apelación") y la fecha de notificación.
3.  **Activación de Plazo:** Marque la casilla **"Es plazo"**. Esto habilitará el campo "Fecha de Vencimiento".
4.  **Fecha Fatal:** Seleccione la fecha límite. El sistema asignará automáticamente el estado "Pendiente".

**Resultados y Alertas:**
*   **Dashboard:** El término se sumará al contador de "Vencimientos Próximos" y aparecerá en la lista de "Términos Urgentes" si faltan pocos días.
*   **Módulo de Control:** Podrá filtrar por estado (Pendiente, Cumplido, Vencido) y marcar actuaciones como concluidas.
*   **Historial:** La ficha del expediente mostrará visualmente el cumplimiento o retraso de cada plazo.

**Ejemplo de uso:** Si registra hoy un término para el 20 de enero, el sistema lo resaltará en rojo en el Dashboard y lo pondrá al inicio de su lista de prioridades hasta que sea marcado como "Cumplido".',
                'image_path' => 'manual/terminos.png',
                'order' => 7,
            ],
            [
                'title' => 'Facturación y Cobranza',
                'content' => 'Gestione la salud financiera de su despacho en el módulo de **Facturación**.

*   **Emisión de Facturas:** Cree comprobantes detallando honorarios y gastos.
*   **Control de Pagos:** Marque facturas como "Pagadas" o "Pendientes".
*   **Cálculo de Impuestos:** El sistema calcula automáticamente el IVA y subtotales.

**Ejemplo de uso:** Al concluir una etapa procesal, genere la factura correspondiente al cliente. Podrá descargar el reporte en PDF para enviarlo por correo electrónico.',
                'image_path' => 'manual/facturacion.png',
                'order' => 8,
            ],
            [
                'title' => 'Bitácora de Seguridad',
                'content' => 'La **Bitácora** registra cada acción relevante realizada en el sistema.

*   **Transparencia:** Sepa quién creó, modificó o eliminó un registro.
*   **Auditoría:** Útil para revisiones internas y control de calidad.
*   **Filtros por Módulo:** Busque acciones específicas realizadas en expedientes o facturación.

**Ejemplo de uso:** Si un expediente fue modificado por error, consulte la bitácora para identificar qué usuario realizó el cambio y en qué fecha exacta.',
                'image_path' => 'manual/bitacora.png',
                'order' => 9,
            ],
            [
                'title' => 'Configuración del Despacho',
                'content' => 'Personalice LegalCore para que se adapte a su identidad corporativa.

*   **Datos del Titular:** Nombre del despacho y dirección oficial.
*   **Logotipo:** Suba el logo de su firma para que aparezca en reportes y facturas.
*   **Notificaciones SMS:** Configure el envío de recordatorios automáticos a clientes y abogados.

**Ejemplo de uso:** Suba su logotipo en formato PNG de alta resolución para que sus reportes de expediente tengan una presentación profesional ante sus clientes.',
                'image_path' => 'manual/configuracion.png',
                'order' => 10,
            ],
        ];

        foreach ($pages as $page) {
            ManualPage::create([
                'title' => $page['title'],
                'slug' => Str::slug($page['title']),
                'content' => $page['content'],
                'image_path' => $page['image_path'],
                'order' => $page['order'],
            ]);
        }
    }
}
