<?php

use Illuminate\Support\Facades\Route;
use Moduloom\Documents\Http\Controllers\DocumentController;

Route::middleware(['module.active:documents', 'capability:documents.manage'])
    ->prefix('modules/documents')
    ->group(function () {
        Route::get('documents', [DocumentController::class, 'index']);
        Route::post('documents', [DocumentController::class, 'store']);
        Route::delete('documents/{document}', [DocumentController::class, 'destroy']);
    });
