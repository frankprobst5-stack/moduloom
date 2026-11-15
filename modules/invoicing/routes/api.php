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
