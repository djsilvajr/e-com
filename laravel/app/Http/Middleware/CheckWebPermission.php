<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckWebPermission
{
    /**
     * Web (session-guard) equivalent of CheckUserPermission.
     *
     * Mirrors the API middleware (api.permission:user.delete) but runs on the
     * "web" guard used by the Storefront and Admin areas. The {permission}
     * parameter is matched against:
     *   - the user's roles    (e.g. "admin", "manager", "user")
     *   - the user's permissions via roles (e.g. "user.delete")
     *
     * Usage:
     *   Route::middleware('web.permission:admin')->group(function () { ... });
     *
     * Behaviour:
     *   - Not authenticated on the "web" guard -> redirect to admin.login
     *   - Authenticated but missing the required role/permission -> HTTP 403
     */
    public function handle($request, Closure $next, $permission)
    {
        /** @var \App\Models\AuthModel|null $user */
        $user = Auth::guard('web')->user();

        if (!$user) {
            return redirect()->route('admin.login');
        }

        $hasRole = $user->roles->contains('name', $permission);
        $hasPermission = $hasRole ? true : $user->hasPermission($permission);

        if (!$hasRole && !$hasPermission) {
            abort(403, 'Acesso negado. Permissão necessária: ' . $permission);
        }

        return $next($request);
    }
}
