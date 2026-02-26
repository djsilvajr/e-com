<?php

namespace App\Services\User\Ensures;

use App\Repository\Contracts\UserRepositoryInterface;
use App\Exceptions\ResourceNotFoundException;

class EnsureUserExist
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {}

    public function validate(int $id): array
    {
        $user = $this->userRepository->getUserById($id);

        if (!$user) {
            throw new ResourceNotFoundException(
                "User not found.",
                ['ID not identified.']
            );
        }

        return $user;
    }
}
