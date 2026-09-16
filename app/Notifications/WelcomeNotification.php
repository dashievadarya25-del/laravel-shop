<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeNotification extends Notification
{
    use Queueable;

    public function __construct()
    {
        //
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Представление приветственного письма.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject('Добро пожаловать в Laravel Shop!')
            ->greeting('Ура, ' . $notifiable->first_name . '!')
            ->line('Ваш адрес электронной почты успешно подтвержден, и аккаунт теперь полностью активирован.')
            ->line('Вам открылся полный доступ к функционалу магазина. Теперь вы можете оформлять заказы, управлять адресами доставки и отслеживать покупки в личном кабинете.')
            ->action('Перейти к покупкам', url('/products'))
            ->line('Спасибо, что зарегистрировались у нас!')
            ->salutation('С уважением, команда Laravel Shop');
    }

    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
