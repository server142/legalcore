<x-mail::message>
# Recordatorio de Agenda: Próximo Vencimiento

Hola, **{{ $user->name }}**.

Te informamos que hay un evento próximo a vencer en el expediente **{{ $event->expediente->numero }}**:

**Evento:** {{ $event->titulo }}
**Fecha:** {{ $event->start_time->format('d/m/Y') }}
**Hora:** {{ $event->start_time->format('H:i') }}

<x-mail::panel>
**Descripción:**
{{ $event->descripcion ?: 'Sin descripción adicional.' }}
</x-mail::panel>

---

### Detalles del Expediente:
- **Título:** {{ $event->expediente->titulo }}
- **Juzgado:** {{ $event->expediente->juzgado }}
- **Materia:** {{ $event->expediente->materia }}

Este es un recordatorio automático programado para enviarse **{{ $timeLabel }}** antes del vencimiento.

<x-mail::button :url="config('app.url') . '/expedientes/' . $event->expediente_id . '?activeTab=agenda'">
Ver Agenda del Expediente
</x-mail::button>

Saludos,<br>
El equipo de {{ config('app.name') }}
</x-mail::message>
