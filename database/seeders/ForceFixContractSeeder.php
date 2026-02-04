<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ForceFixContractSeeder extends Seeder
{
    public function run(): void
    {
        $html = '
<div style="font-family: \'Times New Roman\', Times, serif; font-size: 12pt; color: #000; line-height: 1.5;">

    <h1 style="text-align: center; text-transform: uppercase; font-size: 16pt; margin-bottom: 30px;">
        CONTRATO DE PRESTACIÓN DE SERVICIOS PROFESIONALES
    </h1>

    <p style="text-align: justify; margin-bottom: 20px;">
        En la ciudad de <strong>{{CIUDAD_FIRMA}}</strong>, a los <strong>{{FECHA_ACTUAL}}</strong>, comparecen por una parte
        <strong>{{ABOGADO_RESPONSABLE}}</strong>, en adelante denominado como "EL PROFESIONAL", y por la otra parte
        <strong>{{CLIENTE_NOMBRE}}</strong>, con RFC <strong>{{CLIENTE_RFC}}</strong> y domicilio en
        <strong>{{CLIENTE_DIRECCION}}</strong>, en adelante denominado como "EL CLIENTE", quienes acuerdan celebrar el presente
        Contrato al tenor de las siguientes:
    </p>

    <h2 style="text-align: center; text-transform: uppercase; font-size: 14pt; margin-top: 30px; margin-bottom: 20px;">
        CLÁUSULAS
    </h2>

    <h3 style="font-size: 12pt; text-transform: uppercase; margin-bottom: 10px;">PRIMERA. DEL OBJETO</h3>
    <p style="text-align: justify; margin-bottom: 15px;">
        EL CLIENTE encomienda a EL PROFESIONAL la prestación de sus servicios de asesoría, gestión y representación legal para la atención del asunto identificado bajo los siguientes datos:
    </p>
    <ul style="list-style-type: none; padding-left: 20px; margin-bottom: 20px;">
        <li><strong>Expediente / Asunto:</strong> {{EXPEDIENTE_FOLIO}}</li>
        <li><strong>Título:</strong> {{EXPEDIENTE_TITULO}}</li>
        <li><strong>Materia:</strong> {{EXPEDIENTE_MATERIA}}</li>
        <li><strong>Juzgado / Instancia:</strong> {{EXPEDIENTE_JUZGADO}}</li>
    </ul>

    <h3 style="font-size: 12pt; text-transform: uppercase; margin-bottom: 10px;">SEGUNDA. ALCANCE DE LOS SERVICIOS</h3>
    <p style="text-align: justify; margin-bottom: 15px;">
        EL PROFESIONAL se obliga a prestar sus servicios con la diligencia y ética profesional requeridas, incluyendo la elaboración de demandas, contestaciones, ofrecimiento de pruebas, asistencia a audiencias y todas las promociones necesarias para la debida atención del asunto descrito, hasta la conclusión del mismo en la instancia contratada.
        No se garantiza un resultado específico favorable, sino la aplicación de los mejores conocimientos técnicos y legales para la defensa de los intereses de EL CLIENTE.
    </p>

    <h3 style="font-size: 12pt; text-transform: uppercase; margin-bottom: 10px;">TERCERA. HONORARIOS</h3>
    <p style="text-align: justify; margin-bottom: 15px;">
        Como contraprestación por los servicios profesionales, EL CLIENTE se compromete a pagar a EL PROFESIONAL la cantidad total de:
        <br><br>
        <strong>${{HONORARIOS_TOTALES}} MXN (más IVA en caso de requerir factura).</strong>
    </p>
    <p style="text-align: justify; margin-bottom: 15px;">
        Dichos honorarios podrán ser cubiertos mediante parcialidades según lo acuerden las partes.
        El incumplimiento de pago faculta a EL PROFESIONAL para suspender la prestación de los servicios hasta la regularización del saldo.
    </p>

    <h3 style="font-size: 12pt; text-transform: uppercase; margin-bottom: 10px;">CUARTA. GASTOS PROCESALES</h3>
    <p style="text-align: justify; margin-bottom: 15px;">
        Los honorarios pactados NO incluyen gastos procesales tales como copias certificadas, exhortos, peritajes, fianzas, edictos, ni viáticos fuera del lugar de residencia de EL PROFESIONAL, los cuales deberán ser cubiertos en su totalidad por EL CLIENTE de manera oportuna.
    </p>

    <h3 style="font-size: 12pt; text-transform: uppercase; margin-bottom: 10px;">QUINTA. VIGENCIA Y TERMINACIÓN</h3>
    <p style="text-align: justify; margin-bottom: 15px;">
        El presente contrato surte efectos a partir del <strong>{{FECHA_INICIO}}</strong> y tendrá vigencia hasta la conclusión del asunto o hasta que cualquiera de las partes notifique su deseo de darlo por terminado, debiendo liquidar los honorarios devengados hasta esa fecha.
    </p>

    <h3 style="font-size: 12pt; text-transform: uppercase; margin-bottom: 10px;">SEXTA. CONFIDENCIALIDAD</h3>
    <p style="text-align: justify; margin-bottom: 15px;">
        EL PROFESIONAL se obliga a guardar estricto secreto profesional respecto a la información proporcionada por EL CLIENTE, utilizándola única y exclusivamente para los fines de la defensa legal.
    </p>

    <br><br><br><br>

    <table style="width: 100%; margin-top: 50px; border: none;">
        <tr>
            <td style="width: 50%; text-align: center; border: none; vertical-align: top;">
                __________________________<br>
                <strong>{{ABOGADO_RESPONSABLE}}</strong><br>
                EL PROFESIONAL
            </td>
            <td style="width: 50%; text-align: center; border: none; vertical-align: top;">
                __________________________<br>
                <strong>{{CLIENTE_NOMBRE}}</strong><br>
                EL CLIENTE
            </td>
        </tr>
    </table>
</div>';

        // 1. Update ALL documents of type CONTRATO_SERVICIOS regardless of tenant
        // Using DB facade to bypass scopes and event listeners
        DB::table('legal_documents')
            ->where('tipo', 'CONTRATO_SERVICIOS')
            ->update(['texto' => $html]);

        $this->command->info('Updated all CONTRATO_SERVICIOS templates.');
    }
}
