<?php

declare(strict_types=1);

namespace App\Repository;

use App\Mail\WelcomeMail;
use App\Repository\Contracts\WelcomeMailerInterface;
use Illuminate\Support\Facades\Mail;

class WelcomeMailerRepository implements WelcomeMailerInterface
{
    public function sendWelcome(string $email, string $name): void
    {
        Mail::to($email)->send(new WelcomeMail($name));
    }
}
