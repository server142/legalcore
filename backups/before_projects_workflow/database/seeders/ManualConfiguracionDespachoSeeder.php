<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ManualPage;
use Illuminate\Support\Str;

class ManualConfiguracionDespachoSeeder extends Seeder
{
    public function run(): void
    {
        $title = 'Configuración del Despacho';

        $content = <<<'MD'
El apartado **Configuración del Despacho** te permite adaptar LegalCore a la operación y la identidad de tu firma. Aquí se guardan datos que después se utilizan en reportes, PDFs, recordatorios y reglas de agenda.

## 1) Identidad del despacho (branding)
En esta sección defines la información institucional que aparece en el sistema y en documentos:

- **Nombre del despacho**
  - Se muestra como nombre del tenant.
  - Aparece en encabezados de PDFs.

- **Titular del despacho**
  - Dato informativo que puede aparecer en PDFs.

- **Dirección física**
  - Se utiliza en PDFs y en la información general del despacho.

- **Logotipo**
  - Al subir un logo, se incrusta en los PDFs (por compatibilidad se convierte a **base64** al generar el documento).
  - Recomendación: usar PNG/JPG de buena resolución, fondo transparente si aplica.

- **Datos generales / notas**
  - Texto que se imprime en la sección de notas de documentos como el **Recibo de Dinero** (PDF).

## 2) Notificaciones SMS (Términos)
Este bloque controla recordatorios por SMS relacionados con términos (plazos):

- **Activar avisos por SMS**
- **Avisar cuántos días antes**
- **Números de teléfono a notificar** (separados por coma)

Esto ayuda a reducir el riesgo de omisiones en plazos críticos.

## 3) Asesorías y Agenda
Estas opciones impactan el comportamiento de la agenda y el control de disponibilidad en el módulo de asesorías.

### Horarios laborales
- **Horario laboral (inicio)** y **Horario laboral (fin)**
  - Define el rango de atención del despacho para asesorías.

### Días hábiles
- Selecciona qué días se consideran laborables.

### Granularidad (minutos)
Este valor define el **tamaño del “bloque” de tiempo** que usa el sistema para:

- **Redondear** horarios a marcas válidas (por ejemplo, cada 15 minutos).
- **Buscar el siguiente horario disponible** cuando existe un conflicto de agenda.

Ejemplo:
- Con granularidad **15 min**, un horario como 10:07 se redondea a 10:15.

### Forzar disponibilidad
- Si está activo, el sistema valida que el abogado tenga disponibilidad antes de agendar.
- Si hay conflicto, puede proponer un horario alternativo.

### Sincronizar con Agenda
- Si está activo, la asesoría se refleja como evento en la agenda interna.

## 4) Cobros de Asesorías (Facturación / Recibo / WhatsApp)
Este bloque habilita (por tenant) la integración de asesorías con ingresos.

- **Habilitar cobros de Asesorías (Facturación/Recibo/WhatsApp)**
  - Al estar activo, el sistema permite registrar una asesoría como **pagada** (solo usuarios con permiso **manage billing**).
  - Al marcarla pagada, se genera o actualiza una **Factura** en estado **pagada** para registrar el ingreso y poder emitir el **Recibo (PDF)** con el formato actual.

- **Aplicar IVA (16%) en recibos de asesorías**
  - Si está activo, el sistema calcula el IVA a partir del total.
  - Si está desactivado, el total se registra como subtotal y el IVA se maneja en $0.00.

## Recomendaciones
- Mantén actualizado el logo y los datos generales para que los PDFs salgan con imagen profesional.
- Ajusta la granularidad según tu operación:
  - **15 min** (estándar),
  - **30 min** (agenda más rígida),
  - **5–10 min** (más flexible).
- Si vas a habilitar cobros de asesorías, asegúrate de que solo personal autorizado tenga **manage billing**.
MD;

        ManualPage::updateOrCreate(
            ['title' => $title],
            [
                'slug' => Str::slug($title),
                'content' => $content,
                'image_path' => 'manual/configuracion.png',
                'order' => 10,
            ]
        );
    }
}
