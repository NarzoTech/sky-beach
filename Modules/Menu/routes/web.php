<?php

use Illuminate\Support\Facades\Route;
use Modules\Menu\app\Http\Controllers\MenuCategoryController;
use Modules\Menu\app\Http\Controllers\MenuItemController;
use Modules\Menu\app\Http\Controllers\MenuAddonController;
use Modules\Menu\app\Http\Controllers\ComboController;
use Modules\Menu\app\Http\Controllers\BranchMenuController;
use Modules\Menu\app\Http\Controllers\Frontend\MenuPageController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Admin Routes
Route::group(['as' => 'admin.', 'prefix' => 'admin', 'middleware' => ['auth:admin', 'translation']], function () {

    // Menu Categories
    Route::post('menu-category/delete-all', [MenuCategoryController::class, 'deleteAll'])->name('menu-category.deleteSelected');
    Route::post('menu-category/toggle-status/{id}', [MenuCategoryController::class, 'toggleStatus'])->name('menu-category.toggle-status');
    Route::resource('menu-category', MenuCategoryController::class);

    // Menu Items
    Route::post('menu-item/delete-all', [MenuItemController::class, 'deleteAll'])->name('menu-item.deleteSelected');
    Route::post('menu-item/toggle-status/{id}', [MenuItemController::class, 'toggleStatus'])->name('menu-item.toggle-status');
    Route::post('menu-item/toggle-availability/{id}', [MenuItemController::class, 'toggleAvailability'])->name('menu-item.toggle-availability');
    Route::post('menu-item/toggle-featured/{id}', [MenuItemController::class, 'toggleFeatured'])->name('menu-item.toggle-featured');
    Route::get('menu-item/{id}/variants', [MenuItemController::class, 'manageVariants'])->name('menu-item.variants');
    Route::post('menu-item/{id}/variants', [MenuItemController::class, 'storeVariant'])->name('menu-item.variants.store');
    Route::put('menu-item/{menuItemId}/variants/{variantId}', [MenuItemController::class, 'updateVariant'])->name('menu-item.variants.update');
    Route::delete('menu-item/{menuItemId}/variants/{variantId}', [MenuItemController::class, 'deleteVariant'])->name('menu-item.variants.delete');
    Route::get('menu-item/{id}/addons', [MenuItemController::class, 'manageAddons'])->name('menu-item.addons');
    Route::post('menu-item/{id}/addons', [MenuItemController::class, 'attachAddon'])->name('menu-item.addons.attach');
    Route::put('menu-item/{menuItemId}/addons/{addonId}', [MenuItemController::class, 'updateAddon'])->name('menu-item.addons.update');
    Route::delete('menu-item/{menuItemId}/addons/{addonId}', [MenuItemController::class, 'detachAddon'])->name('menu-item.addons.detach');
    Route::get('menu-item/{id}/recipe', [MenuItemController::class, 'manageRecipe'])->name('menu-item.recipe');
    Route::post('menu-item/{id}/recipe', [MenuItemController::class, 'saveRecipe'])->name('menu-item.recipe.save');
    Route::resource('menu-item', MenuItemController::class);

    // Menu Add-ons
    Route::post('menu-addon/bulk-delete', [MenuAddonController::class, 'bulkDelete'])->name('menu-addon.bulk-delete');
    Route::post('menu-addon/{menuAddon}/toggle-status', [MenuAddonController::class, 'toggleStatus'])->name('menu-addon.toggle-status');
    Route::resource('menu-addon', MenuAddonController::class);

    // Combo Deals
    Route::post('combo/delete-all', [ComboController::class, 'deleteAll'])->name('combo.deleteSelected');
    Route::post('combo/{combo}/toggle-status', [ComboController::class, 'toggleStatus'])->name('combo.toggle-status');
    Route::post('combo/{combo}/toggle-active', [ComboController::class, 'toggleActive'])->name('combo.toggle-active');
    Route::get('combo/{combo}/items', [ComboController::class, 'manageItems'])->name('combo.items');
    Route::post('combo/{combo}/items', [ComboController::class, 'saveItems'])->name('combo.items.save');
    Route::resource('combo', ComboController::class);

    // Branch Menu Management
    Route::get('branch-menu/pricing', [BranchMenuController::class, 'pricing'])->name('branch-menu.pricing');
    Route::post('branch-menu/pricing', [BranchMenuController::class, 'savePricing'])->name('branch-menu.pricing.save');
    Route::get('branch-menu/availability', [BranchMenuController::class, 'availability'])->name('branch-menu.availability');
    Route::post('branch-menu/availability', [BranchMenuController::class, 'saveAvailability'])->name('branch-menu.availability.save');
    Route::get('branch-menu/get-items/{branchId}', [BranchMenuController::class, 'getItemsForBranch'])->name('branch-menu.get-items');
});

// Frontend Routes (Public - No Auth)
Route::group(['prefix' => 'menu', 'as' => 'menu.'], function () {
    Route::get('/', [MenuPageController::class, 'index'])->name('index');
    Route::get('/category/{slug}', [MenuPageController::class, 'category'])->name('category');
    Route::get('/item/{slug}', [MenuPageController::class, 'item'])->name('item');
    Route::get('/combos', [MenuPageController::class, 'combos'])->name('combos');
    Route::get('/combo/{slug}', [MenuPageController::class, 'comboDetail'])->name('combo.detail');
    Route::get('/branch/{id}', [MenuPageController::class, 'branchMenu'])->name('branch');
    Route::get('/search', [MenuPageController::class, 'search'])->name('search');
});
