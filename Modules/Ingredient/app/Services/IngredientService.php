<?php

namespace Modules\Ingredient\app\Services;

use App\Models\Stock;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Ingredient\app\Models\Ingredient;
use Modules\Ingredient\app\Models\Variant;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Ingredient\app\Models\VariantOption;
use Modules\Ingredient\app\Models\AttributeValue;
use Modules\Language\app\Enums\TranslationModels;
use Modules\Language\app\Traits\GenerateTranslationTrait;
use Modules\Ingredient\app\Models\Category;
use Modules\Ingredient\app\Models\IngredientBrand;
use Modules\Ingredient\app\Models\UnitType;

class IngredientService
{
    use GenerateTranslationTrait;
    protected Ingredient $ingredient;
    public function __construct(Ingredient $ingredient)
    {
        $this->ingredient = $ingredient;
    }
    public function getProducts()
    {
        return $this->getIngredients();
    }

    public function getIngredients()
    {
        $query = $this->ingredient->with(['salesDetails', 'purchaseReturnDetails', 'stockDetails', 'orders', 'brand', 'purchaseDetails.purchase', 'latestPurchaseDetail', 'unit', 'purchaseUnit', 'consumptionUnit', 'category', 'brand']);

        if (request('keyword')) {
            $query = $query->where(function ($q) {
                $q->where('name', 'like', '%' . request()->keyword . '%')
                    ->orWhere('sku', 'like', '%' . request()->keyword . '%');
            });
        }
        if (request('order_by')) {
            $query = $query->orderBy('name', request('order_by'));
        } else {
            $query = $query->orderBy('name', 'asc');
        }
        if (request('brand_id')) {
            $query = $query->where('brand_id', request('brand_id'));
        }
        if (request('category_id')) {
            $query = $query->where('category_id', request('category_id'));
        }
        return $query;
    }

    // get all active ingredients
    public function allActiveIngredients($request)
    {
        $ingredients = $this->ingredient->where('status', 1)->withCount('variants')->with('category', 'purchaseDetails', 'latestPurchaseDetail', 'unit', 'purchaseUnit', 'consumptionUnit');

        $sort = request()->order_by ? request()->order_by : 'asc';
        $ingredients = $ingredients->orderBy('name', $sort);
        return $ingredients;
    }

    public function getIngredient($id): ?Ingredient
    {
        return $this->ingredient->with('stockDetails')->where('id', $id)->first();
    }
    public function storeIngredient($request)
    {
        $data = $request->validated();

        if ($request->file('image')) {
            $data['image'] = file_upload($request->image);
        }

        // Calculate consumption_unit_cost if conversion_rate and purchase_price are provided
        if (isset($data['conversion_rate']) && $data['conversion_rate'] > 0 && isset($data['purchase_price'])) {
            $data['consumption_unit_cost'] = $data['purchase_price'] / $data['conversion_rate'];
        }

        $ingredient = $this->ingredient->create(
            $data
        );
        Stock::create([
            'purchase_id' => null,
            'ingredient_id' => $ingredient->id,
            'date' => now(),
            'type' => '	Opening Stock',
            'in_quantity' => $request->stock,
            'available_qty' => $request->stock,
            'sku' => $request->sku,
            'purchase_price' => $request->cost,
            'rate' => $request->cost,
            'sale_price' => 0,
            'tax' => $request->tax,
            'created_by' => auth('admin')->user()->id,
        ]);

        return $ingredient;
    }

    public function updateIngredient($request, $ingredient)
    {
        $data = $request->validated();

        if ($request->file('image')) {
            $data['image'] = file_upload($request->image);
        }

        // Calculate consumption_unit_cost if conversion_rate and purchase_price are provided
        if (isset($data['conversion_rate']) && $data['conversion_rate'] > 0 && isset($data['purchase_price'])) {
            $data['consumption_unit_cost'] = $data['purchase_price'] / $data['conversion_rate'];
        }

        $ingredient->update(
            $data
        );

        return $ingredient;
    }

    public function getActiveIngredientById($id)
    {
        return $this->ingredient->where('id', $id)->where('status', 1)->first();
    }

    public function deleteIngredient($ingredient)
    {
        // check if ingredient has orders
        if ($ingredient->orders->count() > 0) {
            return false;
        }
        return $ingredient->delete();
    }
    public function bulkDelete($ids)
    {
        foreach ($ids as $id) {
            $this->deleteIngredient($this->getActiveIngredientById($id));
        }
        return true;
    }

