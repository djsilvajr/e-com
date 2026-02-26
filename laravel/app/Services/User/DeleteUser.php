<?php

namespace App\Services\User;

use App\Repository\Contracts\UserRepositoryInterface;
use App\Exceptions\PersistenceErrorException;
use App\Services\User\Ensures\EnsureUserExist;

class DeleteUser
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private EnsureUserExist $ensureUserExists
    ) {}

    public function execute(int $id): bool
    {
        $this->ensureUserExists->validate($id);

        if (!$this->userRepository->deleteUserById($id)) {
            throw new PersistenceErrorException();
        }

        return true;
    }
}
