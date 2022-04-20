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
    public function __construct($tempPass)
    {
        $this->tempPass = $tempPass;
    }

    protected function secondaryVerificationUrl($notifiable)
    {
        $forceRuta = URL::forceRootUrl('10.83.30.2');

        $ruta = URL::signedRoute(
            'verification.verify',
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable),
            ]
        );

        return $ruta;
    }

//    use Queueable;

    // change as you want
    public function toMail($notifiable)
    {
        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable);
        }
        if(!is_null($this->tempPass)){
            return (new MailMessage)
                ->subject(Lang::getFromJson('Correo de verificación'))
                ->line(Lang::getFromJson('Por favor haga click en el boton para verificar su dirección de correo.'))
                ->line(Lang::getFromJson('Su contraseña actual es "'.$this->tempPass.'" , le recomendamos cambie su contraseña
                una vez ingrese al sistema por primera vez.'))
                ->action(
                    Lang::getFromJson('Verificar dirección de correo'),
                    $this->verificationUrl($notifiable)
                )
                ->line(Lang::getFromJson('Si usted no ha creado una cuenta en CPT ignore este mensaje.'));    
        }else{
            return (new MailMessage)
                ->subject(Lang::getFromJson('Correo de verificación'))
                ->line(Lang::getFromJson('Si se encuentra dentro de la planta de Cartro:'))
                ->action(
                    [Lang::getFromJson('Click aquí para verificar su dirección de correo'), Lang::getFromJson('Click aquí para verificar su dirección de correo')],
                    [$this->secondaryVerificationUrl($notifiable), $this->verificationUrl($notifiable)]
                )
                ->line(Lang::getFromJson('Si usted no ha creado una cuenta en CPT ignore este mensaje.'));    
        }
    }
}