<?php

declare(strict_types=1);

namespace App\Repository\Contracts;

interface WelcomeMailerInterface
{
    public function sendWelcome(string $email, string $name): void;
}
