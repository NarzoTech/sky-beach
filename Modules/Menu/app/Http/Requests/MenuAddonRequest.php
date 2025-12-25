<?php

namespace Modules\Menu\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MenuAddonRequest extends FormRequest
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
            'description' => 'nullable|string|max:1000',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'status' => 'required|boolean',
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
            'name.required' => __('The add-on name is required'),
            'name.max' => __('The add-on name must not exceed 255 characters'),
            'price.required' => __('The price is required'),
            'price.numeric' => __('The price must be a number'),
            'price.min' => __('The price must be at least 0'),
            'image.image' => __('The file must be an image'),
            'image.mimes' => __('The image must be a file of type: jpeg, png, jpg, gif, webp'),
            'image.max' => __('The image must not be larger than 2MB'),
        ];
    }
}
