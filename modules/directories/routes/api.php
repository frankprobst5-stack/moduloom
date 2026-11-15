<?php

use Illuminate\Support\Facades\Route;
use Moduloom\Directories\Http\Controllers\DirectoryEntryController;

Route::middleware(['module.active:directories', 'capability:directories.manage'])
    ->prefix('modules/directories')
    ->group(function () {
        Route::get('entries', [DirectoryEntryController::class, 'index']);
        Route::post('entries', [DirectoryEntryController::class, 'store']);
        Route::delete('entries/{directoryEntry}', [DirectoryEntryController::class, 'destroy']);
    });
