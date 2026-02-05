<?php

namespace Modules\Menu\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ComboRequest extends FormRequest
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
        $rules = [
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:2000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'gallery' => 'nullable|array|max:5',
            'gallery.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'combo_price' => 'required|numeric|min:0',
            'discount_type' => 'nullable|in:percentage,fixed',
            'discount_value' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_active' => 'required|boolean',
            'status' => 'required|boolean',
            'display_order' => 'nullable|integer|min:0',
            'items' => 'nullable|array',
            'items.*.menu_item_id' => 'required_with:items|exists:menu_items,id',
            'items.*.variant_id' => 'nullable|exists:menu_variants,id',
            'items.*.quantity' => 'nullable|integer|min:1',
        ];

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => __('The combo name is required'),
            'name.max' => __('The combo name must not exceed 255 characters'),
            'combo_price.required' => __('The combo price is required'),
            'combo_price.numeric' => __('The combo price must be a number'),
            'combo_price.min' => __('The combo price must be at least 0'),
            'image.image' => __('The file must be an image'),
            'image.mimes' => __('The image must be a file of type: jpeg, png, jpg, gif, webp'),
            'image.max' => __('The image must not be larger than 2MB'),
            'end_date.after_or_equal' => __('The end date must be after or equal to the start date'),
            'items.*.menu_item_id.exists' => __('The selected menu item is invalid'),
        ];
    }
}
