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

// Phase 6c: the Customer tier. applications.view lets an applicant
// submit and see only their own applications, never the full list.
Route::middleware(['module.active:applications', 'capability:applications.view'])
    ->prefix('modules/applications')
    ->group(function () {
        Route::get('my-applications', [ApplicationController::class, 'mine']);
        Route::post('my-applications', [ApplicationController::class, 'storeMine']);
    });
