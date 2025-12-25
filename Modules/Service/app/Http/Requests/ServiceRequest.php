<?php

namespace Modules\Service\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ServiceRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'status' => 'required|boolean',
            'category_id' => 'required|exists:service_categories,id',
            'description' => 'nullable|string',
            'price' => 'required|numeric'
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Name is required',
            'status.required' => 'Status is required',
            'category_id.required' => 'Category is required',
            'price.required' => 'Price is required',
            'price.numeric' => 'Price must be numeric',
            'description.string' => 'Description must be string',
            'description.required' => 'Description is required',
            'name.string' => 'Name must be string',
        ];
    }
}
