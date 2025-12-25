<?php

namespace Modules\Product\app\Services;


use Modules\Language\app\Enums\TranslationModels;
use Modules\Language\app\Traits\GenerateTranslationTrait;
use Modules\Product\app\Models\Category;

class ProductCategoryService
{
    use GenerateTranslationTrait;

    private $category;
    public function __construct(Category $category)
    {
        $this->category = $category;
    }

    // Get all product categories

    public function getAllProductCategories()
    {
        $category = $this->category;
        if (request()->keyword) {
            $category = $category->where('name', 'like', '%' . request()->keyword . '%');
        }
        if (request()->order_by) {
            $category = $category->orderBy('name', request()->order_by);
        } else {
            $category = $category->orderBy('name', 'asc');
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

    // Get all active product categories

    public function getActiveProductCategories()
    {
        return $this->category->where('status', '1')->get();
    }

    public function getCategories()
    {
        $category = $this->category->with('products', 'products.purchaseDetails', 'products.salesDetails');
        if (request()->keyword) {
            $category = $category->where('name', 'like', '%' . request()->keyword . '%');
        }

        return $category;
    }

    public function getTopProductCategories()
    {
        return $this->category->where('status', '1')->where('top_category', '1');
    }

    // store product category

    public function storeProductCategory($request)
    {
        $category = $this->category->create($request->all());

        return $category;
    }

    // update product category

    public function updateProductCategory($request, $id)
    {
        $category = $this->category->find($id);
        $category->update($request->all());

        return $category;
    }

    // delete product category

    public function deleteProductCategory($id)
    {
        // check if category has products
        $category = $this->category->find($id);
        if ($category->products->count() > 0) {
            return false;
        }
        return $this->category->destroy($id);
    }

    // get all product categories for select

    public function getAllProductCategoriesForSelect()
    {
        return $this->category->where('status', '1')->get();
    }

    // get categories id by product id
    public function getCategoriesIdsByProductId($product_id)
    {
        return $this->category->whereHas('products', function ($query) use ($product_id) {
            $query->where('product_id', $product_id);
        })->pluck('id')->toArray();
    }

    public function getProductCategory($id)
    {
        return $this->category->find($id);
    }

    public function findBySlug($slug)
    {
        return $this->category->where('slug', $slug)->first();
    }

    public function getProductByCategory($slug)
    {
        $category = $this->category->where('slug', $slug)->first();
        if ($category) {
            return $category->products;
        }
        return [];
    }

    public function deleteAll($request)
    {
        $this->category->whereIn('id', $request->ids)->delete();
    }
}
