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

Route::middleware(['auth:admin'])->prefix('admin')->group(function () {
    // Dashboard
    Route::get('membership', [MembershipController::class, 'index'])->name('membership.index');

    // Programs
    Route::resource('membership/programs', LoyaltyProgramController::class)->names([
        'index'   => 'membership.programs.index',
        'create'  => 'membership.programs.create',
        'store'   => 'membership.programs.store',
        'show'    => 'membership.programs.show',
        'edit'    => 'membership.programs.edit',
        'update'  => 'membership.programs.update',
        'destroy' => 'membership.programs.destroy',
    ]);

    // Rules
    Route::resource('membership/rules', LoyaltyRuleController::class)->names([
        'index'   => 'membership.rules.index',
        'create'  => 'membership.rules.create',
        'store'   => 'membership.rules.store',
        'show'    => 'membership.rules.show',
        'edit'    => 'membership.rules.edit',
        'update'  => 'membership.rules.update',
        'destroy' => 'membership.rules.destroy',
    ]);
    Route::post('membership/rules/priorities', [LoyaltyRuleController::class, 'updatePriorities'])->name('membership.rules.updatePriorities');

    // Customers - specific routes BEFORE resource
    Route::get('membership/customers/export', [LoyaltyCustomerController::class, 'export'])->name('membership.customers.export');
    Route::resource('membership/customers', LoyaltyCustomerController::class)->names([
        'index'   => 'membership.customers.index',
        'create'  => 'membership.customers.create',
        'store'   => 'membership.customers.store',
        'show'    => 'membership.customers.show',
        'edit'    => 'membership.customers.edit',
        'update'  => 'membership.customers.update',
        'destroy' => 'membership.customers.destroy',
    ]);
    Route::post('membership/customers/{customer}/adjust-points', [LoyaltyCustomerController::class, 'adjustPoints'])->name('membership.customers.adjustPoints');
    Route::post('membership/customers/{customer}/block', [LoyaltyCustomerController::class, 'block'])->name('membership.customers.block');
    Route::post('membership/customers/{customer}/unblock', [LoyaltyCustomerController::class, 'unblock'])->name('membership.customers.unblock');
    Route::post('membership/customers/{customer}/suspend', [LoyaltyCustomerController::class, 'suspend'])->name('membership.customers.suspend');
    Route::post('membership/customers/{customer}/resume', [LoyaltyCustomerController::class, 'resume'])->name('membership.customers.resume');

    // Transactions - specific routes BEFORE resource
    Route::get('membership/transactions/export', [LoyaltyTransactionController::class, 'export'])->name('membership.transactions.export');
    Route::get('membership/transactions/statistics', [LoyaltyTransactionController::class, 'statistics'])->name('membership.transactions.statistics');
    Route::resource('membership/transactions', LoyaltyTransactionController::class, ['only' => ['index', 'show']])->names([
        'index' => 'membership.transactions.index',
        'show'  => 'membership.transactions.show',
    ]);
});
