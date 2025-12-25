<?php

use Illuminate\Support\Facades\Route;
use Modules\Accounts\app\Http\Controllers\AccountsController;
use Modules\Accounts\app\Http\Controllers\BalanceController;
use Modules\Accounts\app\Http\Controllers\BankController;

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
    Route::resource('accounts', AccountsController::class)->names('accounts');
    Route::get('opening-balance', [BalanceController::class, 'openingBalance'])->name('opening-balance');
    Route::post('opening-balance', [BalanceController::class, 'store'])->name('opening-balance.store');
    Route::get('opening-balance/{id}/edit', [BalanceController::class, 'edit'])->name('opening-balance.edit');
    Route::put('opening-balance/{id}/update', [BalanceController::class, 'update'])->name('opening-balance.update');
    Route::delete('opening-balance/{id}/destroy', [BalanceController::class, 'destroy'])->name('opening-balance.destroy');
    Route::get('balance/transfer', [BalanceController::class, 'transfer'])->name('balance.transfer');
    Route::post('balance/transfer/store', [BalanceController::class, 'transferStore'])->name('balance.transfer.store');
    Route::patch('balance/transfer/update/{id}', [BalanceController::class, 'transferUpdate'])->name('balance.transfer.update');
    Route::delete('balance/transfer/destroy/{id}', [BalanceController::class, 'transferDestroy'])->name('balance.transfer.destroy');
    Route::resource('bank', BankController::class)->names('bank');
    Route::get('cashflow', [AccountsController::class, 'cashflow'])->name('cashflow');
});
