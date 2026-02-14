<?php

namespace Modules\Menu\app\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\Menu\app\Models\AddonRecipe;
use Modules\Menu\app\Models\MenuAddon;

class MenuAddonService
{
    /**
     * Get all addons with optional filtering
     */
    public function getAll(array $filters = [])
    {
        $query = MenuAddon::query();

        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        if (isset($filters['status']) && $filters['status'] !== '') {
            $query->where('status', $filters['status']);
        }

        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);

        $perPage = $filters['per_page'] ?? 15;

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * Get all active addons
     */
    public function getActive()
    {
        return MenuAddon::active()->orderBy('name')->get();
    }

    /**
     * Get addons not attached to a specific menu item
     */
    public function getAvailableForItem(int $menuItemId)
    {
        $attachedIds = \Modules\Menu\app\Models\MenuItem::find($menuItemId)
            ->addons()
            ->pluck('menu_addons.id')
            ->toArray();

        return MenuAddon::active()
            ->whereNotIn('id', $attachedIds)
            ->orderBy('name')
            ->get();
    }

    /**
     * Create a new addon
     */
    public function create(array $data): MenuAddon
    {
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $data['image'] = $this->uploadImage($data['image']);
        }

        $recipes = $data['recipes'] ?? [];
        unset($data['recipes']);

        $addon = MenuAddon::create($data);

        $this->syncRecipes($addon, $recipes);

        return $addon;
    }

    /**
     * Update an addon
     */
    public function update(MenuAddon $addon, array $data): MenuAddon
    {
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            // Delete old image
            if ($addon->image) {
                Storage::disk('public')->delete($addon->image);
            }
            $data['image'] = $this->uploadImage($data['image']);
        }

        $recipes = $data['recipes'] ?? [];
        unset($data['recipes']);

        $addon->update($data);

        $this->syncRecipes($addon, $recipes);

        return $addon->fresh();
    }

    /**
     * Delete an addon
     */
    public function delete(MenuAddon $addon): bool
    {
        // Check if addon is attached to any items
        if ($addon->menuItems()->count() > 0) {
            throw new \Exception('Cannot delete addon that is attached to menu items.');
        }

        // Delete image
        if ($addon->image) {
            Storage::disk('public')->delete($addon->image);
        }

        return $addon->delete();
    }

    /**
     * Bulk delete addons
     */
    public function bulkDelete(array $ids): int
    {
        $deleted = 0;
        foreach ($ids as $id) {
            $addon = MenuAddon::find($id);
            if ($addon && $addon->menuItems()->count() === 0) {
                if ($addon->image) {
                    Storage::disk('public')->delete($addon->image);
                }
                $addon->delete();
                $deleted++;
            }
        }
        return $deleted;
    }

    /**
     * Toggle addon status
     */
    public function toggleStatus(MenuAddon $addon): MenuAddon
    {
        $addon->update(['status' => !$addon->status]);
        return $addon->fresh();
    }

    /**
     * Sync addon recipes - delete existing and recreate
     */
    private function syncRecipes(MenuAddon $addon, array $recipes): void
    {
        $addon->recipes()->delete();

        foreach ($recipes as $recipe) {
            $ingredientId = $recipe['ingredient_id'] ?? null;
            if (!empty($ingredientId) && !empty($recipe['quantity_required'])) {
                $addon->recipes()->create([
                    'ingredient_id' => $ingredientId,
                    'quantity_required' => $recipe['quantity_required'],
                    'unit_id' => $recipe['unit_id'] ?? null,
                ]);
            }
        }
    }

    /**
     * Upload addon image
     */
    protected function uploadImage(UploadedFile $file): string
    {
        $filename = 'addon_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        return $file->storeAs('menu/addons', $filename, 'public');
    }

    /**
     * Attach addon to menu item
     */
    public function attachToMenuItem(int $menuItemId, int $addonId, array $pivotData = []): void
    {
        $menuItem = \Modules\Menu\app\Models\MenuItem::findOrFail($menuItemId);

        $pivotData = array_merge([
            'max_quantity' => 5,
            'is_required' => false,
        ], $pivotData);

        $menuItem->addons()->attach($addonId, $pivotData);
    }

    /**
     * Update addon pivot data for menu item
     */
    public function updateMenuItemAddon(int $menuItemId, int $addonId, array $pivotData): void
    {
        $menuItem = \Modules\Menu\app\Models\MenuItem::findOrFail($menuItemId);
        $menuItem->addons()->updateExistingPivot($addonId, $pivotData);
    }

    /**
     * Detach addon from menu item
     */
    public function detachFromMenuItem(int $menuItemId, int $addonId): void
    {
        $menuItem = \Modules\Menu\app\Models\MenuItem::findOrFail($menuItemId);
        $menuItem->addons()->detach($addonId);
    }
}
