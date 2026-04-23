<?php

namespace App\Services\Admin\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\InvalidCredentialsException;

class AdminLogin
{
    public function execute(Request $request, array $credentials, bool $remember = false): array
    {
        $email = $credentials['email'] ?? '';
        $password = $credentials['password'] ?? '';

        $attempt = Auth::guard('web')->attempt([
            'email'    => $email,
            'password' => $password,
        ], $remember);

        if (!$attempt) {
            throw new InvalidCredentialsException('Credenciais inválidas.');
        }

        $request->session()->regenerate();

        $user = Auth::guard('web')->user();

        return [
            'id'    => $user->id ?? null,
            'name'  => $user->name ?? null,
            'email' => $user->email ?? null,
        ];
    }
}
