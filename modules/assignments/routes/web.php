<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'module.active:assignments'])
    ->get('/admin/assignments', fn () => view('assignments::admin'));
