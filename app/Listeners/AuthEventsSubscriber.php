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
        $this->logAudit('login', 'Seguridad', "Inició sesión: {$event->user->email}", [
            'name' => $event->user->name,
            'roles' => $event->user->getRoleNames() 
        ], $event->user->hasRole('super_admin') ? 'medium' : 'low');
    }

    public function handleUserLogout(Logout $event)
    {
        if ($event->user) {
            $this->logAudit('logout', 'Seguridad', "Cerró sesión: {$event->user->email}");
        }
    }

    public function handleFailedLogin(Failed $event)
    {
        $email = $event->credentials['email'] ?? 'Desconocido';
        
        $this->logAudit('login_fallido', 'Seguridad', "Intento de acceso fallido: {$email}", [
            'credentials_sent' => $event->credentials,
        ], 'critical');
    }

    public function subscribe(Dispatcher $events)
    {
        $events->listen(Login::class, [self::class, 'handleUserLogin']);
        $events->listen(Logout::class, [self::class, 'handleUserLogout']);
        $events->listen(Failed::class, [self::class, 'handleFailedLogin']);
    }
}
