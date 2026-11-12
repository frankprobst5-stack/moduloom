<?php

use Illuminate\Support\Facades\Route;
use Moduloom\Classrooms\Http\Controllers\ClassroomController;
use Moduloom\Classrooms\Http\Controllers\StudentController;

Route::middleware(['module.active:classrooms'])
    ->prefix('modules/classrooms')
    ->group(function () {
        Route::get('classrooms', [ClassroomController::class, 'index']);
        Route::post('classrooms', [ClassroomController::class, 'store']);
        Route::delete('classrooms/{classroom}', [ClassroomController::class, 'destroy']);
        Route::post('classrooms/{classroom}/students', [StudentController::class, 'store']);
    });
