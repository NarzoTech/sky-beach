<?php

namespace Modules\CMS\app\Http\Controllers;

use App\Enums\RedirectType;
use App\Http\Controllers\Controller;
use App\Traits\RedirectHelperTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Modules\CMS\app\Models\PromotionalBanner;

class PromotionalBannerController extends Controller
{
    use RedirectHelperTrait;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        checkAdminHasPermissionAndThrowException('cms.banners.view');

        $query = PromotionalBanner::orderBy('position')->orderBy('sort_order');

        if ($request->has('position') && $request->position) {
            $query->where('position', $request->position);
        }

        $banners = $query->paginate(20);
        $positions = PromotionalBanner::select('position')->distinct()->pluck('position');

        return view('cms::admin.banners.index', compact('banners', 'positions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        checkAdminHasPermissionAndThrowException('cms.banners.create');
        $positions = ['home_large', 'home_small', 'home_full', 'sidebar', 'popup'];
        return view('cms::admin.banners.create', compact('positions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        checkAdminHasPermissionAndThrowException('cms.banners.create');

        $request->validate([
            'position' => 'required|string',
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'image' => 'required|image|max:2048',
            'background_image' => 'nullable|image|max:2048',
        ]);

        DB::beginTransaction();
        try {
            $data = $request->only(['position', 'title', 'subtitle', 'button_text', 'button_link', 'badge_text', 'start_date', 'end_date', 'is_active', 'sort_order']);

            if ($request->hasFile('image')) {
                $data['image'] = 'storage/' . $request->file('image')->store('cms/banners', 'public');
            }

            if ($request->hasFile('background_image')) {
                $data['background_image'] = 'storage/' . $request->file('background_image')->store('cms/banners', 'public');
            }

            PromotionalBanner::create($data);

            DB::commit();
            return $this->redirectWithMessage(RedirectType::CREATE->value, 'admin.cms.banners.index');
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            DB::rollBack();
            return $this->redirectWithMessage(RedirectType::ERROR->value, 'admin.cms.banners.index');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        checkAdminHasPermissionAndThrowException('cms.banners.edit');
        $banner = PromotionalBanner::findOrFail($id);
        $positions = ['home_large', 'home_small', 'home_full', 'sidebar', 'popup'];
        return view('cms::admin.banners.edit', compact('banner', 'positions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        checkAdminHasPermissionAndThrowException('cms.banners.edit');

        $request->validate([
            'position' => 'required|string',
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:2048',
            'background_image' => 'nullable|image|max:2048',
        ]);

        DB::beginTransaction();
        try {
            $banner = PromotionalBanner::findOrFail($id);
            $data = $request->only(['position', 'title', 'subtitle', 'button_text', 'button_link', 'badge_text', 'start_date', 'end_date', 'is_active', 'sort_order']);

            if ($request->hasFile('image')) {
                if ($banner->image && Storage::disk('public')->exists(str_replace('storage/', '', $banner->image))) {
                    Storage::disk('public')->delete(str_replace('storage/', '', $banner->image));
                }
                $data['image'] = 'storage/' . $request->file('image')->store('cms/banners', 'public');
            }

            if ($request->hasFile('background_image')) {
                if ($banner->background_image && Storage::disk('public')->exists(str_replace('storage/', '', $banner->background_image))) {
                    Storage::disk('public')->delete(str_replace('storage/', '', $banner->background_image));
                }
                $data['background_image'] = 'storage/' . $request->file('background_image')->store('cms/banners', 'public');
            }

            $banner->update($data);

            DB::commit();
            return $this->redirectWithMessage(RedirectType::UPDATE->value, 'admin.cms.banners.index');
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            DB::rollBack();
            return $this->redirectWithMessage(RedirectType::ERROR->value, 'admin.cms.banners.index');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        checkAdminHasPermissionAndThrowException('cms.banners.delete');

        try {
            $banner = PromotionalBanner::findOrFail($id);

            if ($banner->image && Storage::disk('public')->exists(str_replace('storage/', '', $banner->image))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $banner->image));
            }
            if ($banner->background_image && Storage::disk('public')->exists(str_replace('storage/', '', $banner->background_image))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $banner->background_image));
            }

            $banner->delete();

            return $this->redirectWithMessage(RedirectType::DELETE->value, 'admin.cms.banners.index');
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return $this->redirectWithMessage(RedirectType::ERROR->value, 'admin.cms.banners.index');
        }
    }

    /**
     * Toggle status.
     */
    public function toggleStatus(string $id)
    {
        checkAdminHasPermissionAndThrowException('cms.banners.edit');

        try {
            $banner = PromotionalBanner::findOrFail($id);
            $banner->is_active = !$banner->is_active;
            $banner->save();

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully',
                'status' => $banner->is_active
            ]);
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return response()->json(['message' => 'Something went wrong'], 500);
        }
    }
}
