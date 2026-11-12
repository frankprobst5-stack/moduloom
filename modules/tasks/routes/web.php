<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'module.active:tasks'])
    ->get('/admin/tasks', fn () => view('tasks::admin'));
