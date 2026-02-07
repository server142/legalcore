<x-mail::message>
# Hola, {{ $user->name }}

Se te ha asignado al expediente **{{ $expediente->numero }}** - *{{ $expediente->titulo }}*.

@if($isResponsible)
**Has sido designado como el Abogado Responsable de este caso.**
@else
Has sido agregado como colaborador en este caso.
@endif

**Detalles del Expediente:**
- **Materia:** {{ $expediente->materia }}
- **Juzgado:** {{ $expediente->juzgado }}
- **Cliente:** {{ $expediente->cliente->nombre }}

Puedes ver todos los detalles y actuaciones del expediente haciendo clic en el siguiente botón:

<x-mail::button :url="config('app.url') . '/expedientes/' . $expediente->id">
Ver Expediente
</x-mail::button>

Saludos,<br>
El equipo de {{ config('app.name') }}
</x-mail::message>
