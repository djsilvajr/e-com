<?php

declare(strict_types=1);

namespace App\Services\User;

use App\Events\User\UserRegisteredSendEmailEvent;
use App\Exceptions\PersistenceErrorException;
use App\Repository\Contracts\FeatureFlagInterface;
use App\Repository\Contracts\UserRepositoryInterface;
use App\Services\User\Ensures\EnsureEmailIsAvailable;

class InsertUser
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private FeatureFlagInterface $featureFlagRepository,
        private EnsureEmailIsAvailable $ensureEmailIsAvailable,
    ) {}

    /**
     * @param  array<string, mixed>  $credentials
     * @return array<string, mixed>
     */
    public function execute(array $credentials): array
    {
        $this->ensureEmailIsAvailable->validate($credentials['email']);

        $addition = $this->userRepository->insertUser($credentials);
        $userId   = $addition['id'] ?? null;

        if (!$userId) {
            throw new PersistenceErrorException();
        }

        if (!$this->userRepository->addUserRole($userId, 3)) {
            throw new PersistenceErrorException();
        }

        if ($this->featureFlagRepository->isEnabled('email_send_enabled')) {
            event(new UserRegisteredSendEmailEvent($userId, $addition['email'], $addition['name']));
        }

        return [
            'id'    => $userId,
            'name'  => $addition['name'],
            'email' => $addition['email'],
        ];
    }
}
