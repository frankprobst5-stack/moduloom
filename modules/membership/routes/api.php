<?php

use Illuminate\Support\Facades\Route;
use Moduloom\Membership\Http\Controllers\MemberController;

Route::middleware(['module.active:membership', 'capability:membership.manage'])
    ->prefix('modules/membership')
    ->group(function () {
        Route::get('members', [MemberController::class, 'index']);
        Route::post('members', [MemberController::class, 'store']);
        Route::patch('members/{member}', [MemberController::class, 'update']);
        Route::delete('members/{member}', [MemberController::class, 'destroy']);
    });
