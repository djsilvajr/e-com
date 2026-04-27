<?php

declare(strict_types=1);

namespace App\Listeners\User;

use App\Events\User\UserRegisteredSendEmailEvent;
use App\Jobs\Queue\User\UserRegisteredSendEmailJob;

class UserRegisteredSendEmailListener
{
    public function __construct() {}

    public function handle(UserRegisteredSendEmailEvent $event): void
    {
        UserRegisteredSendEmailJob::dispatch(
            $event->id,
            $event->email,
            $event->name,
        );
    }
}
