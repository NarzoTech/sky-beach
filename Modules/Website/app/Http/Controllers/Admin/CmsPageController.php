<?php

namespace Modules\Website\app\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Website\app\Models\CmsPage;
use Illuminate\Support\Str;

class CmsPageController extends Controller
{
    public function index()
    {
        $pages = CmsPage::latest()->paginate(20);
        return view('website::admin.cms.index', compact('pages'));
    }

    public function create()
    {
        return view('website::admin.cms.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:cms_pages,slug',
            'content' => 'required|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'banner_image' => 'nullable|image|max:2048',
            'status' => 'nullable|boolean',
        ]);

        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['title']);

        if ($request->hasFile('banner_image')) {
            $validated['banner_image'] = upload_image($request->file('banner_image'), 'cms');
        }

        CmsPage::create($validated);

        return redirect()->route('admin.cms-pages.index')
            ->with('success', 'CMS Page created successfully');
    }

    public function edit(CmsPage $cmsPage)
    {
        return view('website::admin.cms.edit', compact('cmsPage'));
    }

    public function update(Request $request, CmsPage $cmsPage)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:cms_pages,slug,' . $cmsPage->id,
            'content' => 'required|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'banner_image' => 'nullable|image|max:2048',
            'status' => 'nullable|boolean',
        ]);

        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['title']);

        if ($request->hasFile('banner_image')) {
            $validated['banner_image'] = upload_image($request->file('banner_image'), 'cms', $cmsPage->banner_image);
        }

        $cmsPage->update($validated);

        return redirect()->route('admin.cms-pages.index')
            ->with('success', 'CMS Page updated successfully');
    }

    public function destroy(CmsPage $cmsPage)
    {
        delete_image($cmsPage->banner_image);
        $cmsPage->delete();
        return redirect()->route('admin.cms-pages.index')
            ->with('success', 'CMS Page deleted successfully');
    }
}
