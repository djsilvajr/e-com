<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\LoginController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\Storefront\StorefrontHomeController;
use App\Http\Controllers\Web\Admin\AdminLoginController;
use App\Http\Controllers\Web\Admin\AdminDashboardController;
use App\Http\Controllers\Web\Admin\AdminProductTypeController;
use App\Http\Controllers\Web\Admin\AdminProductController;
use App\Http\Controllers\Web\Admin\AdminProductVariantController;

/*
|--------------------------------------------------------------------------
| Web middleware (session + csrf + errors) without forced auth.
| Used by public pages that still need session-backed features such as
| Auth::check() in the navbar and CSRF on forms.
|--------------------------------------------------------------------------
*/
$webPublic = [
    \Illuminate\Session\Middleware\StartSession::class,
    \Illuminate\View\Middleware\ShareErrorsFromSession::class,
    \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
    \Illuminate\Routing\Middleware\SubstituteBindings::class,
];

/*
|--------------------------------------------------------------------------
| Storefront ("/")
|--------------------------------------------------------------------------
*/
Route::middleware($webPublic)->group(function () {
    Route::get('/', [StorefrontHomeController::class, 'index'])->name('welcome');
});

/*
|--------------------------------------------------------------------------
| Legacy / base web auth (kept for compatibility)
|--------------------------------------------------------------------------
*/
Route::middleware($webPublic)->group(function () {
    Route::get('/login', [LoginController::class, 'loginView'])->name('login');
    Route::post('/login', [LoginController::class, 'loginAttempt'])->name('login.attempt');
});

Route::middleware('web.stack')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('auth.logout');
    Route::get('/home', [HomeController::class, 'homeView'])->name('home');
});

/*
|--------------------------------------------------------------------------
| Admin ("/admin") — session guard ("web")
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->group(function () use ($webPublic) {
    /*
    | Public admin routes (login) — NO admin permission check.
    | Adding `web.permission:admin` here would block the very login page.
    */
    Route::middleware($webPublic)->group(function () {
        Route::get('/login', [AdminLoginController::class, 'loginView'])->name('admin.login');
        Route::post('/login', [AdminLoginController::class, 'loginAttempt'])->name('admin.login.attempt');
    });

    /*
    | Protected admin routes — require an authenticated user on the "web"
    | guard AND the "admin" role (enforced by web.permission:admin).
    | If the user is not authenticated they are redirected to admin.login;
    | if they are authenticated but not an admin they receive HTTP 403.
    */
    Route::middleware(['web.stack', 'web.permission:admin'])->group(function () {
        Route::post('/logout', [AdminLoginController::class, 'logout'])->name('admin.logout');
        Route::get('/', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

        /*
        | Product Types administration
        */
        Route::prefix('product')->group(function () {
            Route::get('/types', [AdminProductTypeController::class, 'index'])->name('admin.types.index');
            Route::get('/types/{id}', [AdminProductTypeController::class, 'show'])
                ->whereNumber('id')
                ->name('admin.types.show');
            Route::post('/types/{id}/child', [AdminProductTypeController::class, 'store'])
                ->whereNumber('id')
                ->name('admin.types.store');
            Route::patch('/types/{id}/status', [AdminProductTypeController::class, 'toggleStatus'])
                ->whereNumber('id')
                ->name('admin.types.toggle-status');
            Route::delete('/types/{id}', [AdminProductTypeController::class, 'destroy'])
                ->whereNumber('id')
                ->name('admin.types.destroy');
        });

        /*
        | Products administration
        */
        Route::prefix('products')->group(function () {
            Route::get('/', [AdminProductController::class, 'index'])->name('admin.products.index');
            Route::get('/create', [AdminProductController::class, 'create'])->name('admin.products.create');
            Route::post('/', [AdminProductController::class, 'store'])->name('admin.products.store');
            Route::get('/{id}/edit', [AdminProductController::class, 'edit'])
                ->whereNumber('id')
                ->name('admin.products.edit');
            Route::put('/{id}', [AdminProductController::class, 'update'])
                ->whereNumber('id')
                ->name('admin.products.update');
            Route::delete('/{id}', [AdminProductController::class, 'destroy'])
                ->whereNumber('id')
                ->name('admin.products.destroy');

            /*
            | Variants of a product
            */
            Route::prefix('{product_id}/variants')->whereNumber('product_id')->group(function () {
                Route::get('/', [AdminProductVariantController::class, 'index'])
                    ->name('admin.products.variants.index');
                Route::get('/create', [AdminProductVariantController::class, 'create'])
                    ->name('admin.products.variants.create');
                Route::post('/', [AdminProductVariantController::class, 'store'])
                    ->name('admin.products.variants.store');
                Route::get('/{id}/edit', [AdminProductVariantController::class, 'edit'])
                    ->whereNumber('id')
                    ->name('admin.products.variants.edit');
                Route::put('/{id}', [AdminProductVariantController::class, 'update'])
                    ->whereNumber('id')
                    ->name('admin.products.variants.update');
                Route::delete('/{id}', [AdminProductVariantController::class, 'destroy'])
                    ->whereNumber('id')
                    ->name('admin.products.variants.destroy');
            });
        });
    });
});
