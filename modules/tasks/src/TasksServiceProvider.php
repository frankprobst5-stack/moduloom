<?php

namespace Moduloom\Tasks;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class TasksServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // See Moduloom\Notes\NotesServiceProvider for why migrations are
        // never auto-loaded here — ModuleInstaller runs them once,
        // explicitly, at first install.
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'tasks');

        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        Route::prefix('api')->middleware('api')->group(function (): void {
            $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
        });
    }
}
