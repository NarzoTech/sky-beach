<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Membership\app\Http\Controllers\POSController;

/*
    |--------------------------------------------------------------------------
    | API Routes
    |--------------------------------------------------------------------------
    |
    | Here is where you can register API routes for your application. These
    | routes are loaded by the RouteServiceProvider within a group which
    | is assigned the "api" middleware group. Enjoy building your API!
    |
*/

Route::middleware(['auth:sanctum'])->prefix('v1/membership')->name('api.membership.')->group(function () {
    // Customer identification
    Route::post('identify', [POSController::class, 'identifyCustomer'])->name('identify');

    // Point earning
    Route::post('earn-points', [POSController::class, 'earnPoints'])->name('earn');

    // Point redemption
    Route::post('redeem-points', [POSController::class, 'redeemPoints'])->name('redeem');

    // Check redemption eligibility
    Route::post('check-redemption', [POSController::class, 'checkRedemption'])->name('check-redemption');

    // Get customer balance
    Route::get('balance/{phone}', [POSController::class, 'getBalance'])->name('balance');

    // Get customer profile
    Route::get('customer/{phone}', [POSController::class, 'getCustomerProfile'])->name('customer');

    // Get transaction history
    Route::get('transactions/{phone}', [POSController::class, 'getTransactionHistory'])->name('transactions');
});
