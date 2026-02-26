<?php

namespace App\Services\User;

use App\Repository\Contracts\UserRepositoryInterface;
use App\Exceptions\PersistenceErrorException;
use App\Services\User\Ensures\EnsureUserExist;
use App\Services\User\Ensures\EnsureNewEmailIsAvailable;

class UpdateUser
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private EnsureUserExist $ensureUserExist,
        private EnsureNewEmailIsAvailable $ensureNewEmailIsAvailable
    ) {}

    public function execute(array $credentials): array
    {
        $this->ensureUserExist->validate($credentials['id']);

        $user = $this->userRepository->getUserById($credentials['id']);
        $this->ensureNewEmailIsAvailable->validate(
            array(
                'id' => $credentials['id'],
                'newEmail' => $credentials['email'],
                'oldEmail' => $user['email']
            )
        );

        if (
            !$this->userRepository->updateUser(
                $credentials['id'],
                $credentials['name'],
                $credentials['email']
            )
        ) {
            throw new PersistenceErrorException();
        }

        return $credentials;
    }
}
