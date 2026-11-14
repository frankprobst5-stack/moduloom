<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/setup', fn () => view('setup.picker'));
Route::get('/setup/onboarding', fn () => view('setup.onboarding'));
Route::get('/dashboard', fn () => view('dashboard'));
Route::get('/marketplace', fn () => view('marketplace'));
