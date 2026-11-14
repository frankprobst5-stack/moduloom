<?php

use App\Models\ProfileInstallHistory;
use Illuminate\Support\Facades\Route;

// Never a generic placeholder: a fresh instance goes to the setup
// wizard, one that's already installed something goes to its own
// dashboard.
Route::get('/', function () {
    return ProfileInstallHistory::query()->exists()
        ? redirect('/dashboard')
        : redirect('/setup');
});

Route::get('/setup', fn () => view('setup.picker'));
Route::get('/setup/onboarding', fn () => view('setup.onboarding'));
Route::get('/dashboard', fn () => view('dashboard'));
Route::get('/marketplace', fn () => view('marketplace'));
