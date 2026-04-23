<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Services\Admin\Auth\AdminLogin;
use App\Services\Admin\Auth\AdminLogout;

class AdminAuthService
{
    public function __construct(
        private AdminLogin $adminLogin,
        private AdminLogout $adminLogout,
    ) {}

    public function login(Request $request, array $credentials, bool $remember = false): array
    {
        return $this->adminLogin->execute($request, $credentials, $remember);
    }

    public function logout(Request $request): bool
    {
        return $this->adminLogout->execute($request);
    }
}
