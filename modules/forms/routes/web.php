<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'module.active:forms'])
    ->get('/admin/forms', fn () => view('forms::admin'));
