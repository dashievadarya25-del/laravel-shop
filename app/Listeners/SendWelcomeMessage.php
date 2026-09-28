<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Jobs\SendWelcomeAfterVerificationJob;
use Illuminate\Auth\Events\Verified;

class SendWelcomeMessage
{
    /**
     * Обработка события успешной верификации email.
     */
    public function handle(Verified $event): void
    {
        // $event->user содержит объект пользователя, который подтвердил email
        SendWelcomeAfterVerificationJob::dispatch($event->user->id);
    }
}
