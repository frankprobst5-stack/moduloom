<?php

use Illuminate\Support\Facades\Route;
use Moduloom\Invoicing\Http\Controllers\InvoiceController;

Route::middleware(['module.active:invoicing', 'capability:invoicing.manage'])
    ->prefix('modules/invoicing')
    ->group(function () {
        Route::get('invoices', [InvoiceController::class, 'index']);
        Route::post('invoices', [InvoiceController::class, 'store']);
        Route::patch('invoices/{invoice}', [InvoiceController::class, 'update']);
        Route::delete('invoices/{invoice}', [InvoiceController::class, 'destroy']);
    });

// Phase 6c: the Customer tier. invoicing.view only ever sees the
// authenticated user's own invoices, never the full list .manage gets.
Route::middleware(['module.active:invoicing', 'capability:invoicing.view'])
    ->prefix('modules/invoicing')
    ->group(function () {
        Route::get('my-invoices', [InvoiceController::class, 'mine']);
    });
