<?php

use Illuminate\Support\Facades\Route;
use Moduloom\PublicInformation\Http\Controllers\PublicNoticesController;

Route::middleware(['web', 'module.active:public-information'])->group(function () {
    Route::get('/admin/public-information', fn () => view('public-information::admin'));
    Route::get('/notices', [PublicNoticesController::class, 'index']);
});
