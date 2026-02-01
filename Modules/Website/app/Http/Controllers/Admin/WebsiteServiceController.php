<?php

namespace Modules\Website\app\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Website\app\Models\WebsiteService;
use Illuminate\Support\Str;

class WebsiteServiceController extends Controller
{
    public function index()
    {
        $services = WebsiteService::orderBy('order')->paginate(20);
        return view('website::admin.services.index', compact('services'));
    }

    public function create()
    {
        return view('website::admin.services.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:website_services,slug',
            'short_description' => 'nullable|string',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:2048',
            'price' => 'nullable|numeric|min:0',
            'duration' => 'nullable|integer|min:0',
            'order' => 'nullable|integer|min:0',
            'is_featured' => 'nullable|boolean',
            'status' => 'nullable|boolean',
        ]);

        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['title']);

        if ($request->hasFile('image')) {
            $validated['image'] = upload_image($request->file('image'), 'services');
        }

        WebsiteService::create($validated);

        return redirect()->route('admin.restaurant.website-services.index')
            ->with('success', 'Service created successfully');
    }

    public function edit(WebsiteService $websiteService)
    {
        return view('website::admin.services.edit', compact('websiteService'));
    }

    public function update(Request $request, WebsiteService $websiteService)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:website_services,slug,' . $websiteService->id,
            'short_description' => 'nullable|string',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:2048',
            'price' => 'nullable|numeric|min:0',
            'duration' => 'nullable|integer|min:0',
            'order' => 'nullable|integer|min:0',
            'is_featured' => 'nullable|boolean',
            'status' => 'nullable|boolean',
        ]);

        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['title']);

        if ($request->hasFile('image')) {
            $validated['image'] = upload_image($request->file('image'), 'services', $websiteService->image);
        }

        $websiteService->update($validated);

        return redirect()->route('admin.restaurant.website-services.index')
            ->with('success', 'Service updated successfully');
    }

    public function destroy(WebsiteService $websiteService)
    {
        delete_image($websiteService->image);
        $websiteService->delete();
        return redirect()->route('admin.restaurant.website-services.index')
            ->with('success', 'Service deleted successfully');
    }
}
