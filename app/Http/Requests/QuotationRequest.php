<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QuotationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth('admin')->check() ? true : false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'customer_id' => 'required|exists:users,id',
            'note' => 'nullable',
            'date' => 'required|date',
            'description' => 'required|array|min:1',
            'description.*' => 'required|string|max:255',
            'quantity' => 'required|array',
            'quantity.*' => 'required|numeric|min:1',
            'unit_price' => 'required|array',
            'unit_price.*' => 'required|numeric|min:0',
            'total' => 'required|array',
            'total.*' => 'required|numeric|min:0',
            'subtotal' => 'required|numeric',
            'after_discount' => 'required|numeric',
            'total_amount' => 'required|numeric',
            'discount' => 'nullable',
            'vat' => 'nullable',
        ];
    }

    public function messages(): array
    {
        return [
            'customer_id.required' => 'The customer field is required.',
            'customer_id.exists' => 'The selected customer is invalid.',
            'description.required' => 'At least one item is required.',
            'description.min' => 'At least one item is required.',
            'description.*.required' => 'The item description is required.',
            'description.*.string' => 'The item description must be text.',
            'quantity.required' => 'The quantity field is required.',
            'quantity.*.required' => 'The quantity field is required.',
            'quantity.*.numeric' => 'The quantity must be a number.',
            'quantity.*.min' => 'The quantity must be at least 1.',
            'unit_price.required' => 'The unit price field is required.',
            'unit_price.*.required' => 'The unit price field is required.',
            'unit_price.*.numeric' => 'The unit price must be a number.',
            'total.required' => 'The total field is required.',
            'total.*.required' => 'The total field is required.',
            'subtotal.required' => 'The subtotal field is required.',
            'after_discount.required' => 'The after discount field is required.',
            'total_amount.required' => 'The total amount field is required.',
        ];
    }
}
