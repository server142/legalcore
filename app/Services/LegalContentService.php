<?php

namespace App\Services;

use App\Models\LegalDocument;

class LegalContentService
{
    public static function createGlobalDefaults()
    {
        $docs = [
            [
                'nombre' => 'Aviso de Privacidad Integral',
                'tipo' => 'PRIVACIDAD',
                'version' => '1.0',
                'activo' => true,
                'requiere_aceptacion' => true,
                'visible_en' => ['registro', 'footer', 'login'],
                'fecha_publicacion' => now(),
                'texto' => self::getGlobalPrivacyPolicy(),
            ],
            [
                'nombre' => 'Términos y Condiciones de Uso',
                'tipo' => 'TERMINOS',
                'version' => '1.0',
                'activo' => true,
                'requiere_aceptacion' => true,
                'visible_en' => ['registro', 'footer'],
                'fecha_publicacion' => now(),
                'texto' => self::getGlobalTerms(),
            ],
            [
                'nombre' => 'Acuerdo de Suscripción y Pagos Recurrentes',
                'tipo' => 'CONTRATO_SAAS',
                'version' => '1.0',
                'activo' => true,
                'requiere_aceptacion' => true,
                'visible_en' => ['registro', 'billing'],
                'fecha_publicacion' => now(),
                'texto' => self::getGlobalSubscriptionTerms(),
            ]
        ];

        foreach ($docs as $doc) {
            LegalDocument::updateOrCreate(
                ['tipo' => $doc['tipo'], 'tenant_id' => null, 'version' => $doc['version']],
                $doc
            );
        }
    }

    public static function createTenantDefaults($tenantId)
    {
        $docs = [
            [
                'tenant_id' => $tenantId,
                'nombre' => 'Convenio de Confidencialidad y No Divulgación',
                'tipo' => 'OTRO', // Changed to OTRO to keep CONTRATO_SAAS for the global subscription
                'version' => '1.0',
                'activo' => true,
                'requiere_aceptacion' => true,
                'visible_en' => ['onboarding', 'footer'],
                'fecha_publicacion' => now(),
                'texto' => self::getTenantNDA(),
            ],
            [
                'tenant_id' => $tenantId,
                'nombre' => 'Código de Ética y Conducta Profesional',
                'tipo' => 'OTRO',
                'version' => '1.0',
                'activo' => true,
                'requiere_aceptacion' => true,
                'visible_en' => ['onboarding', 'footer'],
                'fecha_publicacion' => now(),
                'texto' => self::getTenantEthicsCode(),
            ],
            [
                'tenant_id' => $tenantId,
                'nombre' => 'Plantilla Base: Contrato de Servicios',
                'tipo' => 'CONTRATO_SERVICIOS',
                'version' => '1.0',
                'activo' => true,
                'requiere_aceptacion' => false, // No lo acepta el usuario del sistema, sino el cliente final
                'visible_en' => [], // No se muestra en flujos automáticos
                'fecha_publicacion' => now(),
                'texto' => self::getTenantServiceContractTemplate(),
            ]
        ];

        foreach ($docs as $doc) {
            LegalDocument::updateOrCreate(
                [
                    'tenant_id' => $doc['tenant_id'],
                    'nombre' => $doc['nombre'],
                    'version' => $doc['version']
                ],
                $doc
            );
        }
    }

