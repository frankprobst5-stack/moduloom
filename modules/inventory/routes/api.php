<?php

use Illuminate\Support\Facades\Route;
use Moduloom\Inventory\Http\Controllers\InventoryItemController;

Route::middleware(['module.active:inventory'])
    ->prefix('modules/inventory')
    ->group(function () {
        Route::get('items', [InventoryItemController::class, 'index']);
        Route::post('items', [InventoryItemController::class, 'store']);
        Route::patch('items/{item}', [InventoryItemController::class, 'update']);
        Route::delete('items/{item}', [InventoryItemController::class, 'destroy']);
    });
