<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UpdateManualSeeder extends Seeder
{
    public function run(): void
    {
        // Limpiamos contenido previo
        DB::table('manual_pages')->truncate();

        $pages = [
            [
                'title' => '1. Bienvenida y Conceptos Basicos',
                'order' => 1,
                'required_role' => 'user',
                'image_path' => null,
                'content' => "# Bienvenido a Diogenes: El Futuro de su Despacho\n\nDiogenes no es solo un software de gestión, es el copiloto inteligente diseñado para que usted se enfoque en la estrategia legal y no en el desorden administrativo.\n\n### Conceptos Elementales\n*   **El Expediente**: Es la unidad mínima de trabajo. Todo (documentos, comentarios, facturas, citas) orbita alrededor de un expediente.\n*   **Multitenancy**: Sus datos están blindados en un entorno exclusivo para su despacho. Nadie fuera de su organización puede ver su información.\n*   **Inteligencia Artificial (IA)**: Actúa como un asistente jurídico senior que lee sus archivos y responde dudas complejas basándose estrictamente en el contexto de su caso.",
            ],
            [
                'title' => '2. El Corazon del Sistema: Expedientes',
                'order' => 2,
                'required_role' => 'user',
                'image_path' => 'manual/expedientes.png',
                'content' => "# Guia Maestra de Expedientes\n\nUn expediente bien gestionado reduce el tiempo de consulta en un 70%. En Diogenes, el expediente es dinámico y centralizado.\n\n### Anatomia de un Expediente Exitoso\n1.  **Tablero Principal**: Vea el número de caso, juzgado, juez y abogado responsable de forma inmediata.\n2.  **Actuaciones (Timeline)**: Registre cada paso del proceso. \n    *   *Ejemplo*: \"15/Oct - Se presentó demanda de amparo.\"\n3.  **Gestión Documental**: Suba sus archivos en PDF o Imagen. \n    *   **Importante**: La IA analizará estos documentos automáticamente para responder sus dudas.\n\n### Buenas Practicas\n*   **Vincule un Cliente**: Esencial para la facturación automática y el historial de atención.\n*   **Actualice el Estado**: Cambie de \"Instrucción\" a \"Sentencia\" para que sus reportes de productividad sean precisos.\n*   **Nombres Descriptivos**: Nombre sus archivos adecuadamente (ej. `Sentencia_Primera_Instancia.pdf`) para facilitar las búsquedas de la IA.\n\n### Lo que NO debe hacer\n*   **Evite expedientes genéricos**: No mezcle clientes diferentes en un mismo registro.\n*   **Evite fotos de baja calidad**: Si el texto es ilegible para usted, la IA tampoco podrá procesarlo correctamente.",
            ],
            [
                'title' => '3. Diogenes Intelligence: IA Juridica',
                'order' => 3,
                'required_role' => 'user',
                'image_path' => null,
                'content' => "# Maximice su Potencial con la IA\n\nDiogenes Intelligence es un **Modelo de Lenguaje Jurídico** integrado en sus datos. No es un chat genérico; es su asistente con acceso a cada palabra de sus expedientes.\n\n### Como piensa Diogenes?\nAl preguntarle algo dentro de un expediente, el sistema extrae automáticamente la información relevante del caso y se la entrega a la IA como contexto. Esto garantiza que las respuestas sean específicas y no generalidades.\n\n### Los 4 Modos de Operación\n1.  **Analista**: Ideal para auditoría. Evalúa fechas, busca preclusiones y genera tablas cronológicas.\n2.  **Redactor**: Su pluma jurídica. Redacta promociones, demandas o contestaciones con estilo formal.\n3.  **Estratega**: Para simulación de escenarios. ¿Qué argumentos usará la contraparte? Diogenes le ayuda a anticiparse.\n4.  **Investigador**: Su bibliotecario. Explica conceptos doctrinales o diferencias jurisprudenciales.\n\n### El Arte del Prompting (Instrucciones)\n*   ❌ **Mal**: \"Hazme una demanda.\"\n*   ✅ **Excelente**: \"Actúa como abogado penalista. Redacta un recurso de apelación contra el auto de vinculación a proceso, enfocándote en la falta de motivación expuesta en el documento que subí ayer.\"\n\n> **Nota Legal**: Diogenes es una herramienta de apoyo. Usted es el responsable final de revisar y firmar cada documento generado.",
            ],
            [
                'title' => '4. Agenda, Citas y Google Calendar',
                'order' => 4,
                'required_role' => 'user',
                'image_path' => 'manual/google-calendar.png',
                'content' => "# Una Agenda que Trabaja por Usted\n\nMantenga su despacho sincronizado en tiempo real con sus dispositivos personales.\n\n### Sincronizacion con Google Calendar\n1. Vaya a su **Perfil**.\n2. Haga clic en **\"Conectar Google Calendar\"**.\n3. Autorice los permisos. \n\n**Listo!** Ahora, cada vez que registre una audiencia o cita en Diogenes, aparecerá automáticamente en su celular con notificaciones recordatorias.\n\n### Gestion de Terminos (Plazos)\nAl registrar una actuación, marque la casilla **\"Es plazo\"**. \n*   El sistema resaltará el expediente en rojo si el vencimiento es próximo.\n*   Aparecerá una alerta prioritaria en su Dashboard principal.",
            ],
            [
                'title' => '5. Gestion de Asesorias y Ficha de Cita',
                'order' => 5,
                'required_role' => 'user',
                'image_path' => 'manual/asesorias.png',
                'content' => "# Especializado en la Primera Atencion\n\nEl módulo de **Asesorías** le permite gestionar el primer contacto con el cliente antes de que se convierta en un expediente formal.\n\n### El QR de la Ficha de Asesoria\nCada asesoría agendada genera una **Ficha de Cita** única que puede compartir con su cliente.\n*   **Contenidos de la Ficha**: Nombre del cliente, asunto, fecha, hora y modalidad (presencial o virtual).\n*   **El Codigo QR Dinamico**: La ficha incluye un código QR que el cliente puede escanear para:\n    *   Abrir la ubicación en Google Maps (si la cita es presencial).\n    *   Unirse directamente a la videollamada (si la cita es por Zoom/Meet).\n    *   Llamar al despacho con un toque (si la cita es telefónica).\n\n### Flujo de Trabajo\n1.  **Registro**: Captura de datos básicos del prospecto y asunto.\n2.  **Disponibilidad**: El sistema le sugiere el siguiente horario libre si hay un choque de agenda.\n3.  **Seguimiento**: Marque como \"Realizada\" para registrar los acuerdos alcanzados y, opcionalmente, convertirlo en un cliente o expediente real.\n\n### Cobro de Asesorias\nSi su despacho lo tiene habilitado, podrá registrar el pago en el momento y generar un **Recibo PDF** profesional para entregar al cliente de inmediato.",
            ],
            [
                'title' => '6. Finanzas, Cobranza y Recibos',
                'order' => 6,
                'required_role' => 'admin',
                'image_path' => 'manual/facturacion.png',
                'content' => "# Control de Ingresos sin Complicaciones\n\nEvite el uso de hojas de cálculo externas. Todo el flujo de caja de sus expedientes está aquí.\n\n### Ciclo Financiero del Caso\n*   **Presupuesto**: Defina el monto total de honorarios pactado.\n*   **Registrar Abono**: Cada vez que el cliente pague, regístrelo en la pestaña de Finanzas del expediente.\n*   **Saldo Pendiente**: El sistema calcula automáticamente cuánto resta por cobrar.\n*   **Generación de Recibos**: Con un clic, obtenga un PDF con su logo y los detalles del pago realizado.\n\n### Facturacion\nAsocie facturas oficiales a los abonos para mantener un control fiscal estricto y una transparencia total con sus clientes.",
            ],
            [
                'title' => '7. Reportes e Inteligencia de Negocio',
                'order' => 7,
                'required_role' => 'admin',
                'image_path' => null,
                'content' => "# Tome Decisiones Basadas en Datos\n\nEl módulo de reportes le permite ver la \"fotografía\" actual de la salud de su firma.\n\n### Reportes Disponibles\n*   **Ingresos Mensuales**: Compare sus cobros mes a mes.\n*   **Carga de Trabajo**: Vea cuántos expedientes activos tiene cada abogado.\n*   **Eficiencia de Asesorías**: ¿Cuántas asesorías terminan convirtiéndose en expedientes reales?\n*   **Balance de Cartera**: Total de saldos por cobrar para proyectar sus ingresos futuros.",
            ],
            [
                'title' => '8. Roles, Permisos y Colaboracion',
                'order' => 8,
                'required_role' => 'admin',
                'image_path' => null,
                'content' => "# Seguridad y Niveles de Acceso\n\nUsted decide quién ve qué. Diogenes utiliza un sistema de roles para proteger la información sensible.\n\n### Tabla de Roles\n| Rol | Capacidades |\n| :--- | :--- |\n| **Administrador** | Acceso total: finanzas, configuración, borrado de datos y gestión de usuarios. |\n| **Abogado** | Crea y gestiona expedientes, registra actuaciones y usa la IA. No ve finanzas globales. |\n| **Asistente** | Apoyo documental, subida de archivos y agenda. Acceso limitado a áreas críticas. |\n\n### Colaboracion en Tiempo Real\nAsigne varios abogados a un mismo expediente para que compartan la bitácora de documentos y comentarios, manteniendo a todo el equipo alineado.",
            ],
            [
                'title' => '9. Seguridad y Auditoria (Bitacora)',
                'order' => 9,
                'required_role' => 'admin',
                'image_path' => 'manual/bitacora.png',
                'content' => "# Transparencia Absoluta\n\nCada acción importante en el sistema queda registrada en la **Bitácora de Auditoría**.\n\n### Que rastreamos?\n*   Eliminación de documentos o expedientes.\n*   Cambios en presupuestos o estados de cobranza.\n*   Inicios de sesión y direcciones IP de acceso.\n\nUsted podrá filtrar por fecha o usuario para saber exactamente qué sucedió y cuándo, brindando una capa extra de seguridad para el despacho.",
            ],
            [
                'title' => '10. Personalizacion y Configuracion',
                'order' => 10,
                'required_role' => 'admin',
                'image_path' => 'manual/configuracion.png',
                'content' => "# Su Despacho, Su Identidad\n\nPersonalice el sistema para que se sienta como una extensión de su marca.\n\n### Branding\n*   **Logotipo**: Suba su logo para que aparezca en los PDFs de recibos y reportes.\n*   **Datos de Contacto**: Dirección y teléfonos que se usarán en los encabezados oficiales.\n\n### Configuracion de Agenda\n*   **Días Laborables**: Seleccione qué días atiende su despacho.\n*   **Granularidad**: Defina bloques de (15, 30 o 60 min) para sus citas para un control de agenda más ordenado.",
            ],
            [
                'title' => '11. Panel de Super Administrador',
                'order' => 11,
                'required_role' => 'super_admin',
                'image_path' => null,
                'content' => "# Gestion de la Plataforma (SaaS)\n\nEspacio exclusivo para la administración del ecosistema Diogenes.\n\n*   **Gestión de Tenants**: Monitoreo de cada despacho registrado.\n*   **Planes y Suscripciones**: Control de límites (usuarios, almacenamiento, tokens de IA).\n*   **Modelos de IA**: Configuración de proveedores (OpenAI/Anthropic) y monitoreo de costos por API.",
            ],
        ];

        foreach ($pages as $page) {
            $slug = Str::slug($page['title']);
            DB::table('manual_pages')->updateOrInsert(
                ['slug' => $slug],
                array_merge($page, [
                    'updated_at' => now(),
                    'created_at' => now(),
                ])
            );
        }
    }
}
