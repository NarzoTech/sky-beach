<?php

use Illuminate\Support\Facades\Route;
use Modules\Membership\app\Http\Controllers\LoyaltyCustomerController;
use Modules\Membership\app\Http\Controllers\LoyaltyProgramController;
use Modules\Membership\app\Http\Controllers\LoyaltyRuleController;
use Modules\Membership\app\Http\Controllers\LoyaltyTransactionController;
use Modules\Membership\app\Http\Controllers\MembershipController;

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

Route::middleware(['auth:admin', 'permission:membership.view'])->group(function () {
    // Dashboard
    Route::get('membership', [MembershipController::class, 'index'])->name('membership.index');

    // Programs
    Route::resource('membership/programs', LoyaltyProgramController::class, ['as' => 'membership'])->names([
        'index' => 'programs.index',
        'create' => 'programs.create',
        'store' => 'programs.store',
        'show' => 'programs.show',
        'edit' => 'programs.edit',
        'update' => 'programs.update',
        'destroy' => 'programs.destroy',
    ]);

    // Rules
    Route::resource('membership/rules', LoyaltyRuleController::class, ['as' => 'membership'])->names([
        'index' => 'rules.index',
        'create' => 'rules.create',
        'store' => 'rules.store',
        'show' => 'rules.show',
        'edit' => 'rules.edit',
        'update' => 'rules.update',
        'destroy' => 'rules.destroy',
    ]);
    Route::post('membership/rules/priorities', [LoyaltyRuleController::class, 'updatePriorities'])->name('membership.rules.updatePriorities');

    // Customers
    Route::resource('membership/customers', LoyaltyCustomerController::class, ['as' => 'membership'])->names([
        'index' => 'customers.index',
        'show' => 'customers.show',
    ]);
    Route::post('membership/customers/{customer}/adjust-points', [LoyaltyCustomerController::class, 'adjustPoints'])->name('membership.customers.adjustPoints');
    Route::post('membership/customers/{customer}/block', [LoyaltyCustomerController::class, 'block'])->name('membership.customers.block');
    Route::post('membership/customers/{customer}/unblock', [LoyaltyCustomerController::class, 'unblock'])->name('membership.customers.unblock');
    Route::post('membership/customers/{customer}/suspend', [LoyaltyCustomerController::class, 'suspend'])->name('membership.customers.suspend');
    Route::post('membership/customers/{customer}/resume', [LoyaltyCustomerController::class, 'resume'])->name('membership.customers.resume');
    Route::get('membership/customers/export', [LoyaltyCustomerController::class, 'export'])->name('membership.customers.export');

    // Transactions
    Route::resource('membership/transactions', LoyaltyTransactionController::class, ['only' => ['index', 'show'], 'as' => 'membership'])->names([
        'index' => 'transactions.index',
        'show' => 'transactions.show',
    ]);
    Route::get('membership/transactions/export', [LoyaltyTransactionController::class, 'export'])->name('membership.transactions.export');
    Route::get('membership/transactions/statistics', [LoyaltyTransactionController::class, 'statistics'])->name('membership.transactions.statistics');
});
