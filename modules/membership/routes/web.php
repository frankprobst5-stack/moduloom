<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'module.active:membership'])
    ->get('/admin/membership', fn () => view('membership::admin'));
