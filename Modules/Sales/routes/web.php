<?php

use Illuminate\Support\Facades\Route;
use Modules\POS\app\Http\Controllers\POSController;
use Modules\Sales\app\Http\Controllers\SalesController;

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
});
