<x-mail::message>
# ⚠️ TÉRMINO LEGAL POR VENCER

Hola, **{{ $user->name }}**.

Este es un aviso automático de **máxima prioridad**. El plazo fatal para el siguiente expediente está próximo a cumplirse:

<x-mail::panel>
### Expediente: **{{ $expediente->numero }}**
**Título:** {{ $expediente->titulo }}
**Vence en:** <span style="color: #e53e3e; font-weight: bold; font-size: 1.2em;">{{ $timeLabel }}</span>
**Fecha Exacta:** {{ $expediente->vencimiento_termino->format('d/m/Y') }}
</x-mail::panel>

---

### Detalles del Asunto:
- **Materia:** {{ $expediente->materia }}
- **Juzgado:** {{ $expediente->juzgado }}
- **Cliente:** {{ $expediente->cliente->nombre }}

Es imperativo que se realicen las acciones necesarias (contestación, recursos, etc.) antes de que concluya este término.

<x-mail::button :url="config('app.url') . '/expedientes/' . $expediente->id" color="error">
Ver Expediente Ahora
</x-mail::button>

Saludos,<br>
Sistema de Alertas Diogenes
</x-mail::message>
