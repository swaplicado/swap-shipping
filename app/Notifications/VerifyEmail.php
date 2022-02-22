<?php
namespace App\Notifications;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Lang;
use Illuminate\Auth\Notifications\VerifyEmail as VerifyEmailBase;

class VerifyEmail extends VerifyEmailBase
{
//    use Queueable;

    // change as you want
    public function toMail($notifiable)
    {
        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable);
        }
        return (new MailMessage)
            ->subject(Lang::getFromJson('Correo de verificación'))
            ->line(Lang::getFromJson('Por favor haga click en el boton para verificar su dirección de correo.'))
            ->action(
                Lang::getFromJson('Verificar dirección de correo'),
                $this->verificationUrl($notifiable)
            )
            ->line(Lang::getFromJson('Si usted no ha creado una cuenta ignore este mensaje.'));
    }
}