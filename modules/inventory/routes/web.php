<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'module.active:inventory'])
    ->get('/admin/inventory', fn () => view('inventory::admin'));
