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
            'product_id' => 'required|array',
            'quantity' => 'required|array',
            'unit_price' => 'required|array',
            'total' => 'required|array',
            'product_id.*' => 'required|distinct',
            'quantity.*' => 'required|numeric',
            'unit_price.*' => 'required|numeric',
            'total.*' => 'required|numeric',
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
            'product_id.required' => 'The product field is required.',
            'product_id.*.required' => 'The product field is required.',
            'product_id.*.distinct' => 'The product field must be unique.',
            'quantity.required' => 'The quantity field is required.',
            'quantity.*.required' => 'The quantity field is required.',
            'unit_price.required' => 'The unit price field is required.',
            'unit_price.*.required' => 'The unit price field is required.',
            'total.required' => 'The total field is required.',
            'total.*.required' => 'The total field is required.',
            'subtotal.required' => 'The subtotal field is required.',
            'after_discount.required' => 'The after discount field is required.',
            'total_amount.required' => 'The total amount field is required.',
        ];
    }
}
