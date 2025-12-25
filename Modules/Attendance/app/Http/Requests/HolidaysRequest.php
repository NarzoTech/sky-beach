<?php

namespace Modules\Attendance\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HolidaysRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'description' => 'required',
            'status' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The name field is required.',
            'start_date.required' => 'The start date field is required.',
            'end_date.required' => 'The end date field is required.',
            'description.required' => 'The description field is required.',
            'status.required' => 'The status field is required.',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth('admin')->check();
    }
}
