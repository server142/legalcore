<x-mail::message>
<div style="text-align: center; margin-bottom: 20px;">
<img src="{{ $message->embed(public_path('favicon.png')) }}" alt="Logo" style="width: 80px; height: 80px; border-radius: 12px;">
</div>

# {{ $subjectStr }}

{!! nl2br(e($bodyStr)) !!}

<x-mail::button :url="config('app.url') . '/login'">
Acceder a mi Cuenta
</x-mail::button>

**Detalles de tu cuenta:**
* **Email:** {{ $user->email }}
* **Contraseña Temporal:** {{ $password }}

*Por favor, cambia tu contraseña una vez que hayas ingresado por primera vez.*

Si no esperabas esta invitación, puedes ignorar este correo.

Atentamente,<br>
El equipo de {{ config('app.name') }} & {{ $despachoName }}
</x-mail::message>