    public function storeRelatedIngredients($request, $ingredient)
    {
        $ids = $request->ingredient_id;


        // Remove existing related ingredients
        $ingredient->relatedIngredients()->delete();

        // Add new related ingredients
        foreach ($ids as $relatedIngredientId) {
            $ingredient->relatedIngredients()->create([
                'related_ingredient_id' => $relatedIngredientId
            ]);
        }

        return $ingredient;
    }
    public function getIngredientBySlug($slug): ?Ingredient
    {
        return $this->ingredient->where('slug', $slug)->first();
    }

    public function getIngredientsByCategory($category_id, $limit = 10): Collection
    {
        return $this->ingredient->where('category_id', $category_id)->limit($limit)->get();
    }

    public function getIngredientsByBrand($brand_id, $limit = 10): Collection
    {
        return $this->ingredient->where('brand_id', $brand_id)->limit($limit)->get();
    }

    public function getIngredientsByTag($tag, $limit = 10): Collection
    {
        return $this->ingredient->where('tags', 'like', '%' . $tag . '%')->limit($limit)->get();
    }

    public function getFeaturedIngredients($limit = 10): Collection
    {
        return $this->ingredient->where('is_featured', 1)->limit($limit)->get();
    }

    public function getBestSellingIngredients($limit = 10): Collection
    {
        return $this->ingredient->where('is_best_selling', 1)->limit($limit)->get();
    }

    public function getTopRatedIngredients($limit = 10): Collection
    {
        return $this->ingredient->where('is_top_rated', 1)->limit($limit)->get();
    }

    public function getNewArrivalIngredients($limit = 10): Collection
    {
        return $this->ingredient->where('is_new_arrival', 1)->limit($limit)->get();
    }

    public function getRelatedIngredients($ingredient)
    {
        return $ingredient->relatedIngredients->pluck('related_ingredient_id')->toArray();
    }

    public function getIngredientsBySearch($search, $limit = 10): Collection
    {
        return $this->ingredient->where('name', 'like', '%' . $search . '%')->limit($limit)->get();
    }

    public function getIngredientsByPriceRange($min, $max, $limit = 10): Collection
    {
        return $this->ingredient->whereBetween('price', [$min, $max])->limit($limit)->get();
    }

    public function getIngredientsByDiscount($limit = 10): Collection
    {
        return $this->ingredient->where('discount', '>', 0)->limit($limit)->get();
    }

    public function getIngredientsByAttribute($attribute, $limit = 10): Collection
    {
        return $this->ingredient->where('attributes', 'like', '%' . $attribute . '%')->limit($limit)->get();
    }

    public function getVariantBySku($sku)
    {
        return Variant::where('sku', $sku)->first();
    }

    public function getIngredientVariants($ingredient)
    {
        $variants = $ingredient->variants->map(function ($variant) {
            return [
                'id' => $variant->id,
                'sku' => $variant->sku,
                'price' => $variant->price,
                'cost' => $variant->cost,
                'attribute' => $variant->attributes(),
                'attributes' => $variant->options->map(function ($option) {
                    return [
                        'attribute_id' => $option->attribute_id,
                        'attribute_value_id' => $option->attribute_value_id,
                        'attribute' => $option->attribute->name,
                        'attribute_value' => $option->attributeValue->name,
                    ];
                }),
            ];
        });

        return $variants;
    }

    public function getIngredientAttributesByVariant($ingredient)
    {
        $variants = $ingredient->variants->map(function ($variant) {
            return $variant->options->map(function ($option) {
                return [
                    'attribute_id' => $option->attribute_id,
                    'attribute_value_id' => $option->attribute_value_id,
                    'attribute' => $option->attribute->name,
                    'attribute_value' => $option->attributeValue->name,
                ];
            });
        });

        return $variants;
    }

    public function getIngredientAttributeValuesIds($ingredient)
    {
        $variants = $ingredient->variants->map(function ($variant) {
            return $variant->options->map(function ($option) {
                return $option->attribute_value_id;
            });
        });

        return $variants;
    }

    public function storeIngredientVariant($request, $ingredient)
    {
        $variantData = $request->variant;
        $sellingPrices = $request->price;
        $costs = $request->cost;
        $skus = $request->sku;

        foreach ($variantData as $key => $variantInfo) {
            // check if variant already exists
            $existingVariant = $ingredient->variants->where('sku', $skus[$key])->first();

            if ($existingVariant) {
                continue;
            }

            $variantInfoArray = explode('-', $variantInfo);

            // Insert variant into the variants table
            $variant = Variant::create([
                'ingredient_id' => $ingredient->id,
                'sku' => $skus[$key],
                'price' => $sellingPrices[$key],
                'cost' => $costs[$key],
            ]);

            // Insert variant-specific information into the variant_attribute_values table
            foreach ($variantInfoArray as $attributeValue) {
                $attributeValueModel = AttributeValue::where('name', $attributeValue)->first();

                if ($attributeValueModel) {
                    VariantOption::create([
                        'variant_id' => $variant->id,
                        'attribute_id' => $attributeValueModel->attribute_id,
                        'attribute_value_id' => $attributeValueModel->id,
                    ]);
                }
            }
        }
    }

