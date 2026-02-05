<?php

namespace Modules\Menu\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MenuItemRequest extends FormRequest
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
            'slug' => 'nullable|string|max:255|unique:menu_items,slug,' . $this->route('menu_item'),
            'short_description' => 'nullable|string|max:500',
            'long_description' => 'nullable|string',
            'category_id' => 'nullable|exists:menu_categories,id',
            'cuisine_type' => 'nullable|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'gallery' => 'nullable|array|max:6',
            'gallery.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'base_price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'preparation_time' => 'nullable|integer|min:0',
            'calories' => 'nullable|integer|min:0',
            'is_vegetarian' => 'nullable|boolean',
            'is_vegan' => 'nullable|boolean',
            'is_spicy' => 'nullable|boolean',
            'spice_level' => 'nullable|integer|min:0|max:5',
            'allergens' => 'nullable|array',
            'is_featured' => 'nullable|boolean',
            'is_new' => 'nullable|boolean',
            'is_popular' => 'nullable|boolean',
            'is_available' => 'nullable|boolean',
            'available_in_pos' => 'nullable|boolean',
            'available_in_website' => 'nullable|boolean',
            'status' => 'required|boolean',
            'sku' => 'nullable|string|max:50',
            'barcode' => 'nullable|string|max:50',
            'display_order' => 'nullable|integer|min:0',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The menu item name is required.',
            'name.max' => 'The name may not be greater than 255 characters.',
            'slug.unique' => 'This slug is already taken.',
            'base_price.required' => 'The base price is required.',
            'base_price.numeric' => 'The base price must be a number.',
            'base_price.min' => 'The base price must be at least 0.',
            'image.image' => 'The file must be an image.',
            'image.mimes' => 'The image must be a file of type: jpeg, png, jpg, gif, webp.',
            'image.max' => 'The image may not be greater than 2MB.',
            'category_id.exists' => 'The selected category does not exist.',
        ];
    }
}
