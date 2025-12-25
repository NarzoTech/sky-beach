<?php

use Illuminate\Support\Facades\Route;
use Modules\Customer\app\Http\Controllers\AreaController;
use Modules\Customer\app\Http\Controllers\CustomerController;
use Modules\Customer\app\Http\Controllers\CustomerGroupController;

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
    Route::get('customers/import', [CustomerController::class, 'bulkImport'])->name('customers.import');
    Route::post('customers/import', [CustomerController::class, 'bulkImportStore'])->name('customers.import.store');

    Route::get('customers/due-receive-list', [CustomerController::class, 'dueReceiveList'])->name('customers.due-receive.list');
    Route::get('customers/ledger/{id}', [CustomerController::class, 'ledger'])->name('customers.ledger');
    Route::get('customers/ledger-details/{id}', [CustomerController::class, 'ledgerDetails'])->name('customers.ledger-details');
    Route::get('customers/advance/{id}', [CustomerController::class, 'advance'])->name('customers.advance');
    Route::post('customers/advance-store/{id}', [CustomerController::class, 'advanceStore'])->name('customers.advance.pay');
    Route::post('customers/status/{id}', [CustomerController::class, 'changeStatus'])->name('customers.status');
    Route::delete('delete/all-customers/', [CustomerController::class, 'deleteAllCustomer'])->name('delete.all-customers');
    Route::resource('customers', CustomerController::class);
    Route::get('customers/single/{id}', [CustomerController::class, 'singleCustomer'])->name('customer.single');
    Route::get('customers-due-receive/create', [CustomerController::class, 'dueReceiveForm'])->name('customer.due-receive');
    Route::post('customers-due-receive', [CustomerController::class, 'dueReceive'])->name('customer.due-receive.store');
    Route::get('customer/due-receive', [CustomerController::class, 'dueReceiveList'])->name('customer.due-receive.list');
    Route::get('customer-due-receive/edit/{id}', [CustomerController::class, 'dueReceiveEdit'])->name('customer.due-receive.edit');



    Route::post('customer-due-receive/update/{id}', [CustomerController::class, 'dueReceiveUpdate'])->name('customer.due-receive.update');
    Route::delete('customer-due-receive/delete/{id}', [CustomerController::class, 'dueReceiveDelete'])->name('customer.due-receive.delete');
    Route::resource('customerGroup', CustomerGroupController::class);
    Route::resource('area', AreaController::class);
});
