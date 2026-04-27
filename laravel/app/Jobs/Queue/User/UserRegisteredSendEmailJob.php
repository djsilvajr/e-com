<?php

declare(strict_types=1);

namespace App\Jobs\Queue\User;

use App\Services\User\UserRegisteredSendEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class UserRegisteredSendEmailJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 5;
    public int $timeout = 10;

    public function __construct(
        private int $id,
        private string $email,
        private string $name,
    ) {
        $this->onQueue('emails');
    }

    /**
     * @return array<int, int>
     */
    public function backoff(): array
    {
        return [10, 30, 60];
    }

    public function handle(UserRegisteredSendEmail $userRegisteredSendEmail): void
    {
        try {
            $userRegisteredSendEmail->execute([
                'id'    => $this->id,
                'email' => $this->email,
                'name'  => $this->name,
            ]);
        } catch (\Throwable $e) {
            Log::error('Error sending welcome email', [
                'job'   => self::class,
                'email' => $this->email,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
