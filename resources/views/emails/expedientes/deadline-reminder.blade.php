<x-mail::message>
<div style="text-align: center; margin-bottom: 20px;">
    <img src="{{ config('app.url') }}/favicon.png" width="80" alt="Diogenes AI" style="border-radius: 8px;">
</div>

<div style="text-align: center; border-bottom: 2px solid #e53e3e; padding-bottom: 10px; margin-bottom: 20px;">
    <h1 style="color: #c53030; margin: 0; font-size: 24px; font-weight: 800; text-transform: uppercase;">Notificación Urgente</h1>
    <h2 style="color: #718096; margin: 5px 0 0; font-size: 16px; font-weight: normal;">Vencimiento de Término Legal</h2>
</div>

Hola, **{{ $user->name }}**.

El sistema **Diogenes AI** ha detectado un término legal con vencimiento inminente que requiere tu atención inmediata para evitar la preclusión de derechos procesales.

<x-mail::panel>
### Expediente: **{{ $expediente->numero }}**

- **Título:** {{ $expediente->titulo }}
- **Vence en:** <span style="color: #e53e3e; font-weight: bold; font-size: 1.2em;">{{ $timeLabel }}</span>
- **Fecha Límite:** {{ $expediente->vencimiento_termino ? $expediente->vencimiento_termino->format('d/m/Y') : 'N/A' }}
- **Juzgado:** {{ $expediente->juzgado }}
- **Cliente:** {{ $expediente->cliente ? $expediente->cliente->nombre : 'N/A' }}
</x-mail::panel>

---

Es imperativo que se realicen las acciones necesarias (contestación, recursos, desahogo de vistas, etc.) antes de la fecha indicada.

<x-mail::button :url="config('app.url') . '/expedientes/' . $expediente->id" color="error">
⚠️ Ver Expediente y Atender
</x-mail::button>

<div style="text-align: center; font-size: 11px; color: #a0aec0; margin-top: 30px;">
    Este es un mensaje automático generado por <strong>Diogenes AI</strong>.<br>
    Por favor no respondas a este correo.
</div>
</x-mail::message>
