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
            'amount'              => 'required|numeric',
            'payment_type'        => 'required',
            'account_id'          => 'required',
            'expense_type_id'     => 'required',
            'sub_expense_type_id' => 'nullable',
            'note'                => 'nullable',

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
            'payment_type.required'    => 'Payment type is required',
            'account_id.required'      => 'Account is required',
            'expense_type_id.required' => 'Expense type is required',
        ];
    }
}
