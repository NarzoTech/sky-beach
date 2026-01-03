<?php

namespace Modules\Menu\app\Services;

use Modules\Menu\app\Models\MenuItem;
use Modules\Menu\app\Models\MenuVariant;
use Modules\Menu\app\Models\Recipe;
use Illuminate\Support\Str;

class MenuItemService
{
    private $menuItem;

    public function __construct(MenuItem $menuItem)
    {
        $this->menuItem = $menuItem;
    }

    public function getAllItems()
    {
        $items = $this->menuItem->with(['category', 'variants']);

        if (request()->keyword) {
            $items = $items->where(function ($query) {
                $query->where('name', 'like', '%' . request()->keyword . '%')
                    ->orWhere('sku', 'like', '%' . request()->keyword . '%');
            });
        }

        if (request()->category_id) {
            $items = $items->where('category_id', request()->category_id);
        }

        if (request()->status !== null && request()->status !== '') {
            $items = $items->where('status', request()->status);
        }

        if (request()->availability !== null && request()->availability !== '') {
            $items = $items->where('is_available', request()->availability);
        }

        if (request()->order_by) {
            $items = $items->orderBy('display_order', request()->order_by)->orderBy('name', request()->order_by);
        } else {
            $items = $items->orderBy('display_order', 'asc')->orderBy('name', 'asc');
        }

        if (request('par-page')) {
            $parpage = request('par-page') == 'all' ? null : request('par-page');
        } else {
            $parpage = 20;
        }

        $items = $items->paginate($parpage);
        $items->appends(request()->query());

        return $items;
    }

    public function getActiveItems()
    {
        return $this->menuItem->where('status', 1)
            ->where('is_available', 1)
            ->ordered()
            ->get();
    }

    public function getFeaturedItems($limit = 8)
    {
        return $this->menuItem->where('status', 1)
            ->where('is_available', 1)
            ->where('is_featured', 1)
            ->ordered()
            ->limit($limit)
            ->get();
    }

    public function getItemsByCategory($categoryId)
    {
        return $this->menuItem->where('status', 1)
            ->where('is_available', 1)
            ->where('category_id', $categoryId)
            ->with(['variants', 'addons'])
            ->ordered()
            ->get();
    }

    public function storeItem($request)
    {
        $data = $request->all();

        if ($request->hasFile('image')) {
            $data['image'] = file_upload($request->image);
        }

        // Handle gallery images
        if ($request->hasFile('gallery')) {
            $gallery = [];
            foreach ($request->file('gallery') as $image) {
                $gallery[] = file_upload($image);
            }
            $data['gallery'] = $gallery;
        }

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $data['slug'] = $this->generateUniqueSlug($data['slug']);

        // Handle allergens
        if (!empty($data['allergens'])) {
            $data['allergens'] = is_array($data['allergens']) ? $data['allergens'] : explode(',', $data['allergens']);
        }

        // Set cost_price to 0 by default (will be calculated from ingredients/recipes)
        $data['cost_price'] = 0;

        // Remove recipes from data as we'll handle it separately
        $recipes = $data['recipes'] ?? [];
        unset($data['recipes']);

        $item = $this->menuItem->create($data);

        // Handle recipes/ingredients
        if (!empty($recipes)) {
            foreach ($recipes as $recipe) {
                $ingredientId = $recipe['ingredient_id'] ?? $recipe['product_id'] ?? null;
                if (!empty($ingredientId) && !empty($recipe['quantity_required'])) {
                    $item->recipes()->create([
                        'ingredient_id' => $ingredientId,
                        'quantity_required' => $recipe['quantity_required'],
                        'unit_id' => $recipe['unit_id'] ?? null,
                        'notes' => $recipe['notes'] ?? null,
                    ]);
                }
            }
            // Update cost price based on recipe
            $item->cost_price = $item->calculateCostFromRecipe();
            $item->save();
        }

        // Handle translations
        if ($request->has('translations')) {
            foreach ($request->translations as $locale => $translation) {
                $item->translations()->create([
                    'locale' => $locale,
                    'name' => $translation['name'] ?? $item->name,
                    'short_description' => $translation['short_description'] ?? $item->short_description,
                    'long_description' => $translation['long_description'] ?? $item->long_description,
                ]);
            }
        }

        return $item;
    }

