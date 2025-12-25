<?php

use Illuminate\Support\Facades\Route;
use Modules\Supplier\app\Http\Controllers\SupplierController;
use Modules\Supplier\app\Http\Controllers\SupplierGroupController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['as' => 'admin.', 'prefix' => 'admin'], function () {

    Route::get('suppliers/import', [SupplierController::class, 'bulkImport'])->name('suppliers.import');
    Route::post('suppliers/import', [SupplierController::class, 'bulkImportStore'])->name('suppliers.import.store');

    Route::delete('supplier/due-receive/delete/{id}', [SupplierController::class, 'dueReceiveDelete'])->name('supplier.due-receive.delete');
    Route::resource('suppliers', SupplierController::class)->except(['show']);

    Route::post('suppliers/status/{id}', [SupplierController::class, 'changeStatus'])->name('suppliers.status');
    Route::get('suppliers/due-pay/{id}', [SupplierController::class, 'duePay'])->name('suppliers.due-pay');
    Route::post('suppliers/due-pay-store/{id}', [SupplierController::class, 'duePayStore'])->name('suppliers.due-pay-store');
    Route::get('suppliers/advance/{id}', [SupplierController::class, 'advance'])->name('suppliers.advance');
    Route::post('suppliers/advance-store/{id}', [SupplierController::class, 'advanceStore'])->name('supplier.advance.pay');

    Route::get('suppliers/ledger/{id}', [SupplierController::class, 'ledger'])->name('suppliers.ledger');
    Route::get('suppliers/ledger-details/{id}', [SupplierController::class, 'ledgerDetails'])->name('suppliers.ledger-details');
    Route::get('suppliers/due-pay-history', [SupplierController::class, 'duePayHistory'])->name('suppliers.due-pay-history');
    Route::resource('supplierGroup', SupplierGroupController::class);
});
