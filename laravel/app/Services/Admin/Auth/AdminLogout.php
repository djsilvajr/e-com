<?php

namespace App\Services\Admin\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminLogout
{
    public function execute(Request $request): bool
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return true;
    }
}
