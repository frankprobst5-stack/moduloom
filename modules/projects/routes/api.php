<?php

use Illuminate\Support\Facades\Route;
use Moduloom\Projects\Http\Controllers\ProjectController;
use Moduloom\Projects\Http\Controllers\ProjectTaskController;

Route::middleware(['module.active:projects', 'capability:projects.manage'])
    ->prefix('modules/projects')
    ->group(function () {
        Route::get('projects', [ProjectController::class, 'index']);
        Route::post('projects', [ProjectController::class, 'store']);
        Route::delete('projects/{project}', [ProjectController::class, 'destroy']);
        Route::post('projects/{project}/tasks', [ProjectTaskController::class, 'store']);
    });
