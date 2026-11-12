<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'module.active:forums'])
    ->get('/admin/forums', fn () => view('forums::admin'));
