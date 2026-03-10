<x-mail::message>
# {{ $is_admin ? 'Reporte Maestro de Vencimientos' : 'Resumen Semanal de Vencimientos' }}

Hola, **{{ $user->name }}**.

Este es el resumen de los expedientes que tienen términos legales por vencer próximamente o que ya están vencidos.

<x-mail::table>
| Expediente | Cliente | Vencimiento | Estado | {{ $is_admin ? 'Responsable' : '' }} |
| :--- | :--- | :--- | :--- | :--- |
@foreach($expedientes as $exp)
@php
    $days = now()->startOfDay()->diffInDays($exp->vencimiento_termino, false);
    $status = $days < 0 ? '❌ VENCIDO' : ($days == 0 ? '🚨 HOY' : ($days <= 3 ? '⚠️ Proximo' : '⏳ Normal'));
@endphp
| [{{ $exp->numero }}]({{ config('app.url') . '/expedientes/' . $exp->id }}) | {{ Str::limit($exp->cliente->nombre, 20) }} | {{ $exp->vencimiento_termino->format('d/m/Y') }} | {{ $status }} | {{ $is_admin ? ($exp->abogado->name ?? 'N/A') : '' }} |
@endforeach
</x-mail::table>

Si un expediente ya fue atendido, por favor asegúrese de actualizar el estado procesal en el sistema para mantener este reporte al día.

<x-mail::button :url="config('app.url') . '/expedientes'">
Ir a Mis Expedientes
</x-mail::button>

Saludos,<br>
{{ config('app.name') }}
</x-mail::message>
