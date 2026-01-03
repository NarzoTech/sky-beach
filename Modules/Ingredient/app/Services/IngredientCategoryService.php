<?php

namespace Modules\Ingredient\app\Services;


use Modules\Language\app\Enums\TranslationModels;
use Modules\Language\app\Traits\GenerateTranslationTrait;
use Modules\Ingredient\app\Models\Category;

class IngredientCategoryService
{
    use GenerateTranslationTrait;

    private $category;
    public function __construct(Category $category)
    {
        $this->category = $category;
    }

    // Get all ingredient categories

    public function getAllIngredientCategories()
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

    // Get all active ingredient categories

    public function getActiveIngredientCategories()
    {
        return $this->category->where('status', '1')->get();
    }

    public function getCategories()
    {
        $category = $this->category->with('ingredients', 'ingredients.purchaseDetails', 'ingredients.salesDetails');
        if (request()->keyword) {
            $category = $category->where('name', 'like', '%' . request()->keyword . '%');
        }

        return $category;
    }

    public function getTopIngredientCategories()
    {
        return $this->category->where('status', '1')->where('top_category', '1');
    }

    // store ingredient category

    public function storeIngredientCategory($request)
    {
        $category = $this->category->create($request->all());

        return $category;
    }

    // update ingredient category

    public function updateIngredientCategory($request, $id)
    {
        $category = $this->category->find($id);
        $category->update($request->all());

        return $category;
    }

    // delete ingredient category

    public function deleteIngredientCategory($id)
    {
        // check if category has ingredients
        $category = $this->category->find($id);
        if ($category->ingredients->count() > 0) {
            return false;
        }
        return $this->category->destroy($id);
    }

    // get all ingredient categories for select

    public function getAllIngredientCategoriesForSelect()
    {
        return $this->category->where('status', '1')->get();
    }

    // get categories id by ingredient id
    public function getCategoriesIdsByIngredientId($ingredient_id)
    {
        return $this->category->whereHas('ingredients', function ($query) use ($ingredient_id) {
            $query->where('ingredient_id', $ingredient_id);
        })->pluck('id')->toArray();
    }

    public function getIngredientCategory($id)
    {
        return $this->category->find($id);
    }

    public function findBySlug($slug)
    {
        return $this->category->where('slug', $slug)->first();
    }

    public function getIngredientByCategory($slug)
    {
        $category = $this->category->where('slug', $slug)->first();
        if ($category) {
            return $category->ingredients;
        }
        return [];
    }

    public function deleteAll($request)
    {
        $this->category->whereIn('id', $request->ids)->delete();
    }
}
