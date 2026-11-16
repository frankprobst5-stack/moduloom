<?php

use App\Http\Controllers\AuthController;
use App\Models\ProfileInstallHistory;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Never a generic placeholder. A totally fresh instance (no account
// exists yet) goes to registration; an instance with an account but no
// active session goes to login; from there, a fresh profile goes to the
// setup wizard and an installed one goes to its own dashboard.
Route::get('/', function () {
    if (! User::query()->exists()) {
        return redirect('/register');
    }

    if (! Auth::check()) {
        return redirect('/login');
    }

    return ProfileInstallHistory::query()->exists()
        ? redirect('/dashboard')
        : redirect('/setup');
});

Route::get('/register', [AuthController::class, 'showRegister']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/setup', fn () => view('setup.picker'));
    Route::get('/setup/onboarding', fn () => view('setup.onboarding'));
    Route::get('/dashboard', fn () => view('dashboard'));
    Route::get('/marketplace', fn () => view('marketplace'));
    Route::get('/team', fn () => view('team'));
});
