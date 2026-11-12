<?php

use Illuminate\Support\Facades\Route;
use Moduloom\Forums\Http\Controllers\ReplyController;
use Moduloom\Forums\Http\Controllers\ThreadController;

Route::middleware(['module.active:forums'])
    ->prefix('modules/forums')
    ->group(function () {
        Route::get('threads', [ThreadController::class, 'index']);
        Route::post('threads', [ThreadController::class, 'store']);
        Route::get('threads/{thread}', [ThreadController::class, 'show']);
        Route::delete('threads/{thread}', [ThreadController::class, 'destroy']);
        Route::post('threads/{thread}/replies', [ReplyController::class, 'store']);
    });
