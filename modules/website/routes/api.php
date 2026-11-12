<?php

use Illuminate\Support\Facades\Route;
use Moduloom\Website\Http\Controllers\PageController;

Route::middleware(['module.active:website'])
    ->prefix('modules/website')
    ->group(function () {
        Route::get('pages', [PageController::class, 'index']);
        Route::post('pages', [PageController::class, 'store']);
        Route::patch('pages/{page}', [PageController::class, 'update']);
        Route::delete('pages/{page}', [PageController::class, 'destroy']);
    });
