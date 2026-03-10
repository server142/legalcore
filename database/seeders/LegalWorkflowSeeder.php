<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LegalWorkflowSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\LegalWorkflow::updateOrCreate(
            ['name' => 'Juicio de Pensión Alimenticia'],
            [
                'description' => 'Guía paso a paso para iniciar una demanda de alimentos, incluyendo recolección de datos, cálculo de gastos y generación de formato.',
                'materia' => 'Familiar',
                'icon' => 'user-group',
                'steps' => [
                    [
                        'id' => 1,
                        'title' => 'Datos de las Partes',
                        'description' => 'Ingresa la información básica de los involucrados.',
                        'fields' => [
                            ['name' => 'nombre_menor', 'label' => 'Nombre completo del menor', 'type' => 'text', 'required' => true],
                            ['name' => 'nombre_actor', 'label' => 'Nombre del padre/madre solicitante', 'type' => 'text', 'required' => true],
                            ['name' => 'nombre_demandado', 'label' => 'Nombre del deudor alimentario', 'type' => 'text', 'required' => true],
                        ]
                    ],
                    [
                        'id' => 2,
                        'title' => 'Situación Económica',
                        'description' => 'Información sobre ingresos y empleo del demandado.',
                        'fields' => [
                            ['name' => 'monto_solicitado', 'label' => 'Monto o porcentaje solicitado', 'type' => 'text', 'required' => true],
                            ['name' => 'empresa_demandado', 'label' => 'Lugar de trabajo del demandado', 'type' => 'text', 'required' => false],
                            ['name' => 'sueldo_aproximado', 'label' => 'Sueldo mensual aproximado ($)', 'type' => 'number', 'required' => false],
                        ]
                    ],
                    [
                        'id' => 3,
                        'title' => 'Acreditación de Gastos',
                        'description' => 'Detalla las necesidades del menor.',
                        'fields' => [
                            ['name' => 'gastos_educacion', 'label' => 'Gastos de Educación (Mensual)', 'type' => 'number', 'required' => false],
                            ['name' => 'gastos_salud', 'label' => 'Gastos de Salud (Médico, medicinas)', 'type' => 'number', 'required' => false],
                            ['name' => 'gastos_recreacion', 'label' => 'Gastos de Recreación', 'type' => 'number', 'required' => false],
                        ]
                    ],
                    [
                        'id' => 4,
                        'title' => 'Documentación Requerida',
                        'description' => 'Checklist de documentos para el expediente.',
                        'checks' => [
                            'Acta de Nacimiento del menor (Original)',
                            'Acta de Matrimonio o Constancia de Concubinato',
                            'Comprobantes de domicilio',
                            'Lista de testigos (si aplica)',
                            'Recibos de gastos extraordinarios'
                        ]
                    ],
                    [
                        'id' => 5,
                        'title' => 'Generación de Demanda',
                        'description' => 'Finaliza el proceso y genera el documento.',
                        'action' => 'generate_format',
                        'template_suggest' => 'Demanda de Alimentos'
                    ]
                ]
            ]
        );

        \App\Models\LegalWorkflow::updateOrCreate(
            ['name' => 'Divorcio Incausado'],
            [
                'description' => 'Proceso ágil para disolver el vínculo matrimonial sin necesidad de expresar causa, incluyendo propuesta de convenio.',
                'materia' => 'Familiar',
                'icon' => 'academic-cap',
                'steps' => [
                    [
                        'id' => 1,
                        'title' => 'Datos Personales',
                        'description' => 'Información de los cónyuges.',
                        'fields' => [
                            ['name' => 'conyuge_solicitante', 'label' => 'Nombre del Cónyuge Solicitante', 'type' => 'text', 'required' => true],
                            ['name' => 'conyuge_demandado', 'label' => 'Nombre del Cónyuge Demandado', 'type' => 'text', 'required' => true],
                            ['name' => 'fecha_matrimonio', 'label' => 'Fecha de Matrimonio', 'type' => 'text', 'required' => true],
                        ]
                    ],
                    [
                        'id' => 2,
                        'title' => 'Hijos y Bienes',
                        'description' => 'Situación familiar y patrimonial.',
                        'fields' => [
                            ['name' => 'hijos_procreados', 'label' => 'Nombres y edades de los hijos', 'type' => 'text', 'required' => false],
                            ['name' => 'regimen_matrimonial', 'label' => 'Régimen (Sociedad Conyugal / Separación)', 'type' => 'text', 'required' => true],
                        ]
                    ],
                    [
                        'id' => 3,
                        'title' => 'Propuesta de Convenio',
                        'description' => 'Bases para la separación.',
                        'fields' => [
                            ['name' => 'propuesta_custodia', 'label' => 'Propuesta de Custodia', 'type' => 'text', 'required' => false],
                            ['name' => 'propuesta_alimentos', 'label' => 'Propuesta de Pensión', 'type' => 'text', 'required' => false],
                        ]
                    ],
                    [
                        'id' => 4,
                        'title' => 'Finalizar',
                        'description' => 'Generación del escrito inicial.',
                        'action' => 'generate_format',
                        'template_suggest' => 'Escrito Inicial de Divorcio'
                    ]
                ]
            ]
        );

        \App\Models\LegalWorkflow::updateOrCreate(
            ['name' => 'Contrato de Arrendamiento'],
            [
                'description' => 'Generador profesional de contratos de arrendamiento residencial o comercial con cláusulas de protección.',
                'materia' => 'Civil',
                'icon' => 'home',
                'steps' => [
                    [
                        'id' => 1,
                        'title' => 'Partes y Propiedad',
                        'description' => 'Quiénes rentan y qué se renta.',
                        'fields' => [
                            ['name' => 'arrendador', 'label' => 'Nombre del Arrendador', 'type' => 'text', 'required' => true],
                            ['name' => 'arrendatario', 'label' => 'Nombre del Arrendatario', 'type' => 'text', 'required' => true],
                            ['name' => 'direccion_inmueble', 'label' => 'Dirección Completa del Inmueble', 'type' => 'text', 'required' => true],
                        ]
                    ],
                    [
                        'id' => 2,
                        'title' => 'Condiciones Económicas',
                        'description' => 'Monto y fechas.',
                        'fields' => [
                            ['name' => 'renta_mensual', 'label' => 'Monto de Renta Mensual ($)', 'type' => 'number', 'required' => true],
                            ['name' => 'deposito_garantia', 'label' => 'Monto Depósito de Garantía ($)', 'type' => 'number', 'required' => true],
                            ['name' => 'dia_pago', 'label' => 'Día límite de pago (ej. 5)', 'type' => 'number', 'required' => true],
                        ]
                    ],
                    [
                        'id' => 3,
                        'title' => 'Vigencia y Otros',
                        'description' => 'Tiempos y fiador.',
                        'fields' => [
                            ['name' => 'vigencia_meses', 'label' => 'Meses de Vigencia (ej. 12)', 'type' => 'number', 'required' => true],
                            ['name' => 'nombre_fiador', 'label' => 'Nombre del Fiador (si aplica)', 'type' => 'text', 'required' => false],
                        ]
                    ],
                    [
                        'id' => 4,
                        'title' => 'Generar Contrato',
                        'description' => 'Crea el documento final.',
                        'action' => 'generate_format',
                        'template_suggest' => 'Contrato de Arrendamiento'
                    ]
                ]
            ]
        );

        \App\Models\LegalWorkflow::updateOrCreate(
            ['name' => 'Juicio Ejecutivo Mercantil'],
            [
                'description' => 'Cobro judicial de títulos de crédito (Pagarés, Cheques). Incluye preparación de demanda y diligencia de embargo.',
                'materia' => 'Mercantil',
                'icon' => 'currency-dollar',
                'steps' => [
                    [
                        'id' => 1,
                        'title' => 'Título de Crédito',
                        'description' => 'Datos del documento a cobrar.',
                        'fields' => [
                            ['name' => 'monto_principal', 'label' => 'Monto Principal ($)', 'type' => 'number', 'required' => true],
                            ['name' => 'fecha_suscripcion', 'label' => 'Fecha de Suscripción', 'type' => 'text', 'required' => true],
                            ['name' => 'fecha_vencimiento', 'label' => 'Fecha de Vencimiento', 'type' => 'text', 'required' => true],
                        ]
                    ],
                    [
                        'id' => 2,
                        'title' => 'Intereses y Deudor',
                        'description' => 'Cálculo de accesorios.',
                        'fields' => [
                            ['name' => 'tasa_interes', 'label' => 'Tasa de Interés Moratorio (%)', 'type' => 'number', 'required' => true],
                            ['name' => 'nombre_deudor', 'label' => 'Nombre del Deudor', 'type' => 'text', 'required' => true],
                            ['name' => 'domicilio_deudor', 'label' => 'Domicilio para Embargo', 'type' => 'text', 'required' => true],
                        ]
                    ],
                    [
                        'id' => 3,
                        'title' => 'Documentación',
                        'description' => 'Pruebas necesarias.',
                        'checks' => [
                            'Pagaré u Hoja de Cheque Original',
                            'Copia de Identificación del deudor (si se tiene)',
                            'Poder Notarial (si es persona moral)',
                            'Relación de bienes posibles para embargo'
                        ]
                    ],
                    [
                        'id' => 4,
                        'title' => 'Generar Demanda',
                        'description' => 'Crea la demanda ejecutiva.',
                        'action' => 'generate_format',
                        'template_suggest' => 'Demanda Ejecutiva Mercantil'
                    ]
                ]
            ]
        );

        \App\Models\LegalWorkflow::updateOrCreate(
            ['name' => 'Amparo Indirecto'],
            [
                'description' => 'Protección contra actos de autoridad que vulneren derechos humanos o preceptos constitucionales.',
                'materia' => 'Amparo',
                'icon' => 'shield-check',
                'steps' => [
                    [
                        'id' => 1,
                        'title' => 'Autoridades Responsables',
                        'description' => 'Quién emitió y quién ejecuta el acto.',
                        'fields' => [
                            ['name' => 'autoridad_ordenadora', 'label' => 'Autoridad Ordenadora', 'type' => 'text', 'required' => true],
                            ['name' => 'autoridad_ejecutora', 'label' => 'Autoridad Ejecutora', 'type' => 'text', 'required' => true],
                        ]
                    ],
                    [
                        'id' => 2,
                        'title' => 'Acto Reclamado',
                        'description' => 'El hecho que viola tus derechos.',
                        'fields' => [
                            ['name' => 'acto_reclamado', 'label' => 'Descripción del Acto Reclamado', 'type' => 'text', 'required' => true],
                            ['name' => 'fecha_notificacion', 'label' => 'Fecha en que tuvo conocimiento del acto', 'type' => 'text', 'required' => true],
                        ]
                    ],
                    [
                        'id' => 3,
                        'title' => 'Fundamentación',
                        'description' => 'Derechos vulnerados.',
                        'fields' => [
                            ['name' => 'articulos_violados', 'label' => 'Artículos Constitucionales (ej. 14, 16)', 'type' => 'text', 'required' => true],
                        ],
                        'checks' => [
                            'Copia certificada del acto (si se tiene)',
                            'Constancias de la autoridad',
                            'Pruebas documentales'
                        ]
                    ],
                    [
                        'id' => 4,
                        'title' => 'Generar Demanda',
                        'description' => 'Crea la demanda de amparo.',
                        'action' => 'generate_format',
                        'template_suggest' => 'Demanda de Amparo Indirecto'
                    ]
                ]
            ]
        );

        \App\Models\LegalWorkflow::updateOrCreate(
            ['name' => 'Sucesión Intestamentaria'],
            [
                'description' => 'Trámite para adjudicar bienes de una persona fallecida que no dejó testamento.',
                'materia' => 'Civil/Familiar',
                'icon' => 'library',
                'steps' => [
                    [
                        'id' => 1,
                        'title' => 'El Finado',
                        'description' => 'Datos de la persona fallecida.',
                        'fields' => [
                            ['name' => 'nombre_finado', 'label' => 'Nombre del Fallecido', 'type' => 'text', 'required' => true],
                            ['name' => 'fecha_defuncion', 'label' => 'Fecha de Defunción', 'type' => 'text', 'required' => true],
                            ['name' => 'ultimo_domicilio', 'label' => 'Último Domicilio', 'type' => 'text', 'required' => true],
                        ]
                    ],
                    [
                        'id' => 2,
                        'title' => 'Herederos',
                        'description' => 'Personas con derecho a la herencia.',
                        'fields' => [
                            ['name' => 'presuntos_herederos', 'label' => 'Nombres de hijos/cónyuge/padres', 'type' => 'text', 'required' => true],
                        ]
                    ],
                    [
                        'id' => 3,
                        'title' => 'Documentación',
                        'description' => 'Vitales para acreditar entroncamiento.',
                        'checks' => [
                            'Acta de Defunción (Original)',
                            'Actas de Nacimiento de los herederos',
                            'Acta de Matrimonio del finado',
                            'Certificado de no testamento (emitido por Registro Público)',
                            'Lista preliminar de bienes (bienes inmuebles, cuentas, etc.)'
                        ]
                    ],
                    [
                        'id' => 4,
                        'title' => 'Denunciar Juicio',
                        'description' => 'Crea el escrito inicial de denuncia.',
                        'action' => 'generate_format',
                        'template_suggest' => 'Denuncia de Sucesión Intestamentaria'
                    ]
                ]
            ]
        );

        \App\Models\LegalWorkflow::updateOrCreate(
            ['name' => 'Despido Injustificado'],
            [
                'description' => 'Reclamación laboral por terminación sin causa legal, buscando indemnización constitucional o reinstalación.',
                'materia' => 'Laboral',
                'icon' => 'user-minus',
                'steps' => [
                    [
                        'id' => 1,
                        'title' => 'Relación Laboral',
                        'description' => 'Cómo y cuándo trabajó.',
                        'fields' => [
                            ['name' => 'nombre_patron', 'label' => 'Nombre o Razón Social del Patrón', 'type' => 'text', 'required' => true],
                            ['name' => 'fecha_ingreso', 'label' => 'Fecha de Ingreso', 'type' => 'text', 'required' => true],
                            ['name' => 'salario_diario', 'label' => 'Salario Diario Integrado ($)', 'type' => 'number', 'required' => true],
                            ['name' => 'puesto', 'label' => 'Puesto Desempeñado', 'type' => 'text', 'required' => true],
                        ]
                    ],
                    [
                        'id' => 2,
                        'title' => 'El Despido',
                        'description' => 'Hechos de la terminación.',
                        'fields' => [
                            ['name' => 'fecha_despido', 'label' => 'Fecha y Hora del Despido', 'type' => 'text', 'required' => true],
                            ['name' => 'hechos_despido', 'label' => 'Breve narrativa de quién y cómo le despidió', 'type' => 'text', 'required' => true],
                        ]
                    ],
                    [
                        'id' => 3,
                        'title' => 'Prestaciones',
                        'description' => 'Lo que reclama el trabajador.',
                        'checks' => [
                            'Indemnización Constitucional (3 meses)',
                            'Prima de Antigüedad',
                            'Vacaciones y Prima Vacacional proporcional',
                            'Aguinaldo proporcional',
                            'Salarios Vencidos'
                        ]
                    ],
                    [
                        'id' => 4,
                        'title' => 'Generar Demanda',
                        'description' => 'Crea el escrito inicial de demanda laboral.',
                        'action' => 'generate_format',
                        'template_suggest' => 'Demanda de Despido Injustificado'
                    ]
                ]
            ]
        );
    }
}
