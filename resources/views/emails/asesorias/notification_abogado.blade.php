<x-mail::message>
# Nueva Asignación de Asesoría

Hola **{{ $asesoria->abogado->name }}**,

Se te ha asignado una nueva asesoría/cita en el sistema. A continuación los detalles del prospecto/cliente:

**Folio:** {{ $asesoria->folio }}
**Cliente / Prospecto:** {{ $asesoria->nombre_prospecto }}
**Asunto:** {{ $asesoria->asunto }}
**Fecha:** {{ $asesoria->fecha_hora->translatedFormat('d \d\e F \d\e Y') }}
**Hora:** {{ $asesoria->fecha_hora->format('H:i') }} hrs.
**Tipo:** {{ ucfirst($asesoria->tipo) }}
**Contacto:** {{ $asesoria->telefono ?? 'Sin teléfono' }} - {{ $asesoria->email ?? 'Sin email' }}

@if($asesoria->notas)
**Notas Internas:**
{{ $asesoria->notas }}
@endif

@if($asesoria->tipo === 'videoconferencia' && $asesoria->link_videoconferencia)
**Enlace de Videollamada:** [Unirse a la reunión]({{ $asesoria->link_videoconferencia }})
@endif

<x-mail::button :url="route('asesorias.edit', $asesoria->id)">
Ver y Gestionar Asesoría
</x-mail::button>

<x-mail::button :url="$publicUrl" color="success">
Ver Ficha Pública (QR)
</x-mail::button>

Gracias,
**Sistema Diógenes**
</x-mail::message>
