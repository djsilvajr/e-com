<?php

namespace App\Http\Controllers\Web;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;


class LoginController extends Controller
{
    public function loginView()
    {
        if (Auth::guard('web')->check()) {
            return redirect()->route('welcome');
        }

        return view('login/login');
    }

    public function loginAttempt(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:1', 'max:255'],
            'remember' => ['nullable', 'boolean'],
        ], [
            'email.required'    => 'E-mail é obrigatório.',
            'email.email'       => 'Informe um e-mail válido.',
            'password.required' => 'Senha é obrigatória.',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('login')
                ->withErrors($validator)
                ->withInput($request->only('email', 'remember'));
        }

        $credentials = [
            'email'    => (string) $request->input('email', ''),
            'password' => (string) $request->input('password', ''),
        ];

        $remember = (bool) $request->boolean('remember');

        if (Auth::guard('web')->attempt($credentials, $remember)) {
            $request->session()->regenerate();
            return redirect()->intended(route('welcome'));
        }

        return redirect()
            ->route('login')
            ->withInput($request->only('email', 'remember'))
            ->with('error', 'E-mail ou senha incorretos.');
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('welcome');
    }
}
