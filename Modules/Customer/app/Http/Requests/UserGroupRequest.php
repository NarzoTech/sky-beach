<?php

namespace Modules\Customer\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserGroupRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'required',
            'description' => 'nullable',
            'discount' => 'nullable|numeric',
            'type' => 'nullable|in:supplier,customer',
            'status' => 'nullable',

        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the error messages for the defined validation rules.
     */

    public function messages(): array
    {
        return [
            'name.required' => 'Name is required',
            'discount.numeric' => 'Discount must be a number',
        ];
    }
}
