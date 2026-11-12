<?php

use Illuminate\Support\Facades\Route;
use Moduloom\PublicInformation\Http\Controllers\NoticeController;

Route::middleware(['module.active:public-information'])
    ->prefix('modules/public-information')
    ->group(function () {
        Route::get('notices', [NoticeController::class, 'index']);
        Route::post('notices', [NoticeController::class, 'store']);
        Route::delete('notices/{notice}', [NoticeController::class, 'destroy']);
    });
