<?php

namespace Modules\Ingredient\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IngredientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required',
            'short_description' => 'nullable',
            'brand_id' => 'nullable',
            'category_id' => 'required',
            'unit_id' => 'nullable',
            'purchase_unit_id' => 'nullable|exists:unit_types,id',
            'consumption_unit_id' => 'nullable|exists:unit_types,id',
            'conversion_rate' => 'nullable|numeric|min:0.0001',
            'purchase_price' => 'nullable|numeric|min:0',
            'consumption_unit_cost' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|max:2048',
            'cost' => 'nullable',
            'stock_alert' => 'nullable',
            'is_imei' => 'nullable',
            'not_selling' => 'nullable',
            'stock' => 'nullable',
            'stock_status' => 'nullable',
            'sku' => 'required',
            'status' => 'required',
            "tax_type" => 'nullable',
            "tax" => 'nullable',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Ingredient name is required',
            'category_id.required' => 'Ingredient category is required',
            'sku.required' => 'Ingredient code is required',
            'status.required' => 'Ingredient status is required',
            'image.size' => 'Image size must be less than 2MB',
            'purchase_unit_id.exists' => 'Invalid purchase unit',
            'consumption_unit_id.exists' => 'Invalid consumption unit',
            'conversion_rate.numeric' => 'Conversion rate must be a number',
            'conversion_rate.min' => 'Conversion rate must be greater than 0',
            'purchase_price.numeric' => 'Purchase price must be a number',
            'purchase_price.min' => 'Purchase price must be greater than or equal to 0',
        ];
    }
}
