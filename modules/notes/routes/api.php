<?php

use Illuminate\Support\Facades\Route;
use Moduloom\Notes\Http\Controllers\NoteController;

Route::middleware(['module.active:notes'])
    ->prefix('modules/notes')
    ->group(function () {
        Route::get('notes', [NoteController::class, 'index']);
        Route::post('notes', [NoteController::class, 'store']);
        Route::patch('notes/{note}', [NoteController::class, 'update']);
        Route::delete('notes/{note}', [NoteController::class, 'destroy']);
    });
