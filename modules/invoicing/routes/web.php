<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'module.active:invoicing'])
    ->get('/admin/invoicing', fn () => view('invoicing::admin'));
