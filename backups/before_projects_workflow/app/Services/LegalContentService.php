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
    <p>Para la interpretación de este contrato, las partes se someten a la jurisdicción de los tribunales competentes de la ciudad de <strong>Xalapa, Veracruz, México</strong>, renunciando a cualquier otro fuero que pudiera corresponderles por razón de sus domicilios presentes o futuros.</p>';
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
        return '<h2 align="center"><strong>CONTRATO DE PRESTACIÓN DE SERVICIOS PROFESIONALES</strong></h2>

<p><strong>CONTRATO DE PRESTACIÓN DE SERVICIOS PROFESIONALES</strong> QUE CELEBRAN, POR UNA PARTE, <strong>{{ DESPACHO_NOMBRE }}</strong>, REPRESENTADO EN ESTE ACTO POR EL LICENCIADO <strong>{{ DESPACHO_TITULAR }}</strong>, A QUIEN EN LO SUCESIVO SE LE DENOMINARÁ "EL PROFESIONISTA", Y POR LA OTRA PARTE, <strong>{{ CLIENTE_NOMBRE }}</strong>, A QUIEN EN LO SUCESIVO SE LE DENOMINARÁ "EL CLIENTE", AL TENOR DE LAS SIGUIENTES DECLARACIONES Y CLÁUSULAS:</p>

<h3><strong>D E C L A R A C I O N E S</strong></h3>

<p><strong>I. DECLARA "EL PROFESIONISTA":</strong><br>
a) Ser una entidad dedicada a la prestación de servicios jurídicos, legalmente constituida y representada, contando con la capacidad e infraestructura necesarias para el objeto de este contrato.<br>
b) Que su Registro Federal de Contribuyentes es <strong>{{ DESPACHO_RFC }}</strong>.<br>
c) Que para efectos del presente contrato señala como domicilio el ubicado en: <strong>{{ DESPACHO_DIRECCION }}</strong>, y como medio de contacto oficial el correo electrónico: <strong>{{ DESPACHO_EMAIL }}</strong>.</p>

<p><strong>II. DECLARA "EL CLIENTE":</strong><br>
a) Ser una persona con plena capacidad legal para obligarse en los términos del presente instrumento.<br>
b) Que es su voluntad contratar los servicios de "EL PROFESIONISTA" para la atención del asunto legal descrito más adelante.<br>
c) Que su Registro Federal de Contribuyentes es <strong>{{ CLIENTE_RFC }}</strong>.<br>
d) Que señala como domicilio para recibir notificaciones: <strong>{{ CLIENTE_DIRECCION }}</strong>, y correo electrónico: <strong>{{ CLIENTE_EMAIL }}</strong>.</p>

<p>Expuesto lo anterior, las partes otorgan las siguientes:</p>

<h3><strong>C L Á U S U L A S</strong></h3>

<p><strong>PRIMERA. OBJETO.</strong><br>
"EL PROFESIONISTA" se obliga a prestar sus servicios profesionales de asesoría y representación legal en relación con el asunto:</p>

<ul>
<li><strong>Asunto:</strong> {{ EXPEDIENTE_TITULO }}</li>
<li><strong>Expediente:</strong> {{ EXPEDIENTE_FOLIO }}</li>
<li><strong>Materia:</strong> {{ EXPEDIENTE_MATERIA }}</li>
<li><strong>Autoridad:</strong> {{ EXPEDIENTE_JUZGADO }}</li>
</ul>

<p>Los servicios comprenden la atención diligente y profesional en todas las etapas procesales del juicio <strong>{{ EXPEDIENTE_JUICIO }}</strong>, hasta su conclusión en primera instancia.</p>

<p><strong>SEGUNDA. HONORARIOS.</strong><br>
"EL CLIENTE" pagará a "EL PROFESIONISTA" la cantidad de <strong>${{ HONORARIOS_TOTALES }} (M.N.)</strong> por concepto de honorarios profesionales. Esta suma no incluye gastos, costas, peritajes, ni viáticos.</p>

<p><strong>TERCERA. CONFIDENCIALIDAD.</strong><br>
Ambas partes acuerdan mantener estricta confidencialidad sobre la información intercambiada. "EL PROFESIONISTA" protegerá los datos personales y sensibles de "EL CLIENTE" conforme a la legislación aplicable.</p>

<p><strong>CUARTA. VIGENCIA Y TERMINACIÓN.</strong><br>
Este contrato inicia el <strong>{{ FECHA_INICIO }}</strong> y concluirá al finalizar el asunto encargado o por acuerdo escrito entre las partes.</p>

<p><strong>QUINTA. JURISDICCIÓN.</strong><br>
Para la interpretación y cumplimiento de este contrato, las partes se someten a los tribunales competentes de <strong>{{ CIUDAD_FIRMA }}</strong>, renunciando a cualquier otro fuero.</p>

<p>Leído que fue el presente contrato, lo firman en <strong>{{ CIUDAD_FIRMA }}</strong>, a los <strong>{{ FECHA_ACTUAL }}</strong>.</p>

<p><br><br><br></p>

<table style="width: 100%; border-collapse: collapse; border: none;">
<tbody>
<tr>
<td style="width: 50%; text-align: center; border: none;">
__________________________<br>
<strong>"EL PROFESIONISTA"</strong><br>
LIC. {{ DESPACHO_TITULAR }}<br>
{{ DESPACHO_NOMBRE }}
</td>
<td style="width: 50%; text-align: center; border: none;">
__________________________<br>
<strong>"EL CLIENTE"</strong><br>
{{ CLIENTE_NOMBRE }}
</td>
</tr>
</tbody>
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
