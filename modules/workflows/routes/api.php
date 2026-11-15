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
