<?php

namespace App\Http\Controllers\Web\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminLoginRequest;
use App\Services\AdminAuthService;
use App\Exceptions\InvalidCredentialsException;

class AdminLoginController extends Controller
{
    public function __construct(
        private AdminAuthService $adminAuthService,
    ) {}

    public function loginView()
    {
        if (Auth::guard('web')->check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.login');
    }

    public function loginAttempt(AdminLoginRequest $request)
    {
        try {
            $this->adminAuthService->login(
                $request,
                [
                    'email'    => $request->input('email', ''),
                    'password' => $request->input('password', ''),
                ],
                (bool) $request->boolean('remember')
            );
        } catch (InvalidCredentialsException $e) {
            return view('admin.login', ['error' => 'Credenciais inválidas.']);
        }

        return redirect()->route('admin.dashboard');
    }

    public function logout(Request $request)
    {
        $this->adminAuthService->logout($request);

        return redirect()->route('admin.login');
    }
}
