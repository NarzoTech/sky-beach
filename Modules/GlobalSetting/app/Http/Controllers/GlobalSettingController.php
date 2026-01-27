<?php

namespace Modules\GlobalSetting\app\Http\Controllers;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;
use Modules\GlobalSetting\app\Enums\AllTimeZoneEnum;
use Modules\GlobalSetting\app\Models\Setting;


use Modules\GlobalSetting\app\Models\CustomPagination;

class GlobalSettingController extends Controller
{
    protected $cachedSetting;

    public function __construct()
    {
        $this->cachedSetting = Cache::get('setting');
    }

    public function general_setting()
    {
        checkAdminHasPermissionAndThrowException('setting.view');
        $custom_paginations = CustomPagination::all();
        $all_timezones = AllTimeZoneEnum::getAll();

        return view('globalsetting::settings.index', compact('custom_paginations', 'all_timezones'));
    }

    public function update_general_setting(Request $request)
    {

        checkAdminHasPermissionAndThrowException('setting.update');

        $request->validate([
            'app_name' => 'required',
            // 'timezone' => 'required',
        ], [
            'app_name' => __('App name is required'),
            // 'timezone' => __('Timezone is required'),
        ]);


        foreach ($request->except('_token', '_method') as $key => $value) {

            $setting = Setting::where('key', $key)->first();

            if ($key == 'logo') {
                $file_name = file_upload($request->logo, 'uploads/custom-images/', $this->cachedSetting?->logo);
                $value = $file_name;
            }
            // favicon
            if ($key == 'favicon') {
                $file_name = file_upload($request->favicon, 'uploads/custom-images/', $this->cachedSetting?->favicon);
                $value = $file_name;
            }
            // login
            if ($key == 'login') {
                $file_name = file_upload($request->login, 'uploads/custom-images/', $this->cachedSetting?->login);
                $value = $file_name;
            }

            if ($setting) {
                $setting->value = $value;
                $setting->save();
            } else {
                continue;
            }
        }


        $this->put_setting_cache();

        $notification = __('Update Successfully');
        $notification = ['messege' => $notification, 'alert-type' => 'success'];

        return redirect()->back()->with($notification);
    }

    public function update_logo_favicon(Request $request)
    {
        checkAdminHasPermissionAndThrowException('setting.update');

        if ($request->file('logo')) {
            $file_name = file_upload($request->logo, 'uploads/custom-images/', $this->cachedSetting?->logo);
            Setting::where('key', 'logo')->update(['value' => $file_name]);
        }

        if ($request->file('favicon')) {
            $file_name = file_upload($request->favicon, 'uploads/custom-images/', $this->cachedSetting?->favicon);
            Setting::where('key', 'favicon')->update(['value' => $file_name]);
        }

        $this->put_setting_cache();

        $notification = __('Update Successfully');
        $notification = ['messege' => $notification, 'alert-type' => 'success'];

        return redirect()->back()->with($notification);
    }


    public function update_default_avatar(Request $request)
    {
        checkAdminHasPermissionAndThrowException('setting.update');

        if ($request->file('default_avatar')) {
            $file_name = file_upload($request->default_avatar, 'uploads/custom-images/', $this->cachedSetting?->default_avatar);
            Setting::where('key', 'default_avatar')->update(['value' => $file_name]);
        }

        $this->put_setting_cache();

        $notification = __('Update Successfully');
        $notification = ['messege' => $notification, 'alert-type' => 'success'];

        return redirect()->back()->with($notification);
    }


    public function update_copyright_text(Request $request)
    {
        checkAdminHasPermissionAndThrowException('setting.update');
        $request->validate([
            'copyright_text' => 'required',
        ], [
            'copyright_text' => __('Copyright Text is required'),
        ]);
        Setting::where('key', 'copyright_text')->update(['value' => $request->copyright_text]);

        $this->put_setting_cache();

        $notification = __('Update Successfully');
        $notification = ['messege' => $notification, 'alert-type' => 'success'];

        return redirect()->back()->with($notification);
    }

    public function cache_clear()
    {
        checkAdminHasPermissionAndThrowException('setting.update');

        return view('globalsetting::cache_clear');
    }

    public function cache_clear_confirm()
    {
        checkAdminHasPermissionAndThrowException('setting.update');
        Artisan::call('optimize:clear');

        $notification = __('Cache cleared successfully');
        $notification = ['messege' => $notification, 'alert-type' => 'success'];

        return redirect()->back()->with($notification);
    }

    public function put_setting_cache()
    {
        $setting_info = Setting::get();

        $setting = [];
        foreach ($setting_info as $setting_item) {
            $setting[$setting_item->key] = $setting_item->value;
        }

        $setting = (object) $setting;

        Cache::put('setting', $setting);
    }

    /**
     * Display website checkout settings page
     */
    public function website_checkout_settings()
    {
        checkAdminHasPermissionAndThrowException('setting.view');

        return view('globalsetting::settings.website_checkout');
    }

    /**
     * Update website checkout settings (Tax, Delivery Fee, Loyalty)
     */
    public function update_website_checkout_settings(Request $request)
    {
        checkAdminHasPermissionAndThrowException('setting.update');

        $request->validate([
            'website_tax_rate' => 'required|numeric|min:0|max:100',
            'website_delivery_fee' => 'required|numeric|min:0',
            'website_free_delivery_threshold' => 'nullable|numeric|min:0',
        ]);

        $settings = [
            'website_tax_enabled' => $request->has('website_tax_enabled') ? '1' : '0',
            'website_tax_rate' => $request->website_tax_rate,
            'website_delivery_fee_enabled' => $request->has('website_delivery_fee_enabled') ? '1' : '0',
            'website_delivery_fee' => $request->website_delivery_fee,
            'website_free_delivery_threshold' => $request->website_free_delivery_threshold ?? '0',
            'website_loyalty_enabled' => $request->has('website_loyalty_enabled') ? '1' : '0',
        ];

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        $this->put_setting_cache();

        $notification = __('Website checkout settings updated successfully');
        $notification = ['messege' => $notification, 'alert-type' => 'success'];

        return redirect()->back()->with($notification);
    }
}
