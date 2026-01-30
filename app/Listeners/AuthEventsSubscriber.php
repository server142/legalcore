<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Failed;
use Illuminate\Events\Dispatcher;
use App\Traits\Auditable;

class AuthEventsSubscriber
{
    use Auditable;

    public function handleUserLogin(Login $event)
    {
        $this->logAudit('login', 'Seguridad', "Inició sesión: {$event->user->name}", [
            'email' => $event->user->email,
            'role' => $event->user->getRoleNames() 
        ]);
    }

    public function handleUserLogout(Logout $event)
    {
        if ($event->user) {
            $this->logAudit('logout', 'Seguridad', "Cerró sesión: {$event->user->name}");
        }
    }

    public function handleFailedLogin(Failed $event)
    {
        $email = $event->credentials['email'] ?? 'Desconocido';
        
        $this->logAudit('login_fallido', 'Seguridad', "Intento de acceso fallido para: {$email}", [
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);
    }

    public function subscribe(Dispatcher $events)
    {
        $events->listen(Login::class, [self::class, 'handleUserLogin']);
        $events->listen(Logout::class, [self::class, 'handleUserLogout']);
        $events->listen(Failed::class, [self::class, 'handleFailedLogin']);
    }
}
