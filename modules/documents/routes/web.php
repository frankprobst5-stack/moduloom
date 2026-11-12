<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'module.active:documents'])
    ->get('/admin/documents', fn () => view('documents::admin'));
