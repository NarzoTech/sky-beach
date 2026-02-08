<?php
namespace Modules\Expense\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExpenseRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'date'                => 'required|date',
            'amount'              => 'required|numeric|min:0.01',
            'payment_type'        => 'required',
            'account_id'          => 'required',
            'expense_type_id'     => 'required',
            'sub_expense_type_id' => 'nullable',
            'note'                => 'nullable|string',
            'memo'                => 'nullable|string',
            'paying_amount'       => 'nullable|array',
            'paying_amount.*'     => 'nullable|numeric|min:0',
            'document'            => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
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
            'date.required'            => 'Date is required',
            'amount.required'          => 'Amount is required',
            'amount.min'               => 'Amount must be at least 0.01',
            'payment_type.required'    => 'Payment type is required',
            'account_id.required'      => 'Account is required',
            'expense_type_id.required' => 'Expense type is required',
            'document.mimes'           => 'Document must be a PDF, JPG, PNG, DOC, or DOCX file',
            'document.max'             => 'Document must not exceed 5MB',
        ];
    }
}
