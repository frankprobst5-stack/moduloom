<?php

namespace Moduloom\Website;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class WebsiteServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // See Moduloom\Notes\NotesServiceProvider — migrations are run
        // once, explicitly, by ModuleInstaller at first install.
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'website');

        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        Route::prefix('api')->middleware('api')->group(function (): void {
            $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
        });
    }
}
