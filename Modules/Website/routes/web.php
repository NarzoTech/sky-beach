<?php

use Illuminate\Support\Facades\Route;
use Modules\Website\app\Http\Controllers\WebsiteController;
use Modules\Website\app\Http\Controllers\MenuActionController;
use Modules\Website\app\Http\Controllers\CartController;
use Modules\Website\app\Http\Controllers\CheckoutController;
use Modules\Website\app\Http\Controllers\BkashController;
use Modules\Website\app\Http\Controllers\OrderController;
use Modules\Website\app\Http\Controllers\ReservationController;
use Modules\Website\app\Http\Controllers\Admin\WebsiteOrderController;
use Modules\Website\app\Http\Controllers\Admin\CouponController;

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

Route::group([], function () {
    // Home Page
    Route::get('/', [WebsiteController::class, 'index'])->name('website.index');

    // About Page
    Route::get('/about', [WebsiteController::class, 'about'])->name('website.about');

    // Menu Pages
    Route::get('/menu', [WebsiteController::class, 'menu'])->name('website.menu');
    Route::get('/menu/{slug}', [WebsiteController::class, 'menuDetails'])->name('website.menu-details');

    // Blog Pages
    Route::get('/blogs', [WebsiteController::class, 'blogs'])->name('website.blogs');
    Route::get('/blog/{slug}', [WebsiteController::class, 'blogDetails'])->name('website.blog-details');

    // Contact Page
    Route::get('/contact', [WebsiteController::class, 'contact'])->name('website.contact');
    Route::post('/contact', [WebsiteController::class, 'storeContact'])->name('website.contact.store');

    // Chefs Page
    Route::get('/chefs', [WebsiteController::class, 'chefs'])->name('website.chefs');

    // Cart Routes
    Route::prefix('cart')->name('website.cart.')->group(function() {
        Route::get('/', [CartController::class, 'index'])->name('index');
        Route::get('/items', [CartController::class, 'getCart'])->name('items');
        Route::get('/count', [CartController::class, 'getCartCount'])->name('count');
        Route::post('/add', [CartController::class, 'addItem'])->name('add');
        Route::put('/update/{id}', [CartController::class, 'updateQuantity'])->name('update');
        Route::delete('/remove/{id}', [CartController::class, 'removeItem'])->name('remove');
        Route::delete('/clear', [CartController::class, 'clearCart'])->name('clear');
        Route::post('/coupon', [CartController::class, 'applyCoupon'])->name('coupon');
        Route::delete('/coupon', [CartController::class, 'removeCoupon'])->name('coupon.remove');
        Route::get('/coupon', [CartController::class, 'getAppliedCoupon'])->name('coupon.get');
    });

    // Checkout Routes
    Route::prefix('checkout')->name('website.checkout.')->group(function() {
        Route::get('/', [CheckoutController::class, 'index'])->name('index');
        Route::post('/process', [CheckoutController::class, 'processOrder'])->name('process');
        Route::get('/success/{uid}', [CheckoutController::class, 'orderSuccess'])->name('success');
    });

    // bKash Payment Routes
    Route::prefix('bkash')->name('website.bkash.')->group(function() {
        Route::post('/create', [BkashController::class, 'createPayment'])->name('create');
        Route::get('/callback', [BkashController::class, 'callback'])->name('callback');
    });

    // Legacy cart routes (redirect to new routes)
    Route::get('/cart-view', function() {
        return redirect()->route('website.cart.index');
    })->name('website.cart-view');
    Route::get('/checkout-old', function() {
        return redirect()->route('website.checkout.index');
    })->name('website.checkout');

    // FAQ Page
    Route::get('/faq', [WebsiteController::class, 'faq'])->name('website.faq');

    // Reservation Routes
    Route::prefix('reservation')->name('website.reservation.')->group(function() {
        Route::get('/', [ReservationController::class, 'index'])->name('index');
        Route::post('/', [ReservationController::class, 'store'])->name('store');
        Route::get('/check', [ReservationController::class, 'checkAvailability'])->name('check');
        Route::get('/success/{code}', [ReservationController::class, 'success'])->name('success');
    });

    // Service Pages
    Route::get('/service', [WebsiteController::class, 'service'])->name('website.service');
    Route::get('/service/{slug}', [WebsiteController::class, 'serviceDetails'])->name('website.service-details');
    Route::post('/service-contact', [WebsiteController::class, 'storeServiceContact'])->name('website.service-contact.store');

    // Privacy Policy & Terms
    Route::get('/privacy-policy', [WebsiteController::class, 'privacyPolicy'])->name('website.privacy-policy');
    Route::get('/terms-condition', [WebsiteController::class, 'termsCondition'])->name('website.terms-condition');

    // Error Page
    Route::get('/error', [WebsiteController::class, 'error'])->name('website.error');

    // Menu Actions (Favorites & Cart) - Legacy, keep for backwards compatibility
    Route::post('/menu/favorite/{itemId}', [MenuActionController::class, 'toggleFavorite'])->name('website.menu.favorite');
    Route::get('/menu/favorites', [MenuActionController::class, 'getFavorites'])->name('website.menu.favorites.get');
    Route::post('/menu/add-to-cart', [CartController::class, 'addItem'])->name('website.menu.add-to-cart');
});

