<?php

namespace Modules\Menu\app\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Modules\Menu\app\Models\Combo;
use Modules\Menu\app\Models\ComboItem;
use Modules\Menu\app\Models\MenuItem;

class ComboService
{
    /**
     * Get all combos with optional filtering
     */
    public function getAll(array $filters = [])
    {
        $query = Combo::with(['items.menuItem', 'items.variant']);

        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        if (isset($filters['status']) && $filters['status'] !== '') {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $query->where('is_active', $filters['is_active']);
        }

        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);

        $perPage = $filters['per_page'] ?? 15;

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * Get all active combos
     */
    public function getActiveCombos()
    {
        return Combo::where('status', 1)
            ->where('is_active', 1)
            ->where(function ($query) {
                $query->whereNull('start_date')
                    ->orWhere('start_date', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>=', now());
            })
            ->with(['items.menuItem', 'items.variant'])
            ->orderBy('display_order')
            ->get();
    }

    /**
     * Get a single combo
     */
    public function getCombo(int $id): Combo
    {
        return Combo::with(['items.menuItem', 'items.variant'])->findOrFail($id);
    }

    /**
     * Get combo by slug
     */
    public function getComboBySlug(string $slug): Combo
    {
        return Combo::with(['items.menuItem', 'items.variant'])
            ->where('slug', $slug)
            ->firstOrFail();
    }

    /**
     * Create a new combo
     */
    public function create(array $data): Combo
    {
        // Generate slug
        if (empty($data['slug'])) {
            $data['slug'] = $this->generateUniqueSlug($data['name']);
        }

        // Handle image upload
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $data['image'] = file_upload($data['image'], 'uploads/combos/');
        }

        // Handle gallery upload
        if (isset($data['gallery']) && is_array($data['gallery'])) {
            $gallery = [];
            foreach ($data['gallery'] as $image) {
                if ($image instanceof UploadedFile) {
                    $gallery[] = file_upload($image, 'uploads/combos/');
                }
            }
            $data['gallery'] = $gallery;
        }

        // Calculate original price if items are provided
        $combo = Combo::create($data);

        // Sync items if provided
        if (!empty($data['items'])) {
            $this->syncItems($combo, $data['items']);
            $this->recalculatePrices($combo);
        }

        return $combo->fresh(['items.menuItem', 'items.variant']);
    }

    /**
     * Update a combo
     */
    public function update(Combo $combo, array $data): Combo
    {
        // Generate slug if name changed and slug is empty
        if (!empty($data['name']) && $data['name'] !== $combo->name && empty($data['slug'])) {
            $data['slug'] = $this->generateUniqueSlug($data['name'], $combo->id);
        }

        // Handle image upload
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $data['image'] = file_upload($data['image'], 'uploads/combos/', $combo->image);
        }

        // Handle gallery upload
        if (isset($data['gallery']) && is_array($data['gallery'])) {
            // Delete old gallery images
            if ($combo->gallery && is_array($combo->gallery)) {
                foreach ($combo->gallery as $oldImage) {
                    delete_file($oldImage);
                }
            }
            $gallery = [];
            foreach ($data['gallery'] as $image) {
                if ($image instanceof UploadedFile) {
                    $gallery[] = file_upload($image, 'uploads/combos/');
                }
            }
            $data['gallery'] = $gallery;
        }

        $combo->update($data);

        // Sync items if provided
        if (isset($data['items'])) {
            $this->syncItems($combo, $data['items']);
            $this->recalculatePrices($combo);
        }

        return $combo->fresh(['items.menuItem', 'items.variant']);
    }

    /**
     * Delete a combo
     */
    public function delete(Combo $combo): bool
    {
        // Delete image
        if ($combo->image) {
            delete_file($combo->image);
        }

        // Delete gallery images
        if ($combo->gallery && is_array($combo->gallery)) {
            foreach ($combo->gallery as $image) {
                delete_file($image);
            }
        }

        // Delete items
        $combo->items()->delete();

        return $combo->delete();
    }

    /**
     * Bulk delete combos
     */
    public function bulkDelete(array $ids): int
    {
        $deleted = 0;
        foreach ($ids as $id) {
            $combo = Combo::find($id);
            if ($combo) {
                $this->delete($combo);
                $deleted++;
            }
        }
        return $deleted;
    }

    /**
     * Toggle combo status
     */
    public function toggleStatus(Combo $combo): Combo
    {
        $combo->update(['status' => !$combo->status]);
        return $combo->fresh();
    }

    /**
     * Toggle combo active status
     */
    public function toggleActive(Combo $combo): Combo
    {
        $combo->update(['is_active' => !$combo->is_active]);
        return $combo->fresh();
    }

    /**
     * Sync combo items
     */
    public function syncItems(Combo $combo, array $items): void
    {
        // Delete existing items
        $combo->items()->delete();

        // Add new items
        foreach ($items as $item) {
            if (empty($item['menu_item_id'])) continue;

            ComboItem::create([
                'combo_id' => $combo->id,
                'menu_item_id' => $item['menu_item_id'],
                'variant_id' => $item['variant_id'] ?? null,
                'quantity' => $item['quantity'] ?? 1,
            ]);
        }
    }

    /**
     * Recalculate combo prices
     */
    public function recalculatePrices(Combo $combo): Combo
    {
        $combo->load(['items.menuItem', 'items.variant']);

        $originalPrice = 0;
        foreach ($combo->items as $item) {
            $itemPrice = $item->menuItem->base_price ?? 0;
            if ($item->variant) {
                $itemPrice += $item->variant->price_adjustment ?? 0;
            }
            $originalPrice += $itemPrice * $item->quantity;
        }

        $combo->update(['original_price' => $originalPrice]);

        return $combo->fresh();
    }

    /**
     * Generate unique slug
     */
    protected function generateUniqueSlug(string $name, ?int $exceptId = null): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        while ($this->slugExists($slug, $exceptId)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Check if slug exists
     */
    protected function slugExists(string $slug, ?int $exceptId = null): bool
    {
        $query = Combo::where('slug', $slug);

        if ($exceptId) {
            $query->where('id', '!=', $exceptId);
        }

        return $query->exists();
    }
}
