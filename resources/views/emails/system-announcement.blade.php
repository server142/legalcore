<x-mail::message>
# {{ $subjectLine }}

{!! nl2br(e($content)) !!}

Gracias,<br>
{{ config('app.name') }}
</x-mail::message>
