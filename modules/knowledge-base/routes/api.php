<?php

use Illuminate\Support\Facades\Route;
use Moduloom\KnowledgeBase\Http\Controllers\ArticleController;

Route::middleware(['module.active:knowledge-base', 'capability:knowledge_base.manage'])
    ->prefix('modules/knowledge-base')
    ->group(function () {
        Route::get('articles', [ArticleController::class, 'index']);
        Route::post('articles', [ArticleController::class, 'store']);
        Route::delete('articles/{article}', [ArticleController::class, 'destroy']);
    });
