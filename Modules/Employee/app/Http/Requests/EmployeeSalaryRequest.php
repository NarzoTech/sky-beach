<?php

namespace Modules\Employee\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeSalaryRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'amount' => 'required|numeric',
            'type' => 'required',
            'date' => 'required',
            'payment_type' => 'required',
            'account_id' => 'required',
            'note' => 'nullable',
            'month' => 'required',
            'year' => 'required',
            'salary' => 'required',
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
            'required' => 'The :attribute field is required.',
        ];
    }
}