    private static function getGlobalPrivacyPolicy()
    {
        return '<h1>Aviso de Privacidad Integral</h1>
<p><strong>Responsable:</strong> Diogenes, con fundamento en la Ley Federal de Protección de Datos Personales en Posesión de los Particulares (LFPDPPP).</p>

<h2>1. Fundamento Legal</h2>
<p>Este aviso se emite en cumplimiento a los artículos 8, 15, 16, 33 y 36 de la LFPDPPP, su Reglamento y los Lineamientos del Aviso de Privacidad publicados por el INAI.</p>

<h2>2. Finalidades Primarias</h2>
<ul>
    <li>Prestación del servicio de gestión jurídica en la nube.</li>
    <li>Creación y administración de cuentas de usuario.</li>
    <li>Garantizar la seguridad de los expedientes almacenados.</li>
    <li>Emisión de comprobantes fiscales y procesamiento de pagos.</li>
</ul>

<h2>3. Datos Personales Recabados</h2>
<p>Recabamos nombre, correo electrónico, datos de contacto profesional y datos de facturación. En cumplimiento al artículo 9 de la LFPDPPP, informamos que no recabamos datos personales sensibles de los usuarios administradores.</p>

<h2>4. Derechos ARCO</h2>
<p>Usted tiene derecho al Acceso, Rectificación, Cancelación u Oposición de sus datos. Para ejercer estos derechos, debe enviar una solicitud al correo: privacidad@diogenes.com.mx.</p>

<h2>5. Transferencia de Datos</h2>
<p>Sus datos no serán compartidos con terceros ajenos a la prestación del servicio, salvo por mandato de autoridad judicial competente conforme al artículo 37 de la LFPDPPP.</p>';
    }

    private static function getGlobalTerms()
    {
        return '<h1>Términos y Condiciones de Servicio</h1>
<p><strong>Fundamento:</strong> Los presentes términos se rigen por el Código de Comercio de México, el Código Civil Federal y la Ley Federal de Protección al Consumidor.</p>

<h2>1. Aceptación del Servicio</h2>
<p>Al registrarse en Diogenes, usted acepta un contrato de prestación de servicios de software (SaaS) conforme al Código Civil Federal en materia de consentimiento tácito.</p>

<h2>2. Propiedad Intelectual</h2>
<p>Todo el software, código y diseño son propiedad exclusiva de Diogenes. Los datos y expedientes cargados son propiedad exclusiva del usuario/despacho, conforme a la Ley Federal del Derecho de Autor.</p>

<h2>3. Disponibilidad y Responsabilidad</h2>
<p>Diogenes garantiza un uptime del 99.9%. No obstante, conforme a las disposiciones mercantiles, el sistema se provee "tal cual es" (as is), limitando la responsabilidad a la devolución de la última mensualidad pagada en caso de fallas graves comprobables.</p>

<h2>4. Jurisdicción</h2>
<p>Para la interpretación de este contrato, las partes se someten a la jurisdicción de los tribunales competentes de la Ciudad de México, renunciando a cualquier otro fuero.</p>';
    }

    private static function getGlobalSubscriptionTerms()
    {
        return '<h1>Acuerdo de Suscripción y Pagos Recurrentes</h1>
<p><strong>Fundamento:</strong> Ley Federal de Protección al Consumidor (Art. 86 bis) sobre telecomunicaciones y servicios recurrentes.</p>

<h2>1. Autorización de Cobro Automático</h2>
<p>El Usuario autoriza expresamente a Diogenes a realizar cargos automáticos recurrentes (mensuales o anuales según el plan elegido) a la tarjeta de crédito o débito registrada, hasta que el servicio sea cancelado explícitamente.</p>

<h2>2. Política de Cancelación</h2>
<p>El usuario puede cancelar su suscripción en cualquier momento desde su panel de administración. La cancelación detendrá los cobros futuros, pero <strong>no se realizarán reembolsos</strong> por los días no utilizados del periodo en curso.</p>

<h2>3. Modificación de Tarifas</h2>
<p>Diogenes se reserva el derecho de modificar las tarifas del servicio, notificando al usuario con al menos 30 días de anticipación. El uso continuado del servicio después de dicho periodo constituye la aceptación tácita de las nuevas tarifas.</p>

<h2>4. Suspensión por Falta de Pago</h2>
<p>En caso de que el cobro automático sea rechazado, el sistema intentará el cobro nuevamente durante 3 días. Si el pago no se concreta, el acceso a la cuenta será restringido temporalmente hasta regularizar el saldo.</p>';
    }

