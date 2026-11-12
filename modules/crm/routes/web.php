<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'module.active:crm'])
    ->get('/admin/crm', fn () => view('crm::admin'));
