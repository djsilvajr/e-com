<?php
namespace App\Services\User;

use App\Events\UserRegisteredSendEmail;
use App\Repository\Contracts\UserRepositoryInterface;
use App\Repository\Contracts\FeatureFlagInterface;
use App\Exceptions\PersistenceErrorException;
use App\Services\User\Ensures\EnsureEmailIsAvailable;

class InsertUser
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private FeatureFlagInterface $featureFlagRepository,
        private EnsureEmailIsAvailable $ensureEmailIsAvailable
    ) {}

    public function execute(array $credentials): array
    {
        $this->ensureEmailIsAvailable->validate($credentials['email']);

        $addition = $this->userRepository->insertUser($credentials);
        $userId = $addition['id'] ?? null;

        if (!$userId) {
            throw new PersistenceErrorException();
        }

        if (!$this->userRepository->addUserRole($userId, 3)) {
            throw new PersistenceErrorException();
        }

        if ($this->featureFlagRepository->isEnabled('email_send_enabled')) {
            event(new UserRegisteredSendEmail($userId, $addition['email'], $addition['name']));
        }

        return [
            'id' => $userId,
            'name' => $addition['name'],
            'email' => $addition['email']
        ];
    }
}
