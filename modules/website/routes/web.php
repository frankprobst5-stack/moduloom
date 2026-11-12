<?php

use Illuminate\Support\Facades\Route;
use Moduloom\Website\Http\Controllers\PublicPageController;

Route::middleware(['web', 'module.active:website'])->group(function () {
    Route::get('/admin/website', fn () => view('website::admin'));
    Route::get('/site/{slug}', [PublicPageController::class, 'show']);
});
