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
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:100',
            'category_id' => 'required|exists:categories,id',
            'purchase_unit_id' => 'required|exists:unit_types,id',
            'consumption_unit_id' => 'required|exists:unit_types,id',
            'conversion_rate' => 'required|numeric|min:0.0001',
            'purchase_price' => 'required|numeric|min:0',
            'consumption_unit_cost' => 'nullable|numeric|min:0',
            'alert_quantity' => 'nullable|numeric|min:0',
            'status' => 'required|in:0,1',
            // Legacy fields - keep for backward compatibility
            'short_description' => 'nullable',
            'brand_id' => 'nullable',
            'unit_id' => 'nullable',
            'image' => 'nullable|image|max:2048',
            'cost' => 'nullable',
            'stock_alert' => 'nullable',
            'is_imei' => 'nullable',
            'not_selling' => 'nullable',
            'stock' => 'nullable',
            'stock_status' => 'nullable',
            "tax_type" => 'nullable',
            "tax" => 'nullable',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Map alert_quantity to stock_alert for backward compatibility
        if ($this->has('alert_quantity')) {
            $this->merge([
                'stock_alert' => $this->alert_quantity,
            ]);
        }
    }

    public function messages()
    {
        return [
            'name.required' => 'Ingredient name is required',
            'sku.required' => 'Ingredient code is required',
            'category_id.required' => 'Category is required',
            'category_id.exists' => 'Invalid category selected',
            'purchase_unit_id.required' => 'Purchase unit is required',
            'purchase_unit_id.exists' => 'Invalid purchase unit',
            'consumption_unit_id.required' => 'Consumption unit is required',
            'consumption_unit_id.exists' => 'Invalid consumption unit',
            'conversion_rate.required' => 'Conversion rate is required',
            'conversion_rate.numeric' => 'Conversion rate must be a number',
            'conversion_rate.min' => 'Conversion rate must be greater than 0',
            'purchase_price.required' => 'Purchase price is required',
            'purchase_price.numeric' => 'Purchase price must be a number',
            'purchase_price.min' => 'Purchase price must be greater than or equal to 0',
            'status.required' => 'Status is required',
            'image.max' => 'Image size must be less than 2MB',
        ];
    }
}