    public function updateItem($request, $id)
    {
        $item = $this->menuItem->findOrFail($id);
        $data = $request->all();

        if ($request->hasFile('image')) {
            if ($item->image) {
                delete_file($item->image);
            }
            $data['image'] = file_upload($request->image);
        }

        // Handle gallery images
        if ($request->hasFile('gallery')) {
            // Delete old gallery images
            if ($item->gallery && is_array($item->gallery)) {
                foreach ($item->gallery as $oldImage) {
                    delete_file($oldImage);
                }
            }
            $gallery = [];
            foreach ($request->file('gallery') as $image) {
                $gallery[] = file_upload($image);
            }
            $data['gallery'] = $gallery;
        }

        if (!empty($data['slug']) && $data['slug'] !== $item->slug) {
            $data['slug'] = $this->generateUniqueSlug($data['slug'], $id);
        }

        // Handle allergens
        if (!empty($data['allergens'])) {
            $data['allergens'] = is_array($data['allergens']) ? $data['allergens'] : explode(',', $data['allergens']);
        } else {
            $data['allergens'] = [];
        }

        // Handle recipes/ingredients
        $recipes = $data['recipes'] ?? [];
        unset($data['recipes']);

        $item->update($data);

        // Update recipes - delete existing and recreate
        if (isset($request->recipes) || $request->has('recipes')) {
            $item->recipes()->delete();

            foreach ($recipes as $recipe) {
                $ingredientId = $recipe['ingredient_id'] ?? $recipe['product_id'] ?? null;
                if (!empty($ingredientId) && !empty($recipe['quantity_required'])) {
                    $item->recipes()->create([
                        'ingredient_id' => $ingredientId,
                        'quantity_required' => $recipe['quantity_required'],
                        'unit_id' => $recipe['unit_id'] ?? null,
                        'notes' => $recipe['notes'] ?? null,
                    ]);
                }
            }

            // Update cost price based on recipe
            $item->cost_price = $item->calculateCostFromRecipe();
            $item->save();
        }

        // Handle translations
        if ($request->has('translations')) {
            foreach ($request->translations as $locale => $translation) {
                $item->translations()->updateOrCreate(
                    ['locale' => $locale],
                    [
                        'name' => $translation['name'] ?? $item->name,
                        'short_description' => $translation['short_description'] ?? $item->short_description,
                        'long_description' => $translation['long_description'] ?? $item->long_description,
                    ]
                );
            }
        }

        return $item;
    }

    public function deleteItem($id)
    {
        $item = $this->menuItem->findOrFail($id);

        // Delete image
        if ($item->image) {
            delete_file($item->image);
        }

        // Delete gallery
        if ($item->gallery && is_array($item->gallery)) {
            foreach ($item->gallery as $image) {
                delete_file($image);
            }
        }

        return $item->delete();
    }

    public function getItem($id)
    {
        return $this->menuItem->with(['category', 'variants', 'addons', 'recipes.ingredient', 'translations'])->findOrFail($id);
    }

    public function getItemBySlug($slug)
    {
        return $this->menuItem->where('slug', $slug)
            ->where('status', 1)
            ->with(['category', 'activeVariants', 'activeAddons', 'translations'])
            ->first();
    }

    public function deleteAll($request)
    {
        $ids = $request->ids;
        foreach ($ids as $id) {
            $this->deleteItem($id);
        }
    }

    public function toggleStatus($id)
    {
        $item = $this->menuItem->findOrFail($id);
        $item->status = !$item->status;
        $item->save();
        return $item;
    }

    public function toggleAvailability($id)
    {
        $item = $this->menuItem->findOrFail($id);
        $item->is_available = !$item->is_available;
        $item->save();
        return $item;
    }

    public function toggleFeatured($id)
    {
        $item = $this->menuItem->findOrFail($id);
        $item->is_featured = !$item->is_featured;
        $item->save();
        return $item;
    }

    // Variant Management
    public function addVariant($menuItemId, $data)
    {
        $item = $this->menuItem->findOrFail($menuItemId);
        return $item->variants()->create($data);
    }

    public function updateVariant($variantId, $data)
    {
        $variant = MenuVariant::findOrFail($variantId);
        $variant->update($data);
        return $variant;
    }

    public function deleteVariant($variantId)
    {
        return MenuVariant::destroy($variantId);
    }

    // Addon Management
    public function syncAddons($menuItemId, $addons)
    {
        $item = $this->menuItem->findOrFail($menuItemId);

        $syncData = [];
        foreach ($addons as $addon) {
            $syncData[$addon['addon_id']] = [
                'max_quantity' => $addon['max_quantity'] ?? 1,
                'is_required' => $addon['is_required'] ?? false,
            ];
        }

        $item->addons()->sync($syncData);
        return $item;
    }

    // Recipe Management
    public function saveRecipe($menuItemId, $recipes)
    {
        $item = $this->menuItem->findOrFail($menuItemId);

        // Delete existing recipes
        $item->recipes()->delete();

        // Add new recipes
        foreach ($recipes as $recipe) {
            $ingredientId = $recipe['ingredient_id'] ?? $recipe['product_id'] ?? null;
            if (!empty($ingredientId) && !empty($recipe['quantity_required'])) {
                $item->recipes()->create([
                    'ingredient_id' => $ingredientId,
                    'quantity_required' => $recipe['quantity_required'],
                    'unit_id' => $recipe['unit_id'] ?? null,
                    'notes' => $recipe['notes'] ?? null,
                ]);
            }
        }

        // Update cost price based on recipe
        $item->cost_price = $item->calculateCostFromRecipe();
        $item->save();

        return $item;
    }

    public function searchItems($query, $limit = 10)
    {
        return $this->menuItem->where('status', 1)
            ->where('is_available', 1)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', '%' . $query . '%')
                    ->orWhere('short_description', 'like', '%' . $query . '%');
            })
            ->with(['category', 'variants'])
            ->limit($limit)
            ->get();
    }

    private function generateUniqueSlug($slug, $excludeId = null)
    {
        $originalSlug = $slug;
        $count = 1;

        while (true) {
            $query = $this->menuItem->where('slug', $slug);
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
