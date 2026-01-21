<?php

namespace Modules\CMS\app\Http\Controllers;

use App\Enums\RedirectType;
use App\Http\Controllers\Controller;
use App\Traits\RedirectHelperTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Modules\CMS\app\Models\GalleryImage;

class GalleryImageController extends Controller
{
    use RedirectHelperTrait;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        checkAdminHasPermissionAndThrowException('cms.gallery.view');

        $query = GalleryImage::orderBy('category')->orderBy('sort_order');

        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }

        if ($request->has('page_filter') && $request->page_filter) {
            $query->where('page', $request->page_filter);
        }

        $images = $query->paginate(20);
        $categories = GalleryImage::select('category')->distinct()->pluck('category');
        $pages = GalleryImage::select('page')->distinct()->whereNotNull('page')->pluck('page');

        return view('cms::admin.gallery.index', compact('images', 'categories', 'pages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        checkAdminHasPermissionAndThrowException('cms.gallery.create');
        $categories = ['about_story', 'about_showcase', 'home', 'menu', 'catering'];
        $pages = ['home', 'about', 'contact', 'reservation', 'catering', 'menu'];
        return view('cms::admin.gallery.create', compact('categories', 'pages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        checkAdminHasPermissionAndThrowException('cms.gallery.create');

        $request->validate([
            'title' => 'nullable|string|max:255',
            'image' => 'required|image|max:2048',
            'category' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            $data = $request->only(['title', 'category', 'page', 'alt_text', 'is_active', 'sort_order']);

            if ($request->hasFile('image')) {
                $data['image'] = 'storage/' . $request->file('image')->store('cms/gallery', 'public');
            }

            GalleryImage::create($data);

            DB::commit();
            return $this->redirectWithMessage(RedirectType::CREATE->value, 'admin.cms.gallery.index');
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            DB::rollBack();
            return $this->redirectWithMessage(RedirectType::ERROR->value, 'admin.cms.gallery.index');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        checkAdminHasPermissionAndThrowException('cms.gallery.edit');
        $image = GalleryImage::findOrFail($id);
        $categories = ['about_story', 'about_showcase', 'home', 'menu', 'catering'];
        $pages = ['home', 'about', 'contact', 'reservation', 'catering', 'menu'];
        return view('cms::admin.gallery.edit', compact('image', 'categories', 'pages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        checkAdminHasPermissionAndThrowException('cms.gallery.edit');

        $request->validate([
            'title' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:2048',
            'category' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            $galleryImage = GalleryImage::findOrFail($id);
            $data = $request->only(['title', 'category', 'page', 'alt_text', 'is_active', 'sort_order']);

            if ($request->hasFile('image')) {
                if ($galleryImage->image && Storage::disk('public')->exists(str_replace('storage/', '', $galleryImage->image))) {
                    Storage::disk('public')->delete(str_replace('storage/', '', $galleryImage->image));
                }
                $data['image'] = 'storage/' . $request->file('image')->store('cms/gallery', 'public');
            }

            $galleryImage->update($data);

            DB::commit();
            return $this->redirectWithMessage(RedirectType::UPDATE->value, 'admin.cms.gallery.index');
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            DB::rollBack();
            return $this->redirectWithMessage(RedirectType::ERROR->value, 'admin.cms.gallery.index');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        checkAdminHasPermissionAndThrowException('cms.gallery.delete');

        try {
            $image = GalleryImage::findOrFail($id);

            if ($image->image && Storage::disk('public')->exists(str_replace('storage/', '', $image->image))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $image->image));
            }

            $image->delete();

            return $this->redirectWithMessage(RedirectType::DELETE->value, 'admin.cms.gallery.index');
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return $this->redirectWithMessage(RedirectType::ERROR->value, 'admin.cms.gallery.index');
        }
    }

    /**
     * Toggle status.
     */
    public function toggleStatus(string $id)
    {
        checkAdminHasPermissionAndThrowException('cms.gallery.edit');

        try {
            $image = GalleryImage::findOrFail($id);
            $image->is_active = !$image->is_active;
            $image->save();

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully',
                'status' => $image->is_active
            ]);
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return response()->json(['message' => 'Something went wrong'], 500);
        }
    }

    /**
     * Delete multiple images.
     */
    public function deleteAll(Request $request)
    {
        checkAdminHasPermissionAndThrowException('cms.gallery.delete');

        try {
            $ids = $request->ids;
            $images = GalleryImage::whereIn('id', $ids)->get();

            foreach ($images as $image) {
                if ($image->image && Storage::disk('public')->exists(str_replace('storage/', '', $image->image))) {
                    Storage::disk('public')->delete(str_replace('storage/', '', $image->image));
                }
            }

            GalleryImage::whereIn('id', $ids)->delete();

            return response()->json(['success' => true, 'message' => 'Deleted successfully'], 200);
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return response()->json(['message' => 'Something went wrong'], 500);
        }
    }
}
