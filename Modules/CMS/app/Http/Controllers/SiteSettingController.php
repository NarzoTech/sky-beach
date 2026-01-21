<?php

namespace Modules\CMS\app\Http\Controllers;

use App\Enums\RedirectType;
use App\Http\Controllers\Controller;
use App\Traits\RedirectHelperTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\CMS\app\Models\SiteSetting;

class SiteSettingController extends Controller
{
    use RedirectHelperTrait;

    /**
     * Display settings by group.
     */
    public function index(Request $request)
    {
        checkAdminHasPermissionAndThrowException('cms.settings.view');

        $group = $request->get('group', 'general');
        $groups = SiteSetting::select('group')->distinct()->pluck('group');
        $settings = SiteSetting::where('group', $group)->orderBy('sort_order')->get();

        return view('cms::admin.site-settings.index', compact('settings', 'groups', 'group'));
    }

    /**
     * Update settings.
     */
    public function update(Request $request)
    {
        checkAdminHasPermissionAndThrowException('cms.settings.edit');

        DB::beginTransaction();
        try {
            $settings = $request->except('_token', '_method');

            foreach ($settings as $key => $value) {
                // Handle file uploads
                if ($request->hasFile($key)) {
                    $file = $request->file($key);
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $path = $file->storeAs('cms/settings', $filename, 'public');
                    $value = 'storage/' . $path;
                }

                SiteSetting::where('key', $key)->update(['value' => $value]);
                Cache::forget("cms_setting_{$key}");
            }

            // Clear group cache
            Cache::forget("cms_settings_group_" . $request->get('group', 'general'));

            DB::commit();
            return $this->redirectWithMessage(RedirectType::UPDATE->value, 'admin.cms.site-settings.index', ['group' => $request->get('group', 'general')]);
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            DB::rollBack();
            return $this->redirectWithMessage(RedirectType::ERROR->value, 'admin.cms.site-settings.index');
        }
    }

    /**
     * Create a new setting.
     */
    public function create()
    {
        checkAdminHasPermissionAndThrowException('cms.settings.create');
        $groups = SiteSetting::select('group')->distinct()->pluck('group');
        return view('cms::admin.site-settings.create', compact('groups'));
    }

    /**
     * Store a new setting.
     */
    public function store(Request $request)
    {
        checkAdminHasPermissionAndThrowException('cms.settings.create');

        $request->validate([
            'key' => 'required|string|unique:site_settings,key',
            'value' => 'nullable',
            'group' => 'required|string',
            'type' => 'required|string',
            'label' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            SiteSetting::create([
                'key' => $request->key,
                'value' => $request->value,
                'group' => $request->group,
                'type' => $request->type,
                'label' => $request->label,
                'sort_order' => $request->sort_order ?? 0,
            ]);

            DB::commit();
            return $this->redirectWithMessage(RedirectType::CREATE->value, 'admin.cms.site-settings.index', ['group' => $request->group]);
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            DB::rollBack();
            return $this->redirectWithMessage(RedirectType::ERROR->value, 'admin.cms.site-settings.index');
        }
    }

    /**
     * Delete a setting.
     */
    public function destroy(string $id)
    {
        checkAdminHasPermissionAndThrowException('cms.settings.delete');

        try {
            $setting = SiteSetting::findOrFail($id);
            Cache::forget("cms_setting_{$setting->key}");
            $setting->delete();

            return $this->redirectWithMessage(RedirectType::DELETE->value, 'admin.cms.site-settings.index');
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return $this->redirectWithMessage(RedirectType::ERROR->value, 'admin.cms.site-settings.index');
        }
    }
}
