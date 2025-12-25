<?php

use Illuminate\Support\Facades\Route;
use Modules\Product\app\Http\Controllers\ProductController;
use Modules\Purchase\app\Http\Controllers\PurchaseController;
use Modules\Purchase\app\Http\Controllers\PurchaseReturnController;
use Modules\Purchase\app\Http\Controllers\PurchaseReturnTypeController;

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
    Route::resource('purchase', PurchaseController::class)->names('purchase');
    Route::get('purchase/returns/type/list', [PurchaseReturnTypeController::class, 'index'])->name('purchase.return.type.list');
    Route::get('purchase/{id}/invoice', [PurchaseController::class, 'invoice'])->name('purchase.invoice');
    Route::post('purchase/returns/type', [PurchaseReturnTypeController::class, 'store'])->name('purchase.return.type.store');
    Route::delete('purchase/returns/type/{id}', [PurchaseReturnTypeController::class, 'destroy'])->name('purchase.return.type.destroy');
    Route::put('purchase/returns/type/{id}', [PurchaseReturnTypeController::class, 'update'])->name('purchase.return.type.update');

    Route::post('purchase/product', [PurchaseController::class, 'product'])->name('purchase.product');
    Route::get('purchase/list/return', [PurchaseReturnController::class, 'index'])->name('purchase.return.index');
    Route::get('purchase/return/{id}', [PurchaseReturnController::class, 'create'])->name('purchase.return');
    Route::post('purchase/return/{id}', [PurchaseReturnController::class, 'store'])->name('purchase.return.store');

    Route::get('purchase/return/{id}/invoice', [PurchaseReturnController::class, 'invoice'])->name('purchase.return.invoice');

    Route::get('purchase/return/{id}/edit', [PurchaseReturnController::class, 'edit'])->name('purchase.return.edit');
    Route::put('purchase/return/{id}/update', [PurchaseReturnController::class, 'update'])->name('purchase.return.update');
    Route::delete('purchase/return/{id}/destroy', [PurchaseReturnController::class, 'destroy'])->name('purchase.return.destroy');

    Route::post('purchase/product-search', [ProductController::class, 'searchProducts'])->name('purchase.product.search');
});
