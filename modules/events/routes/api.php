<?php

use Illuminate\Support\Facades\Route;
use Moduloom\Events\Http\Controllers\EventController;
use Moduloom\Events\Http\Controllers\RegistrationController;

Route::middleware(['module.active:events', 'capability:events.manage'])
    ->prefix('modules/events')
    ->group(function () {
        Route::get('events', [EventController::class, 'index']);
        Route::post('events', [EventController::class, 'store']);
        Route::delete('events/{event}', [EventController::class, 'destroy']);
        Route::post('events/{event}/registrations', [RegistrationController::class, 'store']);
    });
