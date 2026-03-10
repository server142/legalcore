<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ManualPage;
use Illuminate\Support\Str;

class ManualAsesoriasSeeder extends Seeder
{
    public function run(): void
    {
        $title = 'Asesorías';

        $content = <<<'MD'
El módulo de **Asesorías** está diseñado para gestionar citas de atención jurídica (presencial, telefónica o videoconferencia), registrar su resultado y, si tu despacho lo habilita, registrar el **cobro** y emitir un **recibo (PDF)**.

## 1) ¿Qué puedes hacer con Asesorías?
- **Agendar** una asesoría con fecha/hora, duración y abogado asignado.
- **Evitar choques de agenda** (si tu tenant tiene activada la validación de disponibilidad).
- **Registrar seguimiento**: realizada, cancelada o no atendida.
- **Registrar costo** y (si se habilita por tenant y por permisos) **marcar como pagada**.
- **Convertir el caso** en un expediente (si aplica al flujo del despacho).

## 2) Crear una asesoría (flujo recomendado)
1. Selecciona el **Cliente** (obligatorio).
2. Captura/valida los datos de contacto (se autocompletan desde el cliente).
3. Indica el **asunto** y notas.
4. Define la cita:
   - **Fecha**
   - **Hora**
   - **Duración**
   - **Tipo** (presencial/telefónica/videoconferencia)
5. Asigna el **abogado** (si eres admin; si no, se asigna automáticamente).
6. Define el **costo**.

Al guardar, el sistema genera un **folio** automático (ej. `ASE-00001`).

## 3) Tipos de asesoría
- **Presencial:** cita en oficina.
- **Telefónica:** atención por llamada.
- **Videoconferencia:** permite guardar un **link** (Meet/Zoom/etc.).

## 4) Estados y seguimiento
Una asesoría puede pasar por estos estados:
- **Agendada:** cita futura.
- **Realizada:** se atendió y puedes registrar un resumen.
- **Cancelada:** requiere motivo.
- **No atendida:** requiere motivo (no show).

Cuando marcas **Realizada**, puedes registrar:
- **Resumen / conclusiones**
- **¿El prospecto aceptó contratar?** (para disparar acciones posteriores del flujo del despacho)

## 5) Validación de disponibilidad y sugerencia de horario
Si tu tenant tiene activado **Forzar disponibilidad**, el sistema valida que el abogado no tenga traslapes con otros eventos.

Si hay conflicto:
- se bloquea el guardado,
- y puede sugerirse el **siguiente horario disponible**.

El comportamiento depende de la configuración del despacho:
- **Horario laboral (inicio/fin)**
- **Días hábiles**
- **Granularidad (minutos)**: define el tamaño del bloque de tiempo para redondeo y búsqueda de horarios.

## 6) Pago, facturación e ingreso (por tenant)
Este apartado solo aparece si:
- En **Configuración del Despacho** está activo: **Habilitar cobros de Asesorías**
- El usuario tiene permiso **manage billing**

Cuando se marca una asesoría como **Pagada**:
- Se crea o actualiza una **Factura** en estado **pagada**
- Se liga a la asesoría vía `factura_id`
- Esto permite emitir un **Recibo (PDF)** con el formato estándar del sistema

### IVA opcional
La configuración **Aplicar IVA (16%)** controla si el recibo calcula IVA desde el total:
- **Activo:** subtotal = total/1.16, IVA = total - subtotal
- **Inactivo:** subtotal = total, IVA = 0

## 7) Recibo (PDF)
Cuando la asesoría ya tiene `factura_id`, el sistema muestra un enlace para **Descargar recibo (PDF)**.

## 8) Recomendaciones operativas
- Usa **15 min** de granularidad como estándar para mantener consistencia en agenda.
- Mantén el costo en asesorías para medir ticket promedio y productividad.
- Controla el permiso **manage billing** para que solo personal autorizado registre pagos.
MD;

        ManualPage::updateOrCreate(
            ['title' => $title],
            [
                'slug' => Str::slug($title),
                'content' => $content,
                'image_path' => 'manual/asesorias.png',
                'order' => 6,
            ]
        );
    }
}
