<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'module.active:directories'])
    ->get('/admin/directories', fn () => view('directories::admin'));