    private static function getTenantNDA()
    {
        return '<h1>Convenio de Confidencialidad y Secreto Profesional</h1>
<p><strong>Fundamento:</strong> Ley Federal del Trabajo (Art. 134 fracción XIII) y Ley Federal de Protección a la Propiedad Industrial.</p>

<h2>1. Objeto</h2>
<p>El presente documento regula la obligación del colaborador del Despacho de mantener absoluta confidencialidad sobre la información de los clientes, estrategias jurídicas y secretos industriales a los que tenga acceso a través de Diogenes.</p>

<h2>2. Secreto Profesional</h2>
<p>El abogado/colaborador reconoce que el incumplimiento de este convenio puede derivar en responsabilidad civil, administrativa y penal conforme al secreto profesional regulado en los códigos penales estatales y la Ley de Profesiones.</p>

<h2>3. Propiedad de la Información</h2>
<p>Toda la información contenida en los expedientes digitales es propiedad exclusiva del Despacho y sus clientes. Queda prohibida la extracción o copia de datos para fines ajenos al despacho.</p>';
    }

    private static function getTenantServiceContractTemplate()
    {
        return '<h1>Contrato de Prestación de Servicios Profesionales</h1>
<p><strong>Conste por el presente documento, el Contrato de Prestación de Servicios Profesionales que celebran, por una parte, {{ABOGADO_RESPONSABLE}} (en adelante "EL PRESTADOR") y por la otra {{CLIENTE_NOMBRE}} (en adelante "EL CLIENTE"), al tenor de las siguientes cláusulas:</strong></p>

<h2>1. Objeto del Contrato</h2>
<p>EL PRESTADOR se obliga a brindar asesoría y representación legal a EL CLIENTE en relación con el asunto identificado como <strong>{{EXPEDIENTE_TITULO}}</strong>, con número de expediente <strong>{{EXPEDIENTE_FOLIO}}</strong>, radicado en {{EXPEDIENTE_JUZGADO}}.</p>

<h2>2. Alcance de los Servicios</h2>
<p>Los servicios incluyen la elaboración de demandas, contestaciones, ofrecimiento de pruebas, asistencia a audiencias y todas las gestiones necesarias hasta la conclusión del asunto en primera instancia.</p>

<h2>3. Honorarios Profesionales</h2>
<p>Las partes acuerdan que los honorarios por los servicios descritos ascenderán a la cantidad total de <strong>${{HONORARIOS_TOTALES}} MXN</strong>.</p>
<p>Dichos honorarios no incluyen gastos de juicio, peritajes, fianzas, ni copias certificadas, los cuales serán cubiertos directamente por EL CLIENTE.</p>

<h2>4. Vigencia</h2>
<p>Este contrato entra en vigor a partir del día <strong>{{FECHA_INICIO}}</strong> y concluirá al emitirse la sentencia definitiva o convenio que ponga fin al asunto.</p>

<h2>5. Confidencialidad</h2>
<p>Ambas partes se obligan a guardar estricta confidencialidad sobre la información compartida durante la vigencia de este contrato.</p>

<p><br><br></p>
<p style="text-align: center;">Firmado en {{CIUDAD_FIRMA}} a los {{FECHA_ACTUAL}}.</p>

<table style="width: 100%; margin-top: 50px;">
<tr>
    <td style="width: 50%; text-align: center;">
        __________________________<br>
        <strong>{{ABOGADO_RESPONSABLE}}</strong><br>
        EL PRESTADOR
    </td>
    <td style="width: 50%; text-align: center;">
        __________________________<br>
        <strong>{{CLIENTE_NOMBRE}}</strong><br>
        EL CLIENTE
    </td>
</tr>
</table>';
    }

    private static function getTenantEthicsCode()
    {
        return '<h1>Código de Ética y Operación del Despacho</h1>
<p><strong>Fundamento:</strong> Art. 5º de la Constitución Política de los Estados Unidos Mexicanos y Leyes de Profesiones Estatales.</p>

<h2>1. Principios de Actuación</h2>
<p>Todo integrante de este despacho que utilice el sistema Diogenes se compromete a actuar bajo principios de probidad, lealtad y veracidad en el manejo de expedientes.</p>

<h2>2. Uso del Sistema</h2>
<p>El acceso es personal e intransferible. El usuario es responsable de todas las acciones realizadas bajo su credencial, mismas que quedan registradas en la bitácora de seguridad.</p>

<h2>3. Conflictos de Interés</h2>
<p>Es obligación del colaborador reportar inmediatamente si detecta que un expediente asignado en el sistema genera un conflicto de interés personal o profesional.</p>';
    }
}
