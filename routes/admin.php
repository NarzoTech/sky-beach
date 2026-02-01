<?php

use App\Http\Controllers\Admin\AddonsController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AssetController;
use App\Http\Controllers\Admin\AssetTypeController;
use App\Http\Controllers\Admin\Auth\AuthenticatedSessionController;
/*  Start Admin panel Controller  */
use App\Http\Controllers\Admin\Auth\NewPasswordController;
use App\Http\Controllers\Admin\Auth\PasswordResetLinkController;
use App\Http\Controllers\Admin\BusinessController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\NoticeController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;
use App\Http\Controllers\Admin\QuotationController;
use App\Http\Controllers\Admin\RolesController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\TaxReportController;
use App\Http\Controllers\Admin\WarehouseController;
use App\Http\Controllers\StockController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

/*  End Admin panel Controller  */



Route::group(['as' => 'admin.', 'prefix' => 'admin'], function () {
    /* Start admin auth route */
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('store-login', [AuthenticatedSessionController::class, 'store'])->name('store-login');
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/forget-password', [PasswordResetLinkController::class, 'custom_forget_password'])->name('forget-password');
    Route::get('reset-password/{token}', [NewPasswordController::class, 'custom_reset_password_page'])->name('password.reset');
    Route::post('/reset-password-store/{token}', [NewPasswordController::class, 'custom_reset_password_store'])->name('password.reset-store');
    /* End admin auth route */

    Route::middleware(['auth:admin'])->group(function () {
        Route::get('/', [DashboardController::class, 'dashboard']);
        Route::get('dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');

        Route::get('/stock', [StockController::class, 'index'])->name('stock.index');
        Route::get('/stock/ledger/{id}', [StockController::class, 'ledger'])->name('stock.ledger');
        Route::put('/stock/reset/{id}', [StockController::class, 'reset'])->name('stock.reset');

        Route::put('/stock/reset/', [StockController::class, 'resetAll'])->name('stock.reset.all');
        Route::resource('quotation', QuotationController::class);
        Route::post('user/quick-store', [QuotationController::class, 'quickStoreCustomer'])->name('user.quick-store');
        Route::resource('asset-category', AssetTypeController::class);
        Route::resource('assets', AssetController::class);
        Route::controller(AdminProfileController::class)->group(function () {
            Route::get('edit-profile', 'edit_profile')->name('edit-profile');
            Route::put('profile-update', 'profile_update')->name('profile-update');
            Route::put('update-password', 'update_password')->name('update-password');
        });

        Route::get('role/assign', [RolesController::class, 'assignRoleView'])->name('role.assign');
        Route::post('role/assign/{id}', [RolesController::class, 'getAdminRoles'])->name('role.assign.admin');
        Route::put('role/assign', [RolesController::class, 'assignRoleUpdate'])->name('role.assign.update');
        Route::resource('/role', RolesController::class);
        Route::resource('/role', RolesController::class);

        // Tax Reports
        Route::prefix('tax-reports')->name('tax-reports.')->group(function () {
            Route::get('/', [TaxReportController::class, 'index'])->name('index');
            Route::get('/ledger', [TaxReportController::class, 'ledger'])->name('ledger');
            Route::get('/periods', [TaxReportController::class, 'periods'])->name('periods');
            Route::get('/export', [TaxReportController::class, 'export'])->name('export');
            Route::post('/generate-period', [TaxReportController::class, 'generatePeriod'])->name('generate-period');
            Route::post('/close-period/{id}', [TaxReportController::class, 'closePeriod'])->name('close-period');
            Route::post('/mark-filed/{id}', [TaxReportController::class, 'markFiled'])->name('mark-filed');
            Route::post('/sync-sales', [TaxReportController::class, 'syncSales'])->name('sync-sales');
            Route::post('/void/{id}', [TaxReportController::class, 'voidEntry'])->name('void');
            Route::post('/adjustment', [TaxReportController::class, 'createAdjustment'])->name('adjustment');
        });
    });
    Route::resource('admin', AdminController::class)->except('show');
    Route::put('admin-status/{id}', [AdminController::class, 'changeStatus'])->name('admin.status');
    // Settings routes
    Route::get('settings', [SettingController::class, 'settings'])->name('settings');
    Route::get('print-setting', [SettingController::class, 'printSetting'])->name('print.settings');
    Route::get('courier-settings', [SettingController::class, 'courierSetting'])->name('courier.settings');
    Route::post('courier-settings', [SettingController::class, 'courierSettingStore'])->name('courier.settings.store');
    Route::get('tax-settings', [SettingController::class, 'taxSetting'])->name('tax.settings');

    Route::resource('business', BusinessController::class);
    Route::get('notice/create', [NoticeController::class, 'create'])->name('notice.create');
    Route::post('notice/store', [NoticeController::class, 'store'])->name('notice.store');

    Route::get('reset/database', [SettingController::class, 'resetDatabase'])->name('reset.database');
    Route::delete('reset/database', [SettingController::class, 'clearDatabase'])->name('database-clear-success');

    // Warehouse
    Route::resource('warehouse', WarehouseController::class)->only(['index', 'store', 'update', 'destroy']);

    // Restaurant/Website Management Routes
    Route::prefix('restaurant')->name('restaurant.')->middleware(['auth:admin'])->group(function () {
        // Blogs Management
        Route::resource('blogs', \Modules\Website\app\Http\Controllers\Admin\BlogController::class)->except('show');
        
        // Chefs Management
        Route::resource('chefs', \Modules\Website\app\Http\Controllers\Admin\ChefController::class)->except('show');
        
        // Website Services Management
        Route::resource('website-services', \Modules\Website\app\Http\Controllers\Admin\WebsiteServiceController::class)->except('show');
        
        // Bookings Management
        Route::resource('bookings', \Modules\Website\app\Http\Controllers\Admin\BookingController::class)->only(['index', 'show', 'destroy']);
        Route::put('bookings/{booking}/status', [\Modules\Website\app\Http\Controllers\Admin\BookingController::class, 'updateStatus'])->name('bookings.update-status');
        
        // FAQs Management
        Route::resource('faqs', \Modules\Website\app\Http\Controllers\Admin\FaqController::class)->except('show');

        // Service Contacts Management
        Route::resource('service-contacts', \Modules\Website\app\Http\Controllers\Admin\ServiceContactController::class)->only(['index', 'show', 'destroy']);
        Route::put('service-contacts/{serviceContact}/status', [\Modules\Website\app\Http\Controllers\Admin\ServiceContactController::class, 'updateStatus'])->name('service-contacts.update-status');

        // Service FAQs Management
        Route::resource('service-faqs', \Modules\Website\app\Http\Controllers\Admin\ServiceFaqController::class)->except('show');

        // Contact Messages Management
        Route::resource('contact-messages', \Modules\Website\app\Http\Controllers\Admin\ContactMessageController::class)->only(['index', 'show', 'destroy']);
        Route::put('contact-messages/{contactMessage}/status', [\Modules\Website\app\Http\Controllers\Admin\ContactMessageController::class, 'updateStatus'])->name('contact-messages.update-status');

        // Website Orders Management
        Route::prefix('website-orders')->name('website-orders.')->group(function () {
            Route::get('/', [\Modules\Website\app\Http\Controllers\Admin\WebsiteOrderController::class, 'index'])->name('index');
            Route::get('/export', [\Modules\Website\app\Http\Controllers\Admin\WebsiteOrderController::class, 'export'])->name('export');
            Route::get('/{id}', [\Modules\Website\app\Http\Controllers\Admin\WebsiteOrderController::class, 'show'])->name('show');
            Route::get('/{id}/print', [\Modules\Website\app\Http\Controllers\Admin\WebsiteOrderController::class, 'printOrder'])->name('print');
            Route::put('/{id}/status', [\Modules\Website\app\Http\Controllers\Admin\WebsiteOrderController::class, 'updateStatus'])->name('status');
            Route::put('/{id}/payment-status', [\Modules\Website\app\Http\Controllers\Admin\WebsiteOrderController::class, 'updatePaymentStatus'])->name('payment-status');
            Route::post('/bulk-status', [\Modules\Website\app\Http\Controllers\Admin\WebsiteOrderController::class, 'bulkUpdateStatus'])->name('bulk-status');
        });

    });

    // CMS Management Routes
    Route::prefix('cms')->name('cms.')->group(function () {
        // Section Management (New)
        Route::get('sections/homepage', [\Modules\CMS\app\Http\Controllers\SectionController::class, 'homepage'])->name('sections.homepage');
        Route::get('sections/about', [\Modules\CMS\app\Http\Controllers\SectionController::class, 'aboutPage'])->name('sections.about');
        Route::get('sections/contact', [\Modules\CMS\app\Http\Controllers\SectionController::class, 'contactPage'])->name('sections.contact');
        Route::get('sections/menu', [\Modules\CMS\app\Http\Controllers\SectionController::class, 'menuPage'])->name('sections.menu');
        Route::get('sections/reservation', [\Modules\CMS\app\Http\Controllers\SectionController::class, 'reservationPage'])->name('sections.reservation');
        Route::get('sections/service', [\Modules\CMS\app\Http\Controllers\SectionController::class, 'servicePage'])->name('sections.service');
        Route::get('sections/{section}/edit', [\Modules\CMS\app\Http\Controllers\SectionController::class, 'editSection'])->name('sections.edit');
        Route::put('sections/{section}', [\Modules\CMS\app\Http\Controllers\SectionController::class, 'updateSection'])->name('sections.update');
        Route::post('sections/{section}/toggle-status', [\Modules\CMS\app\Http\Controllers\SectionController::class, 'toggleStatus'])->name('sections.toggle-status');

        // Testimonials
        Route::resource('testimonials', \Modules\CMS\app\Http\Controllers\TestimonialController::class)->except('show');
        Route::post('testimonials/{id}/toggle-status', [\Modules\CMS\app\Http\Controllers\TestimonialController::class, 'toggleStatus'])->name('testimonials.toggle-status');
        Route::post('testimonials/delete-all', [\Modules\CMS\app\Http\Controllers\TestimonialController::class, 'deleteAll'])->name('testimonials.delete-all');

        // Counters
        Route::resource('counters', \Modules\CMS\app\Http\Controllers\CounterController::class)->except('show');
        Route::post('counters/{id}/toggle-status', [\Modules\CMS\app\Http\Controllers\CounterController::class, 'toggleStatus'])->name('counters.toggle-status');

        // Page Sections
        Route::resource('page-sections', \Modules\CMS\app\Http\Controllers\PageSectionController::class)->except('show');
        Route::post('page-sections/{id}/toggle-status', [\Modules\CMS\app\Http\Controllers\PageSectionController::class, 'toggleStatus'])->name('page-sections.toggle-status');

        // Promotional Banners
        Route::resource('banners', \Modules\CMS\app\Http\Controllers\PromotionalBannerController::class)->except('show');
        Route::post('banners/{id}/toggle-status', [\Modules\CMS\app\Http\Controllers\PromotionalBannerController::class, 'toggleStatus'])->name('banners.toggle-status');

        // Legal Pages
        Route::resource('legal-pages', \Modules\CMS\app\Http\Controllers\LegalPageController::class)->except('show');
        Route::post('legal-pages/{id}/toggle-status', [\Modules\CMS\app\Http\Controllers\LegalPageController::class, 'toggleStatus'])->name('legal-pages.toggle-status');

        // Gallery Images
        Route::resource('gallery', \Modules\CMS\app\Http\Controllers\GalleryImageController::class)->except('show');
        Route::post('gallery/{id}/toggle-status', [\Modules\CMS\app\Http\Controllers\GalleryImageController::class, 'toggleStatus'])->name('gallery.toggle-status');
        Route::post('gallery/delete-all', [\Modules\CMS\app\Http\Controllers\GalleryImageController::class, 'deleteAll'])->name('gallery.delete-all');

        // Info Cards
        Route::resource('info-cards', \Modules\CMS\app\Http\Controllers\InfoCardController::class)->except('show');
        Route::post('info-cards/{id}/toggle-status', [\Modules\CMS\app\Http\Controllers\InfoCardController::class, 'toggleStatus'])->name('info-cards.toggle-status');

        // Event Types
        Route::resource('event-types', \Modules\CMS\app\Http\Controllers\EventTypeController::class)->except('show');
        Route::post('event-types/{id}/toggle-status', [\Modules\CMS\app\Http\Controllers\EventTypeController::class, 'toggleStatus'])->name('event-types.toggle-status');

        // Features
        Route::resource('features', \Modules\CMS\app\Http\Controllers\FeatureController::class)->except('show');
        Route::post('features/{id}/toggle-status', [\Modules\CMS\app\Http\Controllers\FeatureController::class, 'toggleStatus'])->name('features.toggle-status');
    });
});
