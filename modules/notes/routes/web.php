<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'module.active:notes'])
    ->get('/admin/notes', fn () => view('notes::admin'));
