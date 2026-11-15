<?php

use App\Http\Middleware\EnsureModuleIsActive;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'module.active' => EnsureModuleIsActive::class,
        ]);

        // Phase 6a: every API route — routes/api.php and every module's
        // own api.php, which all join this same named group via their
        // own `->middleware('api')` call — now requires an authenticated
        // session. Login/register/logout are plain web routes, so this
        // never has to carve out an exception for them.
        //
        // The 'api' group has no session-reading middleware by default
        // (it's stateless out of the box — that's what Sanctum's
        // EnsureFrontendRequestsAreStateful normally adds). Since this
        // app's React screens call /api/* same-origin with the session
        // cookie already set by login, prepending the cookie/session
        // middleware here gets the same effect without pulling in
        // Sanctum for a single-client app that doesn't need tokens.
        $middleware->api(
            prepend: [
                \Illuminate\Cookie\Middleware\EncryptCookies::class,
                \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
                \Illuminate\Session\Middleware\StartSession::class,
            ],
            append: [
                \Illuminate\Auth\Middleware\Authenticate::class,
            ],
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
