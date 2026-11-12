<?php

namespace Moduloom\Notes;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class NotesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Deliberately does NOT call loadMigrationsFrom(). This module's
        // migrations are run exactly once, explicitly, by
        // App\Kernel\ModuleInstaller at first-install time — never by a
        // blanket `artisan migrate`. That's what keeps the kernel's
        // bookkeeping and the module's real schema from ever drifting
        // out of sync with each other.
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'notes');

        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        // loadRoutesFrom() alone skips the "api" prefix and middleware
        // group that routes/api.php gets automatically when declared
        // through bootstrap/app.php's own withRouting(api: ...) — a
        // module's routes are loaded from its own service provider
        // instead, so it has to apply that convention itself.
        Route::prefix('api')->middleware('api')->group(function (): void {
            $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
        });
    }
}
