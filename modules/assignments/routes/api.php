<?php

use Illuminate\Support\Facades\Route;
use Moduloom\Assignments\Http\Controllers\AssignmentController;
use Moduloom\Assignments\Http\Controllers\SubmissionController;

Route::middleware(['module.active:assignments', 'capability:assignments.manage'])
    ->prefix('modules/assignments')
    ->group(function () {
        Route::get('assignments', [AssignmentController::class, 'index']);
        Route::post('assignments', [AssignmentController::class, 'store']);
        Route::delete('assignments/{assignment}', [AssignmentController::class, 'destroy']);
        Route::post('assignments/{assignment}/submissions', [SubmissionController::class, 'store']);
    });
