<?php

use Illuminate\Support\Facades\Route;
use Moduloom\Crm\Http\Controllers\ContactController;

Route::middleware(['module.active:crm'])
    ->prefix('modules/crm')
    ->group(function () {
        Route::get('contacts', [ContactController::class, 'index']);
        Route::post('contacts', [ContactController::class, 'store']);
        Route::delete('contacts/{contact}', [ContactController::class, 'destroy']);
    });
