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
                'name' => 'Demanda de Divorcio Incausado',
                'description' => 'Formato estándar para solicitud de divorcio sin expresión de causa conforme al Código Civil.',
                'category' => 'Familiar',
                'materia' => 'Civil/Familiar',
                'file_path' => 'templates/divorcio_incausado.docx',
                'extension' => 'docx',
                'is_global' => true,
                'extracted_text' => 'SOLICITUD DE DIVORCIO INCAUSADO. C. JUEZ DE LO FAMILIAR EN TURNO... El que suscribe [NOMBRE_ACTOR], por mi propio derecho...',
                'placeholders' => ['NOMBRE_ACTOR', 'NOMBRE_DEMANDADO', 'DOMICILIO', 'JUZGADO'],
            ],
            [
                'name' => 'Contrato de Arrendamiento Residencial',
                'description' => 'Contrato completo para renta de casa habitación con cláusulas de depósito y mantenimiento.',
                'category' => 'Contratos',
                'materia' => 'Civil',
                'file_path' => 'templates/arrendamiento.docx',
                'extension' => 'docx',
                'is_global' => true,
                'extracted_text' => 'CONTRATO DE ARRENDAMIENTO. En la ciudad de [CIUDAD] a [FECHA]... Ambas partes convienen en [CLAUSULA_RENTA]...',
                'placeholders' => ['CIUDAD', 'FECHA', 'ARRENDADOR', 'ARRENDATARIO', 'RENTA_MENSUAL'],
            ],
            [
                'name' => 'Acuerdo de Confidencialidad (NDA)',
                'description' => 'Protección de información sensible para relaciones comerciales o laborales.',
                'category' => 'Corporativo',
                'materia' => 'Mercantil',
                'file_path' => 'templates/nda.docx',
                'extension' => 'docx',
                'is_global' => true,
                'extracted_text' => 'CONVENIO DE CONFIDENCIALIDAD. Entre [EMPRESA_A] y [EMPRESA_B]... La información confidencial incluye [DETALLE_INFORMACION]...',
                'placeholders' => ['EMPRESA_A', 'EMPRESA_B', 'REPRESENTANTE', 'VIGENCIA'],
            ],
            [
                'name' => 'Contrato de Compraventa Vehicular',
                'description' => 'Formato para la enajenación de vehículos usados entre particulares.',
                'category' => 'Contratos',
                'materia' => 'Civil/Mercantil',
                'file_path' => 'templates/compraventa_auto.docx',
                'extension' => 'docx',
                'is_global' => true,
                'extracted_text' => 'CONTRATO DE COMPRAVENTA. Por una parte [VENDEDOR] y por otra [COMPRADOR]... El vehículo marca [MARCA] modelo [MODELO]...',
                'placeholders' => ['VENDEDOR', 'COMPRADOR', 'MARCA', 'MODELO', 'PRECIO', 'NUMERO_SERIE'],
            ],
        ];

        foreach ($templates as $template) {
            \App\Models\LegalTemplate::create($template);
        }
    }
}
