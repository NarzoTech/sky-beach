<?php

namespace Modules\Purchase\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'supplier_id' => 'required',
            'invoice_number' => 'required',
            'purchase_date' => 'required|date',
            'items' => 'required',
            'total_amount' => 'required',
            'paid_amount' => 'required|array',
            'paid_amount.*' => 'required|numeric',
            'due_amount' => 'required',
            'payment_type' => 'required',
            'ingredient_id' => 'required|array',
            'ingredient_id.*' => 'required|distinct',
            'quantity' => 'required|array',
            'quantity.*' => 'required|numeric',
            'stock' => 'required|array',
            'stock.*' => 'required|numeric',
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
            'supplier_id.required' => 'Supplier is required',
            'invoice_number.required' => 'Invoice number is required',
            'purchase_date.required' => 'Purchase date is required',
            'items.required' => 'Items is required',
            'total_amount.required' => 'Total amount is required',
            'paid_amount.required' => 'Paid amount is required',
            'due_amount.required' => 'Due amount is required',
            'payment_type.required' => 'Payment type is required',
            'status.required' => 'Status is required',
            'ingredient_id.required' => 'Ingredient is required',
            'ingredient_id.*.required' => 'Ingredient is required',
            'quantity.required' => 'Quantity is required',
            'quantity.*.required' => 'Quantity is required',
            'stock.required' => 'Stock is required',
            'stock.*.required' => 'Stock is required',
        ];
    }
}
