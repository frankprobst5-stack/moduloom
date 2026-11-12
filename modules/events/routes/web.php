<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'module.active:events'])
    ->get('/admin/events', fn () => view('events::admin'));
