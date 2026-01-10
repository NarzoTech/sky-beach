<?php

use Illuminate\Support\Facades\Route;
use Modules\POS\app\Http\Controllers\POSController;
use Modules\POS\app\Http\Controllers\PosSettingsController;

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
    Route::prefix('pos')->group(function () {
        Route::get('/', [POSController::class, 'index'])->name('pos');
        Route::get('/load-products', [POSController::class, 'load_products'])->name('load-products');
        Route::get('load-products-list', [POSController::class, 'load_products_list'])->name('load-products-list');
        Route::get('/load-product-modal/{id}', [POSController::class, 'load_product_modal'])->name('load-product-modal');
        Route::get('/pos/load-customer-address/{id}', [POSController::class, 'load_customer_address'])->name('load-customer-address');
        Route::get('/add-to-cart', [POSController::class, 'add_to_cart'])->name('add-to-cart');
        Route::get('/cart-quantity-update', [POSController::class, 'cart_quantity_update'])->name('cart-quantity-update');
        Route::get('cart-price-update', [POSController::class, 'cart_price_update'])->name('cart-price-update');
        Route::get('/remove-cart-item/{id}', [POSController::class, 'remove_cart_item'])->name('remove-cart-item');
        Route::get('/cart-clear', [POSController::class, 'cart_clear'])->name('cart-clear');
        Route::get('/pos-cart-item-details/{id}', [POSController::class, 'posCartItemDetails'])->name('pos-cart-item-details');
        Route::post('/create-new-customer', [POSController::class, 'create_new_customer'])->name('create-new-customer');
        Route::post('/create-new-address', [POSController::class, 'create_new_address'])->name('create-new-address');
        Route::post('/place-order', [POSController::class, 'place_order'])->name('place-order');

        Route::get('/check-cart-restaurant/{id}', [POSController::class, 'check_cart_restaurant'])->name('check-cart-restaurant');
        Route::get('/modal-cart-clear', [POSController::class, 'modalClearCart'])->name('modal-cart-clear');

        // Running Orders Routes
        Route::get('/running-orders', [POSController::class, 'getRunningOrders'])->name('pos.running-orders');
        Route::get('/running-orders/count', [POSController::class, 'getRunningOrdersCount'])->name('pos.running-orders.count');
        Route::get('/running-orders/{id}/details', [POSController::class, 'getOrderDetails'])->name('pos.running-orders.details');
        Route::post('/running-orders/{id}/load-to-cart', [POSController::class, 'loadOrderToCart'])->name('pos.running-orders.load-cart');
        Route::post('/running-orders/{id}/update', [POSController::class, 'updateRunningOrder'])->name('pos.running-orders.update');
        Route::post('/running-orders/{id}/complete', [POSController::class, 'completeRunningOrder'])->name('pos.running-orders.complete');
        Route::post('/running-orders/{id}/cancel', [POSController::class, 'cancelRunningOrder'])->name('pos.running-orders.cancel');
    });
    Route::get('cart/source/update', [POSController::class, 'cartSourceUpdate'])->name('cart.source.update');
    Route::get('cart/price/update', [POSController::class, 'cartPriceUpdate'])->name('cart.price.update');
    Route::post('/cart-hold', [POSController::class, 'cartHold'])->name('cart.hold');
    Route::get('/cart-hold/delete/{id}', [POSController::class, 'cartHoldDelete'])->name('cart.hold.delete');
    Route::get('/cart-hold/edit/{id}', [POSController::class, 'cartHoldEdit'])->name('cart.hold.edit');

    Route::get('pos/settings', [PosSettingsController::class, 'index'])->name('pos.settings');
    Route::post('pos/settings', [PosSettingsController::class, 'store'])->name('pos.settings.store');
});
