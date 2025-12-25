<?php

use Illuminate\Support\Facades\Route;
use Modules\GlobalSetting\app\Http\Controllers\EmailSettingController;
use Modules\GlobalSetting\app\Http\Controllers\GlobalSettingController;

Route::group(['as' => 'admin.', 'prefix' => 'admin', 'middleware' => ['auth:admin', 'translation']], function () {

    Route::controller(GlobalSettingController::class)->group(function () {

        Route::get('general-setting', 'general_setting')->name('general-setting');
        Route::put('update-general-setting', 'update_general_setting')->name('update-general-setting');

        Route::put('update-logo-favicon', 'update_logo_favicon')->name('update-logo-favicon');

        Route::put('update-default-avatar', 'update_default_avatar')->name('update-default-avatar');

        Route::put('update-copyright-text', 'update_copyright_text')->name('update-copyright-text');
        Route::put('update-maintenance-mode', 'update_maintenance_mode')->name('update-maintenance-mode');

        Route::get('cache-clear', 'cache_clear')->name('cache.clear');
        Route::post('cache-clear', 'cache_clear_confirm')->name('cache-clear-confirm');
    });

    Route::controller(EmailSettingController::class)->group(function () {

        Route::get('email-configuration', 'email_config')->name('email-configuration');
        Route::put('update-email-configuration', 'update_email_config')->name('update-email-configuration');

        Route::get('edit-email-template/{id}', 'edit_email_template')->name('edit-email-template');
        Route::put('update-email-template/{id}', 'update_email_template')->name('update-email-template');

        Route::post('test/mail/credentials', 'test_mail_credentials')->name('test-mail-credentials');
    });
});
