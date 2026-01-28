<?php

use Illuminate\Support\Facades\Route;
use Modules\POS\app\Http\Controllers\POSController;
use Modules\Sales\app\Http\Controllers\SalesController;
use Modules\Sales\app\Http\Controllers\SalesReturnController;

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
    Route::resource('sales', SalesController::class)->names('sales');
    Route::get('sales/{id}/invoice', [SalesController::class, 'invoice'])->name('sales.invoice');
    Route::get('sales/{id}/details', [SalesController::class, 'getSaleDetails'])->name('sales.details');
    Route::post('sales/receive-payment', [SalesController::class, 'receivePayment'])->name('sales.receive-payment');
    Route::get('sales/return/list', [SalesReturnController::class, 'returnList'])->name('sales.return.list');
    Route::get('sales/return/create/{sale_id}', [SalesReturnController::class, 'create'])->name('sales.return.create');
    Route::post('sales/return/store', [SalesReturnController::class, 'store'])->name('sales.return.store');
    Route::get('sales/return/edit/{id}', [SalesReturnController::class, 'edit'])->name('sales.return.edit');
    Route::put('sales/return/update/{id}', [SalesReturnController::class, 'update'])->name('sales.return.update');
    Route::delete('sales/return/destroy/{id}', [SalesReturnController::class, 'destroy'])->name('sales.return.destroy');
});
