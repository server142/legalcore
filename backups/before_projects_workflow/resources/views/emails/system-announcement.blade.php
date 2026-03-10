<x-mail::message>
# {{ $subjectLine }}

<div style="font-size: 1.1em; line-height: 1.6; color: #374151;">
{!! nl2br(e($content)) !!}
</div>

<x-mail::button :url="config('app.url')">
Acceder a Diogenes
</x-mail::button>

Gracias,<br>
**El Equipo de {{ config('app.name') }}**
<small style="color: #9ca3af;">Asistente Jurídico Inteligente</small>
</x-mail::message>
