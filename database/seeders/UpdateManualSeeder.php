<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UpdateManualSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            [
                'title' => '📊 Tablero Super Admin (Monitoreo SaaS)',
                'order' => 10,
                'required_role' => 'super_admin',
                'image_path' => null,
                'content' => "# Tablero de Control Super Admin\n\nEl nuevo tablero proporciona una vista en tiempo real de la salud de la plataforma SaaS:\n\n*   **Métricas de Crecimiento**: Gráficas de registros de nuevos tenants (despachos) vs cancelaciones.\n*   **Monitoreo de IA**: Seguimiento visual del presupuesto mensual de IA y el consumo acumulado.\n*   **Consumo por Cliente**: Gráfica de dona que desglosa qué despachos están utilizando más recursos de IA.\n*   **Estado de Infraestructura**: Indicadores visuales (Gauge) sobre el vencimiento del dominio y certificados SSL.\n*   **Tabla de Actividad Reciente**: Listado rápido de los últimos despachos registrados con su estado de suscripción.",
            ],
            [
                'title' => '⚙️ Configuración Global del Sistema',
                'order' => 11,
                'required_role' => 'super_admin',
                'image_path' => null,
                'content' => "# Configuración Técnica Centralizada\n\nDesde el módulo de Configuración Global, el Super Admin puede gestionar los pilares del sistema:\n\n1.  **Correo SMTP**: Configuración del servidor para el envío de anuncios masivos y notificaciones del sistema.\n2.  **Pasarela Stripe**: Integración directa con Stripe para el cobro automático de suscripciones.\n3.  **Proveedores de IA**: Selección dinámica entre OpenAI, Groq, DeepSeek y Anthropic según conveniencia de costo y velocidad.\n4.  **Límites de Archivos**: Control del tamaño máximo permitido para cargas de documentos en todo el sistema.\n5.  **Onboarding**: Personalización del video y mensaje de bienvenida que ven los nuevos usuarios al entrar por primera vez.",
            ],
            [
                'title' => '🤖 Asistente de IA Legal Integral',
                'order' => 12,
                'required_role' => 'user',
                'image_path' => null,
                'content' => "# Potenciando el Trabajo con Inteligencia Artificial\n\nDiogenes ahora cuenta con un asistente de IA especializado por expediente:\n\n*   **Análisis Multimodal**: La IA es capaz de leer y entender documentos PDF, imágenes de escaneos y texto descriptivo.\n*   **Consultas Específicas**: Puedes chatear con el asistente para preguntarle sobre fechas clave, nombres de partes involucradas o resúmenes de actuaciones largas.\n*   **Contexto de Expediente**: El asistente conoce todo lo cargado en el expediente (actuaciones, documentos, notas) para dar respuestas precisas.\n*   **Asistente Global**: Disponible en la barra lateral para consultas rápidas de jurisprudencia o redacción de textos legales.",
            ],
            [
                'title' => '💰 Gestión Financiera y Recibos',
                'order' => 13,
                'required_role' => 'admin',
                'image_path' => null,
                'content' => "# Control Total de Honorarios y Pagos\n\nHemos integrado un módulo financiero dentro de cada expediente:\n\n*   **Presupuesto de Honorarios**: Define el monto total acordado con el cliente al crear el expediente.\n*   **Registro de Abonos**: Registra pagos parciales o totales de forma sencilla.\n*   **Saldos Automáticos**: El sistema calcula en tiempo real cuánto ha pagado el cliente y cuánto queda pendiente.\n*   **Recibos PDF Profesionales**: Al registrar un pago, el sistema genera automáticamente un recibo profesional descargable con los datos del despacho.",
            ],
            [
                'title' => '📅 Citas Inteligentes con QR y Enlaces',
                'order' => 14,
                'required_role' => 'user',
                'image_path' => null,
                'content' => "# Automatización de Consultas y Asesorías\n\nGestiona tus citas de forma moderna y eficiente:\n\n*   **Enlaces Públicos**: Cada abogado puede compartir un link para que prospectos agenden citas según su disponibilidad.\n*   **Códigos QR**: Genera QRs para imprimir y colocar en tu oficina física; los clientes podrán escanear y agendar al momento.\n*   **Sincronización con Google Calendar**: Las citas agendadas se reflejan automáticamente en tu calendario personal.\n*   **Conversión a Cliente**: Con un solo clic, convierte a un prospecto de asesoría en un cliente formal del despacho.",
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
