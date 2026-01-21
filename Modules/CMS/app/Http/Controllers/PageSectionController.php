<?php

namespace Modules\CMS\app\Http\Controllers;

use App\Enums\RedirectType;
use App\Http\Controllers\Controller;
use App\Traits\RedirectHelperTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Modules\CMS\app\Models\PageSection;

class PageSectionController extends Controller
{
    use RedirectHelperTrait;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        checkAdminHasPermissionAndThrowException('cms.page-sections.view');

        $page = $request->get('page');
        $pages = PageSection::select('page')->distinct()->pluck('page');

        $query = PageSection::orderBy('page')->orderBy('sort_order');
        if ($page) {
            $query->where('page', $page);
        }

        $sections = $query->paginate(20);

        return view('cms::admin.page-sections.index', compact('sections', 'pages', 'page'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        checkAdminHasPermissionAndThrowException('cms.page-sections.create');
        $pages = ['home', 'about', 'contact', 'reservation', 'catering', 'service'];
        return view('cms::admin.page-sections.create', compact('pages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        checkAdminHasPermissionAndThrowException('cms.page-sections.create');

        $request->validate([
            'page' => 'required|string',
            'section_key' => 'required|string',
            'title' => 'nullable|string|max:500',
            'subtitle' => 'nullable|string|max:500',
            'content' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'background_image' => 'nullable|image|max:2048',
        ]);

        DB::beginTransaction();
        try {
            $data = $request->only(['page', 'section_key', 'title', 'subtitle', 'content', 'button_text', 'button_link', 'extra_data', 'sort_order', 'is_active']);

            if ($request->hasFile('image')) {
                $data['image'] = 'storage/' . $request->file('image')->store('cms/sections', 'public');
            }

            if ($request->hasFile('background_image')) {
                $data['background_image'] = 'storage/' . $request->file('background_image')->store('cms/sections', 'public');
            }

            if ($request->extra_data) {
                $data['extra_data'] = json_decode($request->extra_data, true);
            }

            PageSection::create($data);

            DB::commit();
            return $this->redirectWithMessage(RedirectType::CREATE->value, 'admin.cms.page-sections.index');
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            DB::rollBack();
            return $this->redirectWithMessage(RedirectType::ERROR->value, 'admin.cms.page-sections.index');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        checkAdminHasPermissionAndThrowException('cms.page-sections.edit');
        $section = PageSection::findOrFail($id);
        $pages = ['home', 'about', 'contact', 'reservation', 'catering', 'service'];
        return view('cms::admin.page-sections.edit', compact('section', 'pages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        checkAdminHasPermissionAndThrowException('cms.page-sections.edit');

        $request->validate([
            'page' => 'required|string',
            'section_key' => 'required|string',
            'title' => 'nullable|string|max:500',
            'subtitle' => 'nullable|string|max:500',
            'content' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'background_image' => 'nullable|image|max:2048',
        ]);

        DB::beginTransaction();
        try {
            $section = PageSection::findOrFail($id);
            $data = $request->only(['page', 'section_key', 'title', 'subtitle', 'content', 'button_text', 'button_link', 'sort_order', 'is_active']);

            if ($request->hasFile('image')) {
                // Delete old image
                if ($section->image && Storage::disk('public')->exists(str_replace('storage/', '', $section->image))) {
                    Storage::disk('public')->delete(str_replace('storage/', '', $section->image));
                }
                $data['image'] = 'storage/' . $request->file('image')->store('cms/sections', 'public');
            }

            if ($request->hasFile('background_image')) {
                // Delete old background image
                if ($section->background_image && Storage::disk('public')->exists(str_replace('storage/', '', $section->background_image))) {
                    Storage::disk('public')->delete(str_replace('storage/', '', $section->background_image));
                }
                $data['background_image'] = 'storage/' . $request->file('background_image')->store('cms/sections', 'public');
            }

            if ($request->has('extra_data') && $request->extra_data) {
                $data['extra_data'] = json_decode($request->extra_data, true);
            }

            $section->update($data);

            // Clear cache
            Cache::forget("cms_section_{$section->page}_{$section->section_key}");
            Cache::forget("cms_page_sections_{$section->page}");

            DB::commit();
            return $this->redirectWithMessage(RedirectType::UPDATE->value, 'admin.cms.page-sections.index');
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            DB::rollBack();
            return $this->redirectWithMessage(RedirectType::ERROR->value, 'admin.cms.page-sections.index');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        checkAdminHasPermissionAndThrowException('cms.page-sections.delete');

        try {
            $section = PageSection::findOrFail($id);

            // Delete images
            if ($section->image && Storage::disk('public')->exists(str_replace('storage/', '', $section->image))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $section->image));
            }
            if ($section->background_image && Storage::disk('public')->exists(str_replace('storage/', '', $section->background_image))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $section->background_image));
            }

            $section->delete();

            return $this->redirectWithMessage(RedirectType::DELETE->value, 'admin.cms.page-sections.index');
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return $this->redirectWithMessage(RedirectType::ERROR->value, 'admin.cms.page-sections.index');
        }
    }

    /**
     * Toggle status.
     */
    public function toggleStatus(string $id)
    {
        checkAdminHasPermissionAndThrowException('cms.page-sections.edit');

        try {
            $section = PageSection::findOrFail($id);
            $section->is_active = !$section->is_active;
            $section->save();

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully',
                'status' => $section->is_active
            ]);
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return response()->json(['message' => 'Something went wrong'], 500);
        }
    }
}
