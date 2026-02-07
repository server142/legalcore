<x-mail::message>
# Nuevo Comentario en Expediente

Hola, **{{ $user->name }}**.

El abogado **{{ $author->name }}** ha realizado un nuevo comentario en el expediente **{{ $comentario->expediente->numero }}**:

<x-mail::panel>
"{{ $comentario->contenido }}"
</x-mail::panel>

---

### Detalles del Expediente:
- **Título:** {{ $comentario->expediente->titulo }}
- **Materia:** {{ $comentario->expediente->materia }}
- **Cliente:** {{ $comentario->expediente->cliente->nombre }}

Puedes responder a este comentario o ver más detalles haciendo clic en el siguiente botón:

<x-mail::button :url="config('app.url') . '/expedientes/' . $comentario->expediente_id . '?activeTab=comentarios'" color="success">
Responder Comentario
</x-mail::button>

Saludos,<br>
El equipo de {{ config('app.name') }}
</x-mail::message>
