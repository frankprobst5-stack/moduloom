<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'module.active:files'])
    ->get('/admin/files', fn () => view('files::admin'));
