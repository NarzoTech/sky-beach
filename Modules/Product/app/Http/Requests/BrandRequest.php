<?php

namespace Modules\Product\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BrandRequest extends FormRequest
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
            'name' => 'required|string|max:255|unique:product_brands,name,' . $this->brand,
            'description' => 'nullable|string',
            'status' => 'required|boolean',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */

    public function messages(): array
    {
        return [
            'name.required' => __('Brand name is required'),
            'name.string' => __('Brand name must be a string'),
            'name.max' => __('Brand name must not be greater than 255 characters'),
            'name.unique' => __('Brand name already exists'),
            'slug.required' => __('Brand slug is required'),
            'slug.string' => __('Brand slug must be a string'),
            'slug.max' => __('Brand slug must not be greater than 255 characters'),
            'description.string' => __('Brand description must be a string'),
            'status.required' => __('Brand status is required'),
            'status.boolean' => __('Brand status must be a boolean'),
        ];
    }
}
