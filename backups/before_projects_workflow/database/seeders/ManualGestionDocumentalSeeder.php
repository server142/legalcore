<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ManualPage;
use Illuminate\Support\Str;

class ManualGestionDocumentalSeeder extends Seeder
{
    public function run(): void
    {
        // No truncamos para no borrar el manual existente.
        // Agregamos este módulo al final.
        
        $title = 'Gestión de Documentos Legales';
        
        ManualPage::updateOrCreate(
            ['slug' => Str::slug($title)],
            [
                'title' => $title,
                'content' => 'El módulo de **Gestión de Documentos Legales** permite a los administradores del despacho configurar y mantener las plantillas legales que se utilizan en la operación diaria, así como consultar los términos aceptados.

### **¿Para qué sirve este módulo?**
A diferencia de los términos y condiciones de la plataforma (que son globales), en esta sección usted administra **sus propios documentos legales**, aquellos que su despacho emite y firma con sus clientes finales.

**Funciones principales:**
1.  **Edición de Plantillas de Contrato:** Modifique el texto base del "Contrato de Prestación de Servicios". Esta plantilla es la que el sistema utilizará cuando usted haga clic en "Generar Contrato" dentro de un expediente.
2.  **Documentos Internos:** Publique códigos de ética, reglamentos internos o acuerdos de confidencialidad para sus propios abogados y empleados.
3.  **Control de Versiones:** Mantenga un historial de cambios. Al actualizar una versión, puede reiniciar el ciclo de aceptaciones si es necesario.

### **Tipos de Documentos Permitidos**
Como administrador de un despacho, usted tiene control sobre:
*   **Contrato de Prestación de Servicios:** La plantilla maestra para sus clientes. Configure aquí sus cláusulas estándar de honorarios, responsabilidades y alcances.
*   **Aviso de Privacidad (Interno):** Si requiere un aviso específico para el manejo de datos de sus empleados o clientes directos.
*   **Otros:** Reglamentos, manuales operativos, etc.

> **Nota Importante:** Los documentos globales de la plataforma (como los Términos de Uso de LegalCore o la Política de Cookies del sistema SaaS) son gestionados exclusivamente por los administradores del sistema y no son editables desde esta pantalla.

### **¿Cómo editar la Plantilla de Contratos?**
1.  Navegue a **Administración > Documentos Legales**.
2.  Busque el documento tipo "Contrato de Prestación de Servicios".
3.  Haga clic en el botón de **Editar (Lápiz)**.
4.  Utilice el editor de texto para ajustar las cláusulas. Puede copiar y pegar desde Word respetando el formato.
5.  Guarde los cambios.
6.  **Efecto Inmediato:** A partir de ese momento, cualquier nuevo contrato generado desde un expediente utilizará esta nueva redacción.

### **Variables Dinámicas para Plantillas**
Al redactar su "Contrato de Prestación de Servicios", puede utilizar las siguientes etiquetas. El sistema las reemplazará automáticamente con los datos reales de cada expediente al momento de generar el PDF.

| Código a insertar | Descripción / Dato que se mostrará |
| :--- | :--- |
| `{{CLIENTE_NOMBRE}}` | Nombre completo del cliente (Física o Moral). |
| `{{CLIENTE_RFC}}` | RFC del cliente (si está registrado). |
| `{{CLIENTE_DIRECCION}}` | Domicilio fiscal o legal del cliente. |
| `{{CLIENTE_EMAIL}}` | Correo electrónico de contacto. |
| `{{EXPEDIENTE_FOLIO}}` | Número identificador del expediente (ej. 123/2024). |
| `{{EXPEDIENTE_TITULO}}` | Nombre interno del caso en el sistema. |
| `{{EXPEDIENTE_MATERIA}}` | Materia jurídica (Civil, Penal, etc.). |
| `{{EXPEDIENTE_JUZGADO}}` | Juzgado o autoridad donde se tramita. |
| `{{HONORARIOS_TOTALES}}` | Monto total de honorarios pactados (Formato moneda). |
| `{{ABOGADO_RESPONSABLE}}` | Nombre del abogado titular asignado al caso. |
| `{{FECHA_ACTUAL}}` | Fecha del día en que se genera el documento (dd/mm/aaaa). |
| `{{CIUDAD_FIRMA}}` | Ciudad configurada por defecto (ej. Ciudad de México). |

**Ejemplo de uso en el editor:**
*"En la ciudad de `{{CIUDAD_FIRMA}}`, a `{{FECHA_ACTUAL}}`, comparece por una parte **`{{CLIENTE_NOMBRE}}`**, en adelante EL CLIENTE..."*

### **Seguridad y Permisos**
Este módulo es exclusivo para usuarios con rol de **Administrador**. Los abogados y pasantes no tienen acceso a modificar estas plantillas para garantizar la integridad legal de la firma.',
                'image_path' => 'manual/documentos-legales.png',
                'order' => 11, // Siguiente en la lista
            ]
        );
    }
}
