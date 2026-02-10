<?php

use Illuminate\Support\Facades\Route;
use Modules\Report\app\Http\Controllers\ReportController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::group(['as' => 'admin.', 'prefix' => 'admin', 'middleware' => ['auth:admin', 'translation']], function () {
    // Restaurant Reports
    Route::get('report/menu-item-sales', [ReportController::class, 'menuItemSales'])->name('report.menu-item-sales');
    Route::get('report/waiter-performance', [ReportController::class, 'waiterPerformance'])->name('report.waiter-performance');
    Route::get('report/order-type', [ReportController::class, 'orderType'])->name('report.order-type');
    Route::get('report/table-performance', [ReportController::class, 'tablePerformance'])->name('report.table-performance');

    // General Reports
    Route::get('report/details-sale', [ReportController::class, 'detailsSale'])->name('report.details-sale');
    Route::get('report/customers', [ReportController::class, 'customers'])->name('report.customers');
    Route::get('report/expense', [ReportController::class, 'expense'])->name('report.expense');
    Route::get('report/profit-loss', [ReportController::class, 'profitLoss'])->name('report.profit-loss');
    Route::get('report/purchase', [ReportController::class, 'purchase'])->name('report.purchase');
    Route::get('report/supplier', [ReportController::class, 'supplier'])->name('report.supplier');
    Route::get('report/supplier-payment', [ReportController::class, 'supplierPayment'])->name('report.supplier-payment');
    Route::get('report/salary', [ReportController::class, 'salary'])->name('report.salary');
    Route::get('report/low-stock-alert', [ReportController::class, 'lowStockAlert'])->name('report.low-stock-alert');
});
