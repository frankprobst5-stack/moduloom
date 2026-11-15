<?php

use Illuminate\Support\Facades\Route;
use Moduloom\Forms\Http\Controllers\FormSubmissionController;
use Moduloom\Forms\Http\Controllers\FormTemplateController;

Route::middleware(['module.active:forms', 'capability:forms.manage'])
    ->prefix('modules/forms')
    ->group(function () {
        Route::get('templates', [FormTemplateController::class, 'index']);
        Route::post('templates', [FormTemplateController::class, 'store']);
        Route::delete('templates/{formTemplate}', [FormTemplateController::class, 'destroy']);
        Route::get('templates/{formTemplate}/submissions', [FormSubmissionController::class, 'index']);
        Route::post('templates/{formTemplate}/submissions', [FormSubmissionController::class, 'store']);
    });
