<?php

use Illuminate\Support\Facades\Route;
use Modules\Expense\app\Http\Controllers\ExpenseController;
use Modules\Expense\app\Http\Controllers\ExpenseTypeController;
use Modules\Expense\app\Http\Controllers\ExpenseSupplierController;

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

Route::group(['as' => 'admin.', 'prefix' => 'admin', 'middleware' => ['auth:admin', 'translation']], function () {
    Route::resource('expense', ExpenseController::class)->names('expense');
    Route::resource('expenseType', ExpenseTypeController::class)->names('expense.type');
    Route::get('expense-types/{id}/children', [ExpenseTypeController::class, 'getChildren'])
        ->name('expense.type.children');

    // Expense Suppliers
    Route::resource('expense-suppliers', ExpenseSupplierController::class)->names('expense-suppliers');
    Route::get('expense-suppliers/{id}/due-pay', [ExpenseSupplierController::class, 'duePay'])->name('expense-suppliers.due-pay');
    Route::post('expense-suppliers/{id}/due-pay', [ExpenseSupplierController::class, 'duePayStore'])->name('expense-suppliers.due-pay-store');
    Route::get('expense-suppliers-due-pay-history', [ExpenseSupplierController::class, 'duePayHistory'])->name('expense-suppliers.due-pay-history');
    Route::delete('expense-suppliers/due-pay/{id}', [ExpenseSupplierController::class, 'duePayDelete'])->name('expense-suppliers.due-pay-delete');
    Route::get('expense-suppliers/{id}/advance', [ExpenseSupplierController::class, 'advance'])->name('expense-suppliers.advance');
    Route::post('expense-suppliers/{id}/advance', [ExpenseSupplierController::class, 'advanceStore'])->name('expense-suppliers.advance-store');
    Route::get('expense-suppliers/{id}/ledger', [ExpenseSupplierController::class, 'ledger'])->name('expense-suppliers.ledger');
    Route::get('expense-suppliers/ledger-details/{id}', [ExpenseSupplierController::class, 'ledgerDetails'])->name('expense-suppliers.ledger-details');
    Route::get('expense-suppliers/{id}/status', [ExpenseSupplierController::class, 'changeStatus'])->name('expense-suppliers.status');
    Route::get('expense-suppliers-api/get-suppliers', [ExpenseSupplierController::class, 'getSuppliers'])->name('expense-suppliers.get-suppliers');
});
