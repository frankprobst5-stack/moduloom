<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'module.active:records'])
    ->get('/admin/records', fn () => view('records::admin'));
