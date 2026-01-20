<x-mail::message>
# Hola {{ $user->name }},

Se ha creado una cuenta para ti en **{{ config('app.name') }}**.

Aquí tienes tus datos de acceso:

**Usuario:** {{ $user->email }}
**Contraseña:** {{ $password }}

Puedes iniciar sesión haciendo clic en el siguiente botón:

<x-mail::button :url="route('login')">
Iniciar Sesión
</x-mail::button>

Por seguridad, te recomendamos cambiar tu contraseña una vez que hayas ingresado.

Gracias,<br>
El equipo de {{ config('app.name') }}
</x-mail::message>
