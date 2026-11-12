<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'module.active:personal-databases'])
    ->get('/admin/personal-databases', fn () => view('personal-databases::admin'));
