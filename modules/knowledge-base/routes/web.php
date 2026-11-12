<?php

use Illuminate\Support\Facades\Route;
use Moduloom\KnowledgeBase\Http\Controllers\PublicArticleController;

Route::middleware(['web', 'module.active:knowledge-base'])->group(function () {
    Route::get('/admin/knowledge-base', fn () => view('knowledge-base::admin'));
    Route::get('/kb/{slug}', [PublicArticleController::class, 'show']);
});
