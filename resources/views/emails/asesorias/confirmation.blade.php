<x-mail::message>
# Confirmación de Cita Legal

Hola **{{ $asesoria->nombre_prospecto }}**,

Le informamos que su cita ha sido agendada exitosamente en nuestro sistema. Aquí están los detalles:

**Asunto:** {{ $asesoria->asunto }}
**Fecha:** {{ $asesoria->fecha_hora->translatedFormat('d \d\e F \d\e Y') }}
**Hora:** {{ $asesoria->fecha_hora->format('H:i') }} hrs.
**Tipo:** {{ ucfirst($asesoria->tipo) }}
**Abogado responsable:** {{ $asesoria->abogado->name ?? 'Por asignar' }}

@if($asesoria->tipo === 'videoconferencia' && $asesoria->link_videoconferencia)
**Enlace de Videollamada:** [Unirse a la reunión]({{ $asesoria->link_videoconferencia }})
@endif

<x-mail::button :url="$publicUrl">
Ver Comprobante de Cita
</x-mail::button>

Si necesita cancelar o reagendar su cita, por favor póngase en contacto con nosotros a la brevedad.

Gracias por confiar en **Diógenes**.

Atentamente,
El equipo de {{ config('app.name') }}
</x-mail::message>
