<?php

use Illuminate\Support\Facades\Route;
use Modules\StockAdjustment\app\Http\Controllers\StockAdjustmentController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::group(['as' => 'admin.', 'prefix' => 'admin'], function () {
    Route::resource('stock-adjustment', StockAdjustmentController::class)->names('stock-adjustment');
    Route::get('stock-adjustment-wastage-summary', [StockAdjustmentController::class, 'wastageSummary'])
        ->name('stock-adjustment.wastage-summary');
});
