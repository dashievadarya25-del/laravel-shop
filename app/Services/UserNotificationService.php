<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Notifications\CustomVerifyEmail;
use App\Notifications\WelcomeNotification;

class UserNotificationService
{
    /**
     * Отправка ссылки для верификации email.
     * Вызывается из SendRegistrationVerificationJob.
     */
    public function sendEmailVerification(User $user): void
    {
        $user->notify(new CustomVerifyEmail());
    }

    /**
     * Отправка приветственного письма после успешного подтверждения email.
     * Вызывается из SendWelcomeAfterVerificationJob.
     */
    public function sendWelcome(User $user): void
    {
        $user->notify(new WelcomeNotification());
    }
}
