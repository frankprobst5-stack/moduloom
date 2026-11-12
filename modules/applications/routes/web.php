<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'module.active:applications'])
    ->get('/admin/applications', fn () => view('applications::admin'));
