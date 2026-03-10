<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LegalTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            [
                'name' => 'Demanda de Alimentos',
                'description' => 'Solicitud de pensión alimenticia para menores.',
                'category' => 'Familiar',
                'materia' => 'Familiar',
                'file_path' => 'templates/alimentos.docx',
                'extension' => 'docx',
                'is_global' => true,
                'extracted_text' => 'DEMANDA DE ALIMENTOS. [NOMBRE_ACTOR] vs [NOMBRE_DEMANDADO]... El menor [NOMBRE_MENOR] requiere...',
                'placeholders' => ['NOMBRE_ACTOR', 'NOMBRE_DEMANDADO', 'NOMBRE_MENOR', 'MONTO_SOLICITADO'],
            ],
            [
                'name' => 'Escrito Inicial de Divorcio',
                'description' => 'Formato de divorcio incausado.',
                'category' => 'Familiar',
                'materia' => 'Familiar',
                'file_path' => 'templates/divorcio.docx',
                'extension' => 'docx',
                'is_global' => true,
                'extracted_text' => 'SOLICITUD DE DIVORCIO. [CONYUGE_SOLICITANTE] y [CONYUGE_DEMANDADO]... Matrimonio celebrado en [FECHA_MATRIMONIO]...',
                'placeholders' => ['CONYUGE_SOLICITANTE', 'CONYUGE_DEMANDADO', 'FECHA_MATRIMONIO', 'REGIMEN_MATRIMONIAL'],
            ],
            [
                'name' => 'Contrato de Arrendamiento',
                'description' => 'Contrato de renta de inmueble.',
                'category' => 'Contratos',
                'materia' => 'Civil',
                'file_path' => 'templates/arrendamiento.docx',
                'extension' => 'docx',
                'is_global' => true,
                'extracted_text' => 'CONTRATO DE ARRENDAMIENTO. Arrendador: [ARRENDADOR], Arrendatario: [ARRENDATARIO]... El inmueble ubicado en [DIRECCION_INMUEBLE]...',
                'placeholders' => ['ARRENDADOR', 'ARRENDATARIO', 'DIRECCION_INMUEBLE', 'RENTA_MENSUAL'],
            ],
            [
                'name' => 'Demanda Ejecutiva Mercantil',
                'description' => 'Cobro judicial de pagaré.',
                'category' => 'Mercantil',
                'materia' => 'Mercantil',
                'file_path' => 'templates/mercantil.docx',
                'extension' => 'docx',
                'is_global' => true,
                'extracted_text' => 'JUICIO EJECUTIVO MERCANTIL. [NOMBRE_DEUDOR] adeuda la cantidad de [MONTO_PRINCIPAL]...',
                'placeholders' => ['NOMBRE_DEUDOR', 'MONTO_PRINCIPAL', 'TASA_INTERES'],
            ],
            [
                'name' => 'Demanda de Amparo Indirecto',
                'description' => 'Juicio contra actos de autoridad.',
                'category' => 'Amparo',
                'materia' => 'Amparo',
                'file_path' => 'templates/amparo.docx',
                'extension' => 'docx',
                'is_global' => true,
                'extracted_text' => 'DEMANDA DE AMPARO. Autoridad: [AUTORIDAD_ORDENADORA]... Acto: [ACTO_RECLAMADO]...',
                'placeholders' => ['AUTORIDAD_ORDENADORA', 'ACTO_RECLAMADO', 'ARTICULOS_VIOLADOS'],
            ],
            [
                'name' => 'Denuncia de Sucesión Intestamentaria',
                'description' => 'Inicio de juicio sucesorio sin testamento.',
                'category' => 'Civil',
                'materia' => 'Civil',
                'file_path' => 'templates/sucesion.docx',
                'extension' => 'docx',
                'is_global' => true,
                'extracted_text' => 'JUICIO SUCESORIO INTESTAMENTARIO. El finado [NOMBRE_FINADO] falleció en [FECHA_DEFUNCION]...',
                'placeholders' => ['NOMBRE_FINADO', 'FECHA_DEFUNCION', 'PRESUNTOS_HEREDEROS'],
            ],
            [
                'name' => 'Demanda de Despido Injustificado',
                'description' => 'Juicio laboral por despido.',
                'category' => 'Laboral',
                'materia' => 'Laboral',
                'file_path' => 'templates/laboral.docx',
                'extension' => 'docx',
                'is_global' => true,
                'extracted_text' => 'DEMANDA LABORAL. Patrón: [NOMBRE_PATRON], Trabajador ingresó en [FECHA_INGRESO]...',
                'placeholders' => ['NOMBRE_PATRON', 'FECHA_INGRESO', 'SALARIO_DIARIO', 'FECHA_DESPIDO'],
            ],
        ];

        foreach ($templates as $template) {
            \App\Models\LegalTemplate::updateOrCreate(
                ['name' => $template['name']],
                $template
            );
        }
    }
}
