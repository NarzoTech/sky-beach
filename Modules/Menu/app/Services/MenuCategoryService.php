<?php

namespace Modules\Menu\app\Services;

use Modules\Menu\app\Models\MenuCategory;
use Illuminate\Support\Str;

class MenuCategoryService
{
    private $category;

    public function __construct(MenuCategory $category)
    {
        $this->category = $category;
    }

    public function getAllCategories()
    {
        $category = $this->category->with('parent');

        if (request()->keyword) {
            $category = $category->where('name', 'like', '%' . request()->keyword . '%');
        }

        if (request()->status !== null && request()->status !== '') {
            $category = $category->where('status', request()->status);
        }

        if (request()->order_by) {
            $category = $category->orderBy('display_order', request()->order_by)->orderBy('name', request()->order_by);
        } else {
            $category = $category->orderBy('display_order', 'asc')->orderBy('name', 'asc');
        }

        if (request('par-page')) {
            $parpage = request('par-page') == 'all' ? null : request('par-page');
        } else {
            $parpage = 20;
        }

        $category = $category->paginate($parpage);
        $category->appends(request()->query());

        return $category;
    }

    public function getActiveCategories()
    {
        return $this->category->where('status', 1)->ordered()->get();
    }

    public function getActiveCategoriesWithItems()
    {
        return $this->category->where('status', 1)
            ->with(['activeMenuItems' => function ($query) {
                $query->ordered();
            }])
            ->ordered()
            ->get();
    }

    public function getParentCategories()
    {
        return $this->category->where('status', 1)
            ->whereNull('parent_id')
            ->ordered()
            ->get();
    }

    public function getCategoriesForSelect()
    {
        return $this->category->where('status', 1)->ordered()->get();
    }

    public function getFeaturedCategories()
    {
        return $this->category->where('status', 1)
            ->where('is_featured', 1)
            ->ordered()
            ->get();
    }

    public function storeCategory($request)
    {
        $data = $request->all();

        if ($request->hasFile('image')) {
            $data['image'] = file_upload($request->image);
        }

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        // Check for unique slug
        $data['slug'] = $this->generateUniqueSlug($data['slug']);

        $category = $this->category->create($data);

        // Handle translations if provided
        if ($request->has('translations')) {
            foreach ($request->translations as $locale => $translation) {
                $category->translations()->create([
                    'locale' => $locale,
                    'name' => $translation['name'] ?? $category->name,
                    'description' => $translation['description'] ?? $category->description,
                ]);
            }
        }

        return $category;
    }

    public function updateCategory($request, $id)
    {
        $category = $this->category->findOrFail($id);
        $data = $request->all();

        if ($request->hasFile('image')) {
            // Delete old image
            if ($category->image) {
                delete_file($category->image);
            }
            $data['image'] = file_upload($request->image);
        }

        if (!empty($data['slug']) && $data['slug'] !== $category->slug) {
            $data['slug'] = $this->generateUniqueSlug($data['slug'], $id);
        }

        $category->update($data);

        // Handle translations if provided
        if ($request->has('translations')) {
            foreach ($request->translations as $locale => $translation) {
                $category->translations()->updateOrCreate(
                    ['locale' => $locale],
                    [
                        'name' => $translation['name'] ?? $category->name,
                        'description' => $translation['description'] ?? $category->description,
                    ]
                );
            }
        }

        return $category;
    }

    public function deleteCategory($id)
    {
        $category = $this->category->findOrFail($id);

        // Check if category has menu items
        if ($category->menuItems()->count() > 0) {
            return false;
        }

        // Check if category has children
        if ($category->children()->count() > 0) {
            return false;
        }

        // Delete image
        if ($category->image) {
            delete_file($category->image);
        }

        return $category->delete();
    }

    public function getCategory($id)
    {
        return $this->category->with('translations')->findOrFail($id);
    }

    public function getCategoryBySlug($slug)
    {
        return $this->category->where('slug', $slug)
            ->where('status', 1)
            ->with(['activeMenuItems.variants', 'activeMenuItems.addons'])
            ->first();
    }

    public function deleteAll($request)
    {
        $ids = $request->ids;
        foreach ($ids as $id) {
            $category = $this->category->find($id);
            if ($category && $category->menuItems()->count() == 0 && $category->children()->count() == 0) {
                if ($category->image) {
                    delete_file($category->image);
                }
                $category->delete();
            }
        }
    }

    public function updateStatus($id)
    {
        $category = $this->category->findOrFail($id);
        $category->status = !$category->status;
        $category->save();
        return $category;
    }

    private function generateUniqueSlug($slug, $excludeId = null)
    {
        $originalSlug = $slug;
        $count = 1;

        while (true) {
            $query = $this->category->where('slug', $slug);
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }

            if (!$query->exists()) {
                break;
            }

            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }
}
