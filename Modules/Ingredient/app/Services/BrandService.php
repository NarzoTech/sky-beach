<?php

namespace Modules\Ingredient\app\Services;


use Modules\Language\app\Enums\TranslationModels;
use Modules\Language\app\Traits\GenerateTranslationTrait;
use Modules\Ingredient\app\Models\IngredientBrand;

class BrandService
{
    use GenerateTranslationTrait;
    protected IngredientBrand $brand;

    public function __construct(IngredientBrand $brand)
    {
        $this->brand = $brand;
    }

    public function all()
    {
        return $this->brand->all();
    }

    // get ingredient paginate
    public function getPaginateBrands()
    {
        $brand = $this->brand;
        if (request()->keyword) {
            $brand = $brand->where('name', 'like', '%' . request()->keyword . '%')->orWhere('description', 'like', '%' . request()->keyword . '%');
        }
        if (request()->order_by) {
            $brand = $brand->orderBy('name', request()->order_by);
        } else {
            $brand = $brand->orderBy('name', 'asc');
        }

        return $brand;
    }

    // store ingredient brand

    public function store($request)
    {

        $brand = $this->brand->create($request->all());

        return $brand;
    }

    public function find($id)
    {
        return $this->brand->find($id);
    }

    public function update($request, $id)
    {
        $brand = $this->brand->find($id);
        $brand->update($request->all());


        return $brand;
    }

    public function delete($id)
    {
        $brand = $this->brand->find($id);

        return $brand->delete();
    }

    public function getActiveBrands()
    {
        return $this->brand->where('status', '1')->get();
    }

    public function findBySlug($slug)
    {
        return $this->brand->where('slug', $slug)->first();
    }

    public function getIngredientByBrand($slug)
    {
        $brand = $this->brand->where('slug', $slug)->first();
        if ($brand) {
            return $brand->ingredients;
        }
        return [];
    }

    public function deleteAll($request)
    {
        $ids = $request->ids;
        return $this->brand->whereIn('id', $ids)->delete();
    }
}
