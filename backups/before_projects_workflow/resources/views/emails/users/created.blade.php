<x-mail::message>
<div style="text-align: center; margin-bottom: 20px;">
<img src="{{ $message->embed(public_path('favicon.png')) }}" alt="Logo" style="width: 80px; height: 80px; border-radius: 12px;">
</div>

# {{ $subjectStr }}

{!! nl2br(e($bodyStr)) !!}

<x-mail::button :url="config('app.url') . '/login'">
Comenzar ahora
</x-mail::button>

**Tus datos de acceso:**
* **Usuario:** {{ $user->email }}
@if($password)
* **Contraseña Temporal:** {{ $password }}
@endif

<x-mail::panel>
**¿Necesitas ayuda?**
Recuerda que tienes a tu disposición nuestro Asistente de IA y el Manual de Usuario dentro de la plataforma para resolver cualquier duda.
</x-mail::panel>

¡Gracias por confiar en nosotros!

Atentamente,<br>
El equipo de {{ config('app.name') }}
</x-mail::message>
