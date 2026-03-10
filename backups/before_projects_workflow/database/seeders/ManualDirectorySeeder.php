<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ManualDirectorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('manual_pages')->updateOrInsert(
            ['slug' => 'guia-directorio-publico'],
            [
                'title' => 'Gestión de Directorio Público',
                'content' => "
### ¿Qué es el Directorio Público?

El **Directorio Público de Abogados** es una vitrina digital diseñada para conectar directamente a profesionales del derecho con clientes potenciales. Al formar parte de este directorio, su perfil profesional estará visible para miles de personas que buscan asesoría legal diariamente.

### Configuración de su Perfil

Para maximizar sus oportunidades, es crucial mantener su perfil actualizado y completo. Puede acceder a la gestión de su perfil desde el menú principal seleccionando la opción **'Perfil Público'**.

**Campos Editables:**
1.  **Titular Profesional:** Una frase corta y potente que resuma su especialidad principal (ej. *'Especialista en Derecho Laboral y Despidos Injustificados'*).
2.  **Biografía:** Una descripción detallada de su experiencia, enfoque y valores.
3.  **Especialidades:** Seleccione hasta 10 áreas de práctica para que los clientes puedan encontrarlo fácilmente mediante filtros.
4.  **Ubicación:** Ciudad y Estado donde ejerce principalmente.
5.  **Cédula Profesional:** Esencial para generar confianza y obtener la insignia de 'Verificado'.
6.  **Contacto:** Enlaces directos a su WhatsApp, sitio web y perfil de LinkedIn.

### Visibilidad del Perfil

Usted tiene el control total sobre cuándo es visible su perfil:

-   **Público:** Su perfil aparece en las búsquedas y cualquier persona puede verlo.
-   **Oculto:** Su perfil no aparecerá en el directorio, útil si está actualizando información o tomándose un descanso.

Puede alternar este estado usando el interruptor **'Hacer mi perfil visible públicamente'** en la parte superior del editor.

### Planes y Características

Dependiendo de su plan, tendrá acceso a diferentes características:

-   **Plan Gratuito:** Listado básico en el directorio.
-   **Plan Destacado:** Insignia de verificación, botón directo de WhatsApp, prioridad en resultados de búsqueda.
-   **Diogenes Suite:** Acceso total al directorio más todas las herramientas de gestión del despacho (Expedientes, Clientes, IA).

> **Consejo Pro:** Los perfiles con foto profesional y todos los campos completados reciben hasta un **300% más de contactos** que los perfiles incompletos.
                ",
                // 'category' => 'general', // Column not found
                // 'is_active' => true, // Column not found
                'order' => 90, // Un orden alto para que aparezca hacia el final
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        
        $this->command->info('Sección del manual "Gestión de Directorio Público" agregada correctamente.');
    }
}
