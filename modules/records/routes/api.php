<?php

use Illuminate\Support\Facades\Route;
use Moduloom\Records\Http\Controllers\PublicRecordController;

Route::middleware(['module.active:records'])
    ->prefix('modules/records')
    ->group(function () {
        Route::get('records', [PublicRecordController::class, 'index']);
        Route::post('records', [PublicRecordController::class, 'store']);
        Route::delete('records/{publicRecord}', [PublicRecordController::class, 'destroy']);
    });
