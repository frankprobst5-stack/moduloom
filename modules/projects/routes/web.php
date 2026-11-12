<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'module.active:projects'])
    ->get('/admin/projects', fn () => view('projects::admin'));
