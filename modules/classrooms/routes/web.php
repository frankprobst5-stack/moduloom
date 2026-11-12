<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'module.active:classrooms'])
    ->get('/admin/classrooms', fn () => view('classrooms::admin'));
