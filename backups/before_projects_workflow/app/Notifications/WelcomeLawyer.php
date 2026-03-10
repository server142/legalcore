<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;
use App\Models\User;

class WelcomeLawyer extends Notification implements ShouldQueue
{
    use Queueable;

    public $password;

    /**
     * Create a new notification instance.
     */
    public function __construct($password)
    {
        $this->password = $password;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addHours(48),
            ['id' => $notifiable->getKey(), 'hash' => sha1($notifiable->getEmailForVerification())]
        );

        return (new MailMessage)
                    ->subject('Bienvenido al Despacho - Invitación')
                    ->greeting('Hola ' . $notifiable->name . ',')
                    ->line('Has sido invitado a unirte a la plataforma de gestión del despacho.')
                    ->line('Tu cuenta ha sido creada con las siguientes credenciales:')
                    ->line('Email: ' . $notifiable->email)
                    ->line('Contraseña: ' . $this->password)
                    ->action('Activar Cuenta e Iniciar Sesión', $url)
                    ->line('Por favor, cambia tu contraseña después de iniciar sesión.')
                    ->line('¡Gracias por usar nuestra aplicación!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
