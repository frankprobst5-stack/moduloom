<?php

use Illuminate\Support\Facades\Route;
use Moduloom\Applications\Http\Controllers\ApplicationController;

Route::middleware(['module.active:applications', 'capability:applications.manage'])
    ->prefix('modules/applications')
    ->group(function () {
        Route::get('applications', [ApplicationController::class, 'index']);
        Route::post('applications', [ApplicationController::class, 'store']);
        Route::patch('applications/{application}', [ApplicationController::class, 'update']);
    });
