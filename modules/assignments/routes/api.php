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

// Phase 6c: the Customer tier. assignments.view lets a student see the
// assignment list (nothing in it is "owned" by one student over
// another, so my-assignments reuses the same query as the Staff list,
// just under a distinct route — URI reuse would let the first-matched
// .manage route always win) and submit and view only their own
// submissions.
Route::middleware(['module.active:assignments', 'capability:assignments.view'])
    ->prefix('modules/assignments')
    ->group(function () {
        Route::get('my-assignments', [AssignmentController::class, 'index']);
        Route::post('assignments/{assignment}/my-submission', [SubmissionController::class, 'storeMine']);
        Route::get('my-submissions', [SubmissionController::class, 'mine']);
    });
