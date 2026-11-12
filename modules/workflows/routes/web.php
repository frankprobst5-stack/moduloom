<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'module.active:workflows'])
    ->get('/admin/workflows', fn () => view('workflows::admin'));
