<?php

use Illuminate\Support\Facades\Route;
use Modules\Website\app\Http\Controllers\WebsiteController;

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
    Route::get('/menu-details', [WebsiteController::class, 'menuDetails'])->name('website.menu-details');
    
    // Blog Pages
    Route::get('/blogs', [WebsiteController::class, 'blogs'])->name('website.blogs');
    Route::get('/blog/{slug}', [WebsiteController::class, 'blogDetails'])->name('website.blog-details');
    
    // Contact Page
    Route::get('/contact', [WebsiteController::class, 'contact'])->name('website.contact');
    
    // Chefs Page
    Route::get('/chefs', [WebsiteController::class, 'chefs'])->name('website.chefs');
    
    // Cart & Checkout Pages
    Route::get('/cart-view', [WebsiteController::class, 'cartView'])->name('website.cart-view');
    Route::get('/checkout', [WebsiteController::class, 'checkout'])->name('website.checkout');
    
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
});
