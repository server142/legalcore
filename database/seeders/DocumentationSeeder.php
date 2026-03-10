<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ManualPage;
use Illuminate\Support\Facades\DB;

class DocumentationSeeder extends Seeder
{
    public function run()
    {
        // Limpiamos la tabla para regenerar todo el manual
        DB::table('manual_pages')->truncate();

        $pages = [
            // ============================================================
            // SECCIÓN 1: ADMINISTRADOR (CONFIGURACIÓN Y DEPLOY)
            // ============================================================
            [
                'title'   => '👑 1. Primeros Pasos: Configuración Inicial',
                'slug'    => 'admin-onboarding',
                'order'   => 1,
                'content' => '# Bienvenido, Administrador. Comencemos.

Antes de cargar su primer expediente, es vital configurar la identidad de su despacho.

### 1. Configuración del Despacho
Diríjase a `Configuración > Ajustes Generales`. Aquí definirá:
*   **Identidad:** Suba el **Logotipo** de su firma. Este aparecerá en todos los reportes y recibos PDF.
*   **Datos Fiscales:** Razón Social, RFC y Dirección. Estos datos encabezarán sus documentos oficiales.
*   **Zona Horaria:** Asegúrese de que esté en `America/Mexico_City` (o su zona) para que las alertas de términos sean precisas.

### 2. Alta de Equipo (Abogados y Pasantes)
Un despacho es su gente. Vaya a `Configuración > Usuarios` para invitar a su equipo.
1.  Haga clic en **"Invitar Usuario"**.
2.  Ingrese Nombre y Correo Electrónico.
3.  **Haga clic en el Rol adecuado:**
    *   **Abogado:** Puede gestionar expedientes y ver agenda.
    *   **Pasante:** Acceso limitado (generalmente solo lectura o carga de archivos).
    *   **Administrador:** Acceso total (Cuidado con a quién le da este rol).

> **Tip:** El usuario recibirá un correo con un enlace temporal para establecer su contraseña.
'
            ],
            [
                'title'   => '💳 2. Planes, Pagos y Suscripción',
                'slug'    => 'admin-planes',
                'order'   => 2,
                'content' => '# Gestión de su Suscripción Diogenes

Entienda cómo funciona su ciclo de facturación y los límites de su cuenta.

### Plan Inicial (Prueba Gratuita)
Al registrarse, usted disfruta del **Plan Trial** por **30 días**.
*   **Incluye:** Usuarios ilimitados, IA ilimitada y almacenamiento completo.
*   **Al finalizar:** Deberá seleccionar un plan de pago para continuar accediendo a sus datos.

### Cómo cambiar de Plan
Vaya a `Configuración > Suscripción`.
1.  Verá los planes disponibles (Básico, Pro, Enterprise).
2.  Seleccione **"Mejorar Plan"** o **"Cambiar Plan"**.
3.  Será redirigido a la pasarela segura (Stripe) para ingresar su tarjeta.

### Facturación del Servicio
Sus facturas por el uso de Diogenes SaaS se generarán mensualmente y llegarán a su correo registrado. Puede descargarlas también desde la sección de Suscripción.
'
            ],
            [
                'title'   => '⚙️ 3. Configuración Avanzada y Bitácora',
                'slug'    => 'admin-avanzado',
                'order'   => 3,
                'content' => '# Control Total y Auditoría

Herramientas de poder para el Administrador del Despacho.

### Bitácora de Seguridad (Audit Logs)
¿Alguien borró un expediente importante? ¿Quién modificó ese acuerdo?
Visite el módulo **Bitácora**.
*   Registra **CADA acción** realizada en el sistema: Login, Creación, Edición, Eliminación.
*   Muestra: Usuario, Fecha, IP, Acción y Datos Afectados.
*   Es inmutable: Nadie puede borrar estos registros.

### Personalización de IA (Prompting)
En `Configuración > Inteligencia Artificial`, puede ajustar el comportamiento de Diogenes.
*   **Prompt del Sistema:** Defina si quiere que la IA sea agresiva, conciliadora o académica.
*   **Modelo:** Elija entre GPT-4 (más inteligente, más caro) o GPT-3.5 (más rápido).
'
            ],

            // ============================================================
            // SECCIÓN 2: OPERATIVA (ABOGADOS - EL DÍA A DÍA)
            // ============================================================
            [
                'title'   => '📊 4. Interpretando el Dashboard',
                'slug'    => 'uso-dashboard',
                'order'   => 4,
                'content' => '# Su Centro de Mando

El Dashboard es lo primero que ve al iniciar sesión. Está diseñado para responder: *"¿Qué es urgente hoy?"*

### Secciones Clave
1.  **KPIs Superiores:** Tarjetas con números grandes (Total Expedientes, Clientes Activos, Ingresos del Mes).
2.  **Eventos Próximos:** Lista de audiencias y citas para los **siguientes 3 días**.
3.  **Términos por Vencer:** ¡Cuidado aquí! Lista roja de términos que vencen en menos de 48 horas.
4.  **Actividad Reciente:** Un feed estilo red social de lo último que han hecho sus compañeros en los expedientes.
'
            ],
            [
                'title'   => '🗂 5. Gestión de Expedientes y Términos',
                'slug'    => 'uso-expedientes',
                'order'   => 5,
                'content' => '# El Núcleo del Trabajo Jurídico

### Alta de Expediente
1.  Botón **"Nuevo Expediente"**.
2.  Llene los básicos: Número, Juzgado, Juez, Materia.
3.  **Partes:** Agregue Actor, Demandado y Cliente. Esto es vital para que la IA entienda el contexto.

### Control de Términos (Semáforos)
Cada vez que agrega una actuación tipo "Acuerdo" con fecha de término:
*   🔴 **Rojo:** Vencido o vence HOY.
*   🟡 **Amarillo:** Vence en los próximos 3 días.
*   🟢 **Verde:** Vence en más de 3 días.
*   ⚪ **Gris:** Sin término o término cumplido.

> **Automatización:** El sistema le enviará una notificación (y correo, si está configurado) 24 horas antes de un vencimiento.
'
            ],
            [
                'title'   => '🤝 6. Asesorías y Clientes',
                'slug'    => 'uso-asesorias',
                'order'   => 6,
                'content' => '# Gestión de Prospectos

No mezcle prospectos con expedientes activos. Use el módulo de **Asesorías**.

### Flujo de Una Asesoría
1.  **Agendar:** Registre la cita (presencial/zoom), cliente y **Costo de la Asesoría**.
2.  **Sincronización:** Si tiene Google Calendar conectado, aparecerá allá automáticamente.
3.  **Ejecución:** Al terminar la cita, marque el estado como "Realizada".
4.  **Conversión:** Si el cliente lo contrata, use el botón **"Convertir a Expediente"** para transferir todos los datos sin reescribirlos.
'
            ],
            [
                'title'   => '💰 7. Facturación Interna y Recibos',
                'slug'    => 'uso-facturacion',
                'order'   => 7,
                'content' => '# Cobranza Profesional

Diogenes le ayuda a emitir recibos de honorarios internos (no fiscales, o fiscales si conecta el módulo SAT) para dar profesionalismo a su firma.

### Generar un Recibo
1.  Dentro de un Expediente o Asesoría, vaya a la pestaña **"Finanzas"**.
2.  Clic en **"Nuevo Cargo"**.
3.  Describa el concepto (ej. "Honorarios Iniciales") y monto.
4.  Clic en **"Generar PDF"**.
5.  El sistema crea un documento con su **Logo** (configurado en el paso 1) listo para enviar por WhatsApp al cliente.
'
            ],
            [
                'title'   => '🤖 8. Diogenes Intelligence (Tutorial)',
                'slug'    => 'uso-ia',
                'order'   => 8,
                'content' => '# Domine la IA Jurídica

Use el botón **"Asistente IA"** flotante dentro de cualquier expediente.

### Modos de Uso
1.  **🔍 Analista:** *"¿Cuándo fue el último acuerdo de este expediente?"* (Búsqueda de datos).
2.  **✍️ Redactor:** *"Redacta un escrito de autorización de abogados."* (Generación de documentos).
3.  **🧠 Estratega:** *"¿Qué riesgos ves en la contestación de la contraparte?"* (Análisis lógico).
4.  **📚 Investigador:** *"Explícame el concepto de Litispendencia."* (Conceptos teóricos).

> **Consejo Pro:** Háblele como a un pasante. Dele instrucciones claras: *"Actúa como abogado penalista y dime..."*
'
            ],
            [
                'title'   => '👤 9. Mi Perfil y Seguridad Personal',
                'slug'    => 'uso-perfil',
                'order'   => 9,
                'content' => '# Su Cuenta Personal

En la esquina superior derecha, haga clic en su nombre > **Perfil**.

### Lo que debe configurar HOY:
1.  **Foto de Perfil:** Ayuda a sus compañeros a identificar sus notas en la bitácora.
2.  **Cambio de Contraseña:** Hágalo periódicamente.
3.  **Autenticación de Dos Factores (2FA):** **ALTAMENTE RECOMENDADO.** Active esto para que, aunque roben su contraseña, no puedan entrar sin su celular. Proteja los datos de sus clientes.
4.  **Integraciones:** Conecte aquí su cuenta de Google para el Calendario.
'
            ],
            [
                'title'   => '🚀 10. Proyectos Inteligentes (Workflows)',
                'slug'    => 'uso-proyectos',
                'order'   => 10,
                'content' => '# Automatización de Procesos Legales

El módulo de **Proyectos** es el corazón de la eficiencia en Diogenes. Permite pasar de una idea o consulta inicial a un borrador legal en minutos.

### ¿Qué es un Proyecto?
A diferencia de un Expediente (que es un caso ya radicado), un **Proyecto** es la etapa de preparación. Es un asistente que le guiará paso a paso para recolectar la información necesaria según el tipo de juicio.

### Biblioteca de Procesos Incluida:
Diogenes incluye por defecto flujos especializados para:
*   **Familiar:** Pensión Alimenticia y Divorcio Incausado.
*   **Civil:** Contratos de Arrendamiento y Sucesiones Intestamentarias.
*   **Mercantil:** Juicio Ejecutivo (Cobro de Pagarés).
*   **Amparo:** Guía para Amparo Indirecto contra actos de autoridad.
*   **Laboral:** Demandas por Despido Injustificado.

### Flujo de Trabajo Sugerido:
1.  **Selección:** Elija un flujo de trabajo de la galería.
2.  **Wizard (Asistente):** Complete los pasos. Diogenes le pedirá datos específicos, hará cálculos y le mostrará un checklist de documentos necesarios (ej. Acta de Defunción en Sucesiones o el Pagaré en Mercantil).
3.  **Generación de Documentos:** En el último paso, haga clic en **"Generar Documento"**. El sistema usará sus respuestas para rellenar automáticamente la plantilla de su biblioteca.
4.  **Formalización:** ¿El cliente ya firmó? Use el botón **"Convertir a Expediente"** para transferir todo al control operativo del despacho sin escribir nada de nuevo.

### Configuración del Despacho
Para que el autollenado funcione, asegúrese de que sus formatos en la **Biblioteca** tengan etiquetas entre corchetes, por ejemplo: `[NOMBRE_DEL_ACTOR]`.
'
            ]
        ];

        foreach ($pages as $page) {
            ManualPage::create($page);
        }
    }
}