    public function getIngredientVariantById($variant_id)
    {
        return Variant::with('options', 'optionValues')->find($variant_id);
    }
    public function getIngredientVariant($variant_id)
    {
        return $this->getIngredientVariantById($variant_id);
    }
    public function updateIngredientVariant($request, $variant)
    {
        $variant->update([
            'price' => $request->selling_price,
            'cost' => $request->cost,
            'sku' => $request->sku,
        ]);
        return $variant;
    }

    public function deleteIngredientVariant($variant)
    {
        // delete variant options
        $variant->options()->delete();

        return $variant->delete();
    }

    public function bulkImport($request)
    {
        $file = $request->file('file');

        // read xlxs / xls / csv file
        $data = Excel::toCollection(null, $file);

        $data = $data->first()->slice(0);
        //  remove the first row
        $data = $data->slice(1);


        //  loop through the data and store the ingredients
        $unsavedData = [];
        foreach ($data as $row) {
            $findIngredient = $this->ingredient->where(function ($q) use ($row) {
                $q->where('sku', trim($row[1]));
            })->first();

            if ($findIngredient) {
                $unsavedData[] = $row;
                continue;
            }

            // if category not found, create a new one

            $categoryName = trim($row[2]);

            $category = Category::where('name', $categoryName)->first();
            if ($categoryName && !$category) {
                $category = Category::create([
                    'name' => $categoryName,
                    'status' => 1,
                ]);
            }

            // if brand not found, create a new one

            $brand_id = null;
            if (isset($row[5])) {
                $brandName = trim($row[5]);
                $brand = IngredientBrand::where('name', $brandName)->first();
                if ($brandName && !$brand) {
                    $brand = IngredientBrand::create([
                        'name' => $brandName,
                        'status' => '1',
                    ]);
                }

                $brand_id = $brand->id;
            }

            // if unit not found, create a new one

            $unitName = trim($row[4]);
            $unit = UnitType::where('name', $unitName)->first();
            if ($unitName && !$unit) {
                $unit = UnitType::create([
                    'name' => $unitName,
                    'ShortName' => $unitName,
                    'status' => 1,
                ]);
            }

            // if ingredient not found, create a new one


            // generate ingredient sku
            $sku = mt_rand(10000000, 99999999);
            $ingredient = $this->ingredient->create([
                'name' => trim($row[0]),
                'sku' => trim($row[1]) != null ? trim($row[1]) : $sku,
                'category_id' => $category->id,
                'unit_id' => $unit->id,
                'brand_id' => $brand_id,
                'stock_alert' => trim($row[6]),
                'cost' => trim($row[11]),
                'stock' => (trim($row[18]) == null || trim($row[18]) < 0) ? 0 : trim($row[18]),
                'status' => 1,
                'images' => ['null'],
            ]);


            // store ingredient stock

            Stock::create([
                'ingredient_id' => $ingredient->id,
                'date' => now(),
                'type' => '	Opening Stock',
                'in_quantity' => (trim($row[18]) == null || trim($row[18]) < 0) ? 0 : trim($row[18]),
                'sku' => $ingredient->sku,
                'purchase_price' => 0,
                'rate' => 0,
                'sale_price' => 0,
                'created_by' => auth('admin')->user()->id,
            ]);
        }
    }


    public function storeIngredientGallery($request, $ingredient)
    {
        $images = $request->images;

        $ingredient->images = $images;

        $ingredient->save();
        return $ingredient;
    }

    /**
     * Calculate the total consumption cost for a recipe/menu item
     *
     * @param array $ingredients Array of ['ingredient_id' => id, 'quantity' => qty]
     * @return float Total cost
     */
    public function calculateRecipeCost(array $ingredients): float
    {
        $totalCost = 0;

        foreach ($ingredients as $item) {
            $ingredient = $this->getIngredient($item['ingredient_id']);
            if ($ingredient) {
                $totalCost += $ingredient->calculateConsumptionCost($item['quantity']);
            }
        }

        return $totalCost;
    }
}
