<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

use App\Http\Middleware\TraceRequests;
use App\Http\Middleware\ApiAuthenticate;
use App\Http\Middleware\WebAuthenticate;
use App\Http\Middleware\CheckUserPermission;
use App\Http\Middleware\CheckWebPermission;
use GPBMetadata\Opentelemetry\Proto\Trace\V1\Trace;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'api.auth' => ApiAuthenticate::class,
            'web.auth' => WebAuthenticate::class,
            'api.permission' => CheckUserPermission::class,
            'web.permission' => CheckWebPermission::class,
            'trace.request' => TraceRequests::class,
        ]);

        $middleware->group('api.stack', [
            'api.auth',
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            'trace.request'
        ]);

        $middleware->group('web.stack', [
            \Illuminate\Session\Middleware\StartSession::class,
            'web.auth:web',
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,    
        ]);
        
        //$middleware->append(TraceRequests::class);
    })->withProviders([
        \App\Providers\TelemetrySdkServiceProvider::class,
        \App\Providers\AppServiceProvider::class,
    ])
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();