// Frontend Order Routes (Auth Required)
Route::middleware('auth')->group(function() {
    // Orders
    Route::get('/my-orders', [OrderController::class, 'myOrders'])->name('website.orders.index');
    Route::get('/order/{id}', [OrderController::class, 'orderDetails'])->name('website.orders.show');
    Route::get('/order/{id}/track', [OrderController::class, 'trackOrder'])->name('website.orders.track');
    Route::get('/order/{id}/status', [OrderController::class, 'getOrderStatus'])->name('website.orders.status');
    Route::post('/order/{id}/cancel', [OrderController::class, 'cancelOrder'])->name('website.orders.cancel');
    Route::post('/order/{id}/reorder', [OrderController::class, 'reorder'])->name('website.orders.reorder');

    // Reservations
    Route::get('/my-reservations', [ReservationController::class, 'myReservations'])->name('website.reservations.index');
    Route::delete('/reservation/{id}', [ReservationController::class, 'cancel'])->name('website.reservation.cancel');
});

// Admin Website Orders Routes - Moved to routes/admin.php under Restaurant section
// Route::prefix('admin/website-orders')->middleware(['auth:admin'])->name('admin.website-orders.')->group(function() {
//     Route::get('/', [WebsiteOrderController::class, 'index'])->name('index');
//     Route::get('/export', [WebsiteOrderController::class, 'export'])->name('export');
//     Route::get('/{id}', [WebsiteOrderController::class, 'show'])->name('show');
//     Route::get('/{id}/print', [WebsiteOrderController::class, 'printOrder'])->name('print');
//     Route::put('/{id}/status', [WebsiteOrderController::class, 'updateStatus'])->name('status');
//     Route::post('/bulk-status', [WebsiteOrderController::class, 'bulkUpdateStatus'])->name('bulk-status');
// });

// Admin Coupons Routes
Route::prefix('admin/coupons')->middleware(['auth:admin'])->name('admin.coupons.')->group(function() {
    Route::get('/', [CouponController::class, 'index'])->name('index');
    Route::get('/create', [CouponController::class, 'create'])->name('create');
    Route::post('/', [CouponController::class, 'store'])->name('store');
    Route::get('/generate-code', [CouponController::class, 'generateCode'])->name('generate-code');
    Route::get('/{coupon}', [CouponController::class, 'show'])->name('show');
    Route::get('/{coupon}/edit', [CouponController::class, 'edit'])->name('edit');
    Route::put('/{coupon}', [CouponController::class, 'update'])->name('update');
    Route::delete('/{coupon}', [CouponController::class, 'destroy'])->name('destroy');
    Route::post('/{coupon}/toggle-status', [CouponController::class, 'toggleStatus'])->name('toggle-status');
});
