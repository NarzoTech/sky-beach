<?php

use Illuminate\Support\Facades\Route;
use Modules\Service\app\Http\Controllers\ServiceCategoryController;
use Modules\Service\app\Http\Controllers\ServiceController;

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
    Route::post('service/wishlist/{id}', [ServiceController::class, 'addToWishlist'])->name('service.wishlist');
    Route::resource('service', ServiceController::class)->names('service');
    Route::resource('serviceCategory', ServiceCategoryController::class);
});
