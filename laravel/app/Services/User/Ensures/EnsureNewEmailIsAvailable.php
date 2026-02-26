<?php

namespace App\Services\User\Ensures;

use App\Repository\Contracts\UserRepositoryInterface;
use App\Exceptions\DuplicatedValueException;

class EnsureNewEmailIsAvailable
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {}

    public function validate(array $data): void
    {
        $id = $data['id'];
        $newEmail = $data['newEmail'];
        $oldEmail = $data['oldEmail'];

        if ($newEmail === $oldEmail) {
            return;
        }

        $exists = $this->userRepository
            ->verifyNewEmailIsAvailable($oldEmail, $newEmail, $id);

        if ($exists) {
            throw new DuplicatedValueException(
                "Invalid request.",
                ['Email already registered.']
            );
        }
    }
}
