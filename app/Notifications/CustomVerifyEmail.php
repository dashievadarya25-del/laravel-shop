<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;

class CustomVerifyEmail extends BaseVerifyEmail
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        return (new MailMessage())
            ->subject('Подтверждение адреса электронной почты')
            ->greeting('Здравствуйте, ' . $notifiable->first_name . '!')
            ->line('Спасибо за регистрацию на нашем сайте! Пожалуйста, подтвердите свой email, чтобы получить доступ к оформлению заказов.')
            ->action('Подтвердить Email', $verificationUrl)
            ->line('Если вы не создавали аккаунт, просто проигнорируйте это письмо.')
            ->salutation('С уважением, команда Laravel Shop');
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
