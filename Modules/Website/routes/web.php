<?php

use Illuminate\Support\Facades\Route;
use Modules\Website\app\Http\Controllers\WebsiteController;
use Modules\Website\app\Http\Controllers\MenuActionController;
use Modules\Website\app\Http\Controllers\CartController;
use Modules\Website\app\Http\Controllers\CheckoutController;

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
    });

    // Checkout Routes
    Route::prefix('checkout')->name('website.checkout.')->group(function() {
        Route::get('/', [CheckoutController::class, 'index'])->name('index');
        Route::post('/process', [CheckoutController::class, 'processOrder'])->name('process');
        Route::get('/success/{id}', [CheckoutController::class, 'orderSuccess'])->name('success');
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

    // Reservation Page
    Route::get('/reservation', [WebsiteController::class, 'reservation'])->name('website.reservation');

    // Service Pages
    Route::get('/service', [WebsiteController::class, 'service'])->name('website.service');
    Route::get('/service/{slug}', [WebsiteController::class, 'serviceDetails'])->name('website.service-details');

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
