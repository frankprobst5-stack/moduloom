<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ModuleMarketplaceController;
use App\Http\Controllers\SetupController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\ThemeController;
use Illuminate\Support\Facades\Route;

// App-shell level routes (setup wizard, dashboard nav) live here.
// Each module registers its own API routes via its own service
// provider's loadRoutesFrom() call.
Route::prefix('setup')->group(function () {
    Route::get('profiles', [SetupController::class, 'profiles']);
    Route::post('install/{slug}', [SetupController::class, 'install'])->middleware('capability:instance.manage');
});

Route::prefix('dashboard')->group(function () {
    Route::get('nav', [DashboardController::class, 'nav']);
    Route::get('latest-install', [DashboardController::class, 'latestInstall']);
});

Route::prefix('theme')->group(function () {
    Route::get('/', [ThemeController::class, 'show']);
    Route::post('activate', [ThemeController::class, 'activate']);
});

Route::prefix('marketplace')->group(function () {
    Route::get('modules', [ModuleMarketplaceController::class, 'index']);
    Route::post('install/{slug}', [ModuleMarketplaceController::class, 'install'])->middleware('capability:instance.manage');
    Route::post('uninstall/{slug}', [ModuleMarketplaceController::class, 'uninstall'])->middleware('capability:instance.manage');
});

Route::prefix('team')->middleware('capability:instance.manage')->group(function () {
    Route::get('/', [TeamController::class, 'index']);
    Route::post('/', [TeamController::class, 'store']);
});
