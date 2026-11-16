<?php

use Illuminate\Support\Facades\Route;
use Moduloom\Workflows\Http\Controllers\WorkflowRequestController;

Route::middleware(['module.active:workflows', 'capability:workflows.manage'])
    ->prefix('modules/workflows')
    ->group(function () {
        Route::get('requests', [WorkflowRequestController::class, 'index']);
        Route::post('requests', [WorkflowRequestController::class, 'store']);
        Route::patch('requests/{workflowRequest}', [WorkflowRequestController::class, 'update']);
    });

// Phase 6c: the Customer tier. workflows.view lets a requester submit
// and view only their own requests, never the full list.
Route::middleware(['module.active:workflows', 'capability:workflows.view'])
    ->prefix('modules/workflows')
    ->group(function () {
        Route::get('my-requests', [WorkflowRequestController::class, 'mine']);
        Route::post('my-requests', [WorkflowRequestController::class, 'storeMine']);
    });
