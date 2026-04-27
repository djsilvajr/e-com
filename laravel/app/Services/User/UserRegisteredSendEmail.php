<?php

declare(strict_types=1);

namespace App\Services\User;

use App\Exceptions\InvalidParametersException;
use App\Repository\Contracts\FeatureFlagInterface;
use App\Repository\Contracts\WelcomeMailerInterface;

class UserRegisteredSendEmail
{
    private const FEATURE_FLAG_KEY = 'email_send_enabled';

    public function __construct(
        private WelcomeMailerInterface $welcomeMailer,
        private FeatureFlagInterface $featureFlagRepository,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function execute(array $data): bool
    {
        $email = isset($data['email']) ? (string) $data['email'] : '';
        $name  = isset($data['name'])  ? (string) $data['name']  : '';

        if ($email === '' || $name === '') {
            throw new InvalidParametersException(
                'email and name are required',
                [
                    'email' => 'email is required',
                    'name'  => 'name is required',
                ],
            );
        }

        if (!$this->featureFlagRepository->isEnabled(self::FEATURE_FLAG_KEY)) {
            return false;
        }

        $this->welcomeMailer->sendWelcome($email, $name);

        return true;
    }
}
