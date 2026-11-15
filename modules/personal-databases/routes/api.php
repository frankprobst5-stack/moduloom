<?php

use Illuminate\Support\Facades\Route;
use Moduloom\PersonalDatabases\Http\Controllers\CollectionController;
use Moduloom\PersonalDatabases\Http\Controllers\RecordController;

Route::middleware(['module.active:personal-databases', 'capability:personal_databases.manage'])
    ->prefix('modules/personal-databases')
    ->group(function () {
        Route::get('collections', [CollectionController::class, 'index']);
        Route::post('collections', [CollectionController::class, 'store']);
        Route::delete('collections/{collection}', [CollectionController::class, 'destroy']);

        Route::get('collections/{collection}/records', [RecordController::class, 'index']);
        Route::post('collections/{collection}/records', [RecordController::class, 'store']);
        Route::delete('records/{record}', [RecordController::class, 'destroy']);
    });
