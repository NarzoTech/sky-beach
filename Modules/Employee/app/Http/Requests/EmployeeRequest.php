<?php

namespace Modules\Employee\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'required',
            'email' => 'nullable|email',
            'mobile' => 'nullable',
            'designation' => 'nullable',
            'nid' => 'nullable',
            'address' => 'nullable',
            'join_date' => 'nullable|date',
            'salary' => 'nullable|numeric',
            'yearly_leaves' => 'nullable|numeric',
            'image' => 'nullable|image',
            'status' => 'nullable'
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}
