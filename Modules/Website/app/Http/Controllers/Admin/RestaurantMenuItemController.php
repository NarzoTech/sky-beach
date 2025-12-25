<?php

namespace Modules\Website\app\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Website\app\Models\RestaurantMenuItem;
use Illuminate\Support\Str;

class RestaurantMenuItemController extends Controller
{
    public function index()
    {
        $menuItems = RestaurantMenuItem::orderBy('order')->paginate(20);
        return view('website::admin.menu-items.index', compact('menuItems'));
    }

    public function create()
    {
        return view('website::admin.menu-items.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:restaurant_menu_items,slug',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|max:2048',
            'category' => 'nullable|string|max:255',
            'cuisine_type' => 'nullable|string|max:255',
            'is_vegetarian' => 'nullable|boolean',
            'is_spicy' => 'nullable|boolean',
            'spice_level' => 'nullable|in:mild,medium,hot,extra hot',
            'ingredients' => 'nullable|string',
            'allergens' => 'nullable|string',
            'preparation_time' => 'nullable|integer|min:0',
            'calories' => 'nullable|integer|min:0',
            'available_in_pos' => 'nullable|boolean',
            'available_in_website' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'is_new' => 'nullable|boolean',
            'is_popular' => 'nullable|boolean',
            'order' => 'nullable|integer|min:0',
            'status' => 'nullable|boolean',
        ]);

        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['name']);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('menu-items', 'public');
        }

        RestaurantMenuItem::create($validated);

        return redirect()->route('admin.menu-items.index')
            ->with('success', 'Menu item created successfully');
    }

    public function edit(RestaurantMenuItem $menuItem)
    {
        return view('website::admin.menu-items.edit', compact('menuItem'));
    }

    public function update(Request $request, RestaurantMenuItem $menuItem)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:restaurant_menu_items,slug,' . $menuItem->id,
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|max:2048',
            'category' => 'nullable|string|max:255',
            'cuisine_type' => 'nullable|string|max:255',
            'is_vegetarian' => 'nullable|boolean',
            'is_spicy' => 'nullable|boolean',
            'spice_level' => 'nullable|in:mild,medium,hot,extra hot',
            'ingredients' => 'nullable|string',
            'allergens' => 'nullable|string',
            'preparation_time' => 'nullable|integer|min:0',
            'calories' => 'nullable|integer|min:0',
            'available_in_pos' => 'nullable|boolean',
            'available_in_website' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'is_new' => 'nullable|boolean',
            'is_popular' => 'nullable|boolean',
            'order' => 'nullable|integer|min:0',
            'status' => 'nullable|boolean',
        ]);

        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['name']);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('menu-items', 'public');
        }

        $menuItem->update($validated);

        return redirect()->route('admin.menu-items.index')
            ->with('success', 'Menu item updated successfully');
    }

    public function destroy(RestaurantMenuItem $menuItem)
    {
        $menuItem->delete();
        return redirect()->route('admin.menu-items.index')
            ->with('success', 'Menu item deleted successfully');
    }
}
