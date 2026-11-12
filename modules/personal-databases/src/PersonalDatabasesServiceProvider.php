<?php

namespace Moduloom\PersonalDatabases;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class PersonalDatabasesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // See Moduloom\Notes\NotesServiceProvider — migrations are run
        // once, explicitly, by ModuleInstaller at first install.
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'personal-databases');

        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        Route::prefix('api')->middleware('api')->group(function (): void {
            $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
        });
    }
}
