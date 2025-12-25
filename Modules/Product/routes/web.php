<?php

use Illuminate\Support\Facades\Route;
use Modules\Product\app\Http\Controllers\BrandController;
use Modules\Product\app\Http\Controllers\ProductAttributeController;
use Modules\Product\app\Http\Controllers\ProductCategoryController;
use Modules\Product\app\Http\Controllers\ProductController;
use Modules\Product\app\Http\Controllers\UnitTypeController;

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

    // bulk product import

    Route::get('product/import', [ProductController::class, 'bulkImport'])->name('product.import');
    Route::post('product/import', [ProductController::class, 'bulkImportStore'])->name('product.import.store');

    Route::post('product/wishlist/{id}', [ProductController::class, 'wishlist'])->name('product.wishlist');
    // Products
    Route::get('product/barcode', [ProductController::class, 'barcode'])->name('product.barcode');
    Route::post('product/barcode/print', [ProductController::class, 'barcodePrint'])->name('product.barcode.print');
    Route::get('product/search', [ProductController::class, 'search'])->name('product.search');
    Route::post('product/status/{id}', [ProductController::class, 'status'])->name('product.status');
    Route::get('product/view/{id}', [ProductController::class, 'singleProduct'])->name('product.view');
    Route::post('product/delete', [ProductController::class, 'bulkDelete'])->name('product.bulk.delete');
    Route::get('product/unit-family', [ProductController::class, 'getUnitFamily'])->name('product.unit-family');
    Route::resource('product', ProductController::class);




    Route::get('product/product-gallery/{id}', [ProductController::class, 'product_gallery'])->name('product-gallery');
    Route::post('product/product-gallery/{id}', [ProductController::class, 'product_gallery_store'])->name('product-gallery.store');

    // view
    Route::get('product/related-product/{id}', [ProductController::class, 'related_product'])->name('related-products');
    // store
    Route::post('product/related-product/{id}', [ProductController::class, 'related_product_store'])->name('store-related-products');

    Route::get('product/related-variant/{id}', [ProductController::class, 'product_variant'])->name('product-variant');

    Route::get('product/related-variant/{id}/create', [ProductController::class, 'product_variant_create'])->name('product-variant.create');

    Route::post('product/related-variant/{id}', [ProductController::class, 'product_variant_store'])->name('product-variant.store');
    Route::get('product/related-variant/edit/{variant_id}', [ProductController::class, 'product_variant_edit'])->name('product-variant.edit');
    Route::put('product/related-variant/{variant_id}', [ProductController::class, 'product_variant_update'])->name('product-variant.update');

    Route::delete('product/related-variant/{variant_id}', [ProductController::class, 'product_variant_delete'])->name('product-variant.delete');

    Route::get('/{id}/clone', [ProductController::class, 'clone'])->name('clone');
    Route::get('bulk-product-upload', [ProductController::class, 'bulk_product_upload_page'])->name('bulk_product_upload_page');
    Route::post('bulk-product-upload-store', [ProductController::class, 'bulk_product_store'])->name('bulk_product_store');
    Route::post('wholesale-modal', [ProductController::class, 'productWholesaleModal'])->name('wholesale.modal');


    // Categories Routes


    Route::get('get-sub-category/{id}', [ProductCategoryController::class, 'getSubCategory'])->name('get.sub-category');
    Route::get('bulk-category-upload', [ProductCategoryController::class, 'bulk_category_upload_page'])->name('bulk_category_upload_page');

    Route::get('download-category-list-csv', [ProductCategoryController::class, 'csv_category_download'])->name('csv_category_download');

    Route::post('bulk-category-upload-store', [ProductCategoryController::class, 'bulk_category_store'])->name('bulk_category_store');

    Route::get('/category-info', [ProductCategoryController::class, 'info'])->name('categories.index_info');
    Route::get('/categories/get-data', [ProductCategoryController::class, 'getData'])->name('categories.get-data');

    // delete all selected category
    Route::post('/categories/delete-all', [ProductCategoryController::class, 'deleteAll'])->name('category.deleteSelected');

    // delete all selected brand
    Route::post('/brands/delete-all', [BrandController::class, 'deleteAll'])->name('brand.deleteSelected');

    Route::post('/request-product/approved', [ProductController::class, 'approved'])->name('request.approved');
    Route::group(['prefix' => 'products'], function () {
        Route::resource('category', ProductCategoryController::class);
        Route::resource('brand', BrandController::class);

        Route::resource('attribute', ProductAttributeController::class);
        Route::post('attribute/get-value/', [ProductAttributeController::class, 'getValue'])->name('attribute.get.value');
        Route::post('attribute/value/delete', [ProductAttributeController::class, 'deleteValue'])->name('attribute.value.delete');
        Route::post('/attribute/has-value', [ProductAttributeController::class, 'checkHasValue'])->name('attribute.has-value');
        Route::resource('unit', UnitTypeController::class);
        Route::get('unit/parent/{id}', [UnitTypeController::class, 'unitByParent'])->name('unit.parent');
    });
});
