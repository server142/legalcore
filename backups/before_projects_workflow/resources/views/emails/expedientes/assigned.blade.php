<x-mail::message>
# Hola, {{ $user->name }}

Se te ha asignado al expediente **{{ $expediente->numero }}** - *{{ $expediente->titulo }}*.

@if($isResponsible)
**Has sido designado como el Abogado Responsable de este caso.**
@else
Has sido agregado como colaborador en este caso.
@endif

---

### ¿Qué puedes hacer con este expediente en Diogenes?

Desde nuestra plataforma, tienes acceso a herramientas avanzadas para la gestión integral del caso:

*   **⚡ Bitácora de Actuaciones:** Registra y consulta cada paso procesal y avance del caso de forma cronológica.
*   **📂 Gestión de Documentos:** Sube, organiza y visualiza escritos, pruebas y anexos (PDF, imágenes, video y audio) con nuestro visor integrado.
*   **📅 Agenda Judicial:** Controla fechas de audiencias, citas y términos legales importantes con recordatorios automáticos.
*   **🤖 Asistente con IA:** Utiliza nuestra inteligencia artificial para analizar el contenido de tus documentos y recibir resúmenes o sugerencias jurídicas.
*   **💬 Comentarios y Colaboración:** Mantente en comunicación directa con los demás abogados asignados, dejando notas y observaciones clave.
*   **💰 Control Financiero:** Realiza el seguimiento de los honorarios pactados, pagos recibidos y saldos pendientes del expediente.

---

**Detalles Generales:**
- **Materia:** {{ $expediente->materia }}
- **Juzgado:** {{ $expediente->juzgado }}
- **Cliente:** {{ $expediente->cliente->nombre }}

<x-mail::button :url="config('app.url') . '/expedientes/' . $expediente->id">
Ver Expediente
</x-mail::button>

<x-mail::button :url="config('app.url') . '/expedientes/' . $expediente->id . '?activeTab=comentarios'" color="success">
Comentar el expediente
</x-mail::button>

Saludos,<br>
El equipo de {{ config('app.name') }}
</x-mail::message>
