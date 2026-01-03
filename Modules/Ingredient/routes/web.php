<?php

use Illuminate\Support\Facades\Route;
use Modules\Ingredient\app\Http\Controllers\BrandController;
use Modules\Ingredient\app\Http\Controllers\IngredientAttributeController;
use Modules\Ingredient\app\Http\Controllers\IngredientCategoryController;
use Modules\Ingredient\app\Http\Controllers\IngredientController;
use Modules\Ingredient\app\Http\Controllers\UnitTypeController;

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

Route::group(['as' => 'admin.', 'prefix' => 'admin', 'middleware' => ['auth:admin', 'translation']], function () {

    // bulk ingredient import

    Route::get('ingredient/import', [IngredientController::class, 'bulkImport'])->name('ingredient.import');
    Route::post('ingredient/import', [IngredientController::class, 'bulkImportStore'])->name('ingredient.import.store');

    Route::post('ingredient/wishlist/{id}', [IngredientController::class, 'wishlist'])->name('ingredient.wishlist');
    // Ingredients
    Route::get('ingredient/barcode', [IngredientController::class, 'barcode'])->name('ingredient.barcode');
    Route::post('ingredient/barcode/print', [IngredientController::class, 'barcodePrint'])->name('ingredient.barcode.print');
    Route::get('ingredient/search', [IngredientController::class, 'search'])->name('ingredient.search');
    Route::post('ingredient/status/{id}', [IngredientController::class, 'status'])->name('ingredient.status');
    Route::get('ingredient/view/{id}', [IngredientController::class, 'singleIngredient'])->name('ingredient.view');
    Route::post('ingredient/delete', [IngredientController::class, 'bulkDelete'])->name('ingredient.bulk.delete');
    Route::get('ingredient/unit-family', [IngredientController::class, 'getUnitFamily'])->name('ingredient.unit-family');
    Route::resource('ingredient', IngredientController::class);




    Route::get('ingredient/ingredient-gallery/{id}', [IngredientController::class, 'ingredient_gallery'])->name('ingredient-gallery');
    Route::post('ingredient/ingredient-gallery/{id}', [IngredientController::class, 'ingredient_gallery_store'])->name('ingredient-gallery.store');

    // view
    Route::get('ingredient/related-ingredient/{id}', [IngredientController::class, 'related_ingredient'])->name('related-ingredients');
    // store
    Route::post('ingredient/related-ingredient/{id}', [IngredientController::class, 'related_ingredient_store'])->name('store-related-ingredients');

    Route::get('ingredient/related-variant/{id}', [IngredientController::class, 'ingredient_variant'])->name('ingredient-variant');

    Route::get('ingredient/related-variant/{id}/create', [IngredientController::class, 'ingredient_variant_create'])->name('ingredient-variant.create');

    Route::post('ingredient/related-variant/{id}', [IngredientController::class, 'ingredient_variant_store'])->name('ingredient-variant.store');
    Route::get('ingredient/related-variant/edit/{variant_id}', [IngredientController::class, 'ingredient_variant_edit'])->name('ingredient-variant.edit');
    Route::put('ingredient/related-variant/{variant_id}', [IngredientController::class, 'ingredient_variant_update'])->name('ingredient-variant.update');

    Route::delete('ingredient/related-variant/{variant_id}', [IngredientController::class, 'ingredient_variant_delete'])->name('ingredient-variant.delete');

    Route::get('/{id}/clone', [IngredientController::class, 'clone'])->name('clone');
    Route::get('bulk-ingredient-upload', [IngredientController::class, 'bulk_ingredient_upload_page'])->name('bulk_ingredient_upload_page');
    Route::post('bulk-ingredient-upload-store', [IngredientController::class, 'bulk_ingredient_store'])->name('bulk_ingredient_store');
    Route::post('wholesale-modal', [IngredientController::class, 'ingredientWholesaleModal'])->name('wholesale.modal');


    // Categories Routes


    Route::get('get-sub-category/{id}', [IngredientCategoryController::class, 'getSubCategory'])->name('get.sub-category');
    Route::get('bulk-category-upload', [IngredientCategoryController::class, 'bulk_category_upload_page'])->name('bulk_category_upload_page');

    Route::get('download-category-list-csv', [IngredientCategoryController::class, 'csv_category_download'])->name('csv_category_download');

    Route::post('bulk-category-upload-store', [IngredientCategoryController::class, 'bulk_category_store'])->name('bulk_category_store');

    Route::get('/category-info', [IngredientCategoryController::class, 'info'])->name('categories.index_info');
    Route::get('/categories/get-data', [IngredientCategoryController::class, 'getData'])->name('categories.get-data');

    // delete all selected category
    Route::post('/categories/delete-all', [IngredientCategoryController::class, 'deleteAll'])->name('category.deleteSelected');

    // delete all selected brand
    Route::post('/brands/delete-all', [BrandController::class, 'deleteAll'])->name('brand.deleteSelected');

    Route::post('/request-ingredient/approved', [IngredientController::class, 'approved'])->name('request.approved');
    Route::group(['prefix' => 'ingredients'], function () {
        Route::resource('category', IngredientCategoryController::class);
        Route::resource('brand', BrandController::class);

        Route::resource('attribute', IngredientAttributeController::class);
        Route::post('attribute/get-value/', [IngredientAttributeController::class, 'getValue'])->name('attribute.get.value');
        Route::post('attribute/value/delete', [IngredientAttributeController::class, 'deleteValue'])->name('attribute.value.delete');
        Route::post('/attribute/has-value', [IngredientAttributeController::class, 'checkHasValue'])->name('attribute.has-value');
        Route::resource('unit', UnitTypeController::class);
        Route::get('unit/parent/{id}', [UnitTypeController::class, 'unitByParent'])->name('unit.parent');
    });
});
