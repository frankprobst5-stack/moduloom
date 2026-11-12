<?php

use Illuminate\Support\Facades\Route;
use Moduloom\Files\Http\Controllers\FileController;

Route::middleware(['module.active:files'])
    ->prefix('modules/files')
    ->group(function () {
        Route::get('files', [FileController::class, 'index']);
        Route::post('files', [FileController::class, 'store']);
        Route::delete('files/{uploadedFile}', [FileController::class, 'destroy']);
    });
