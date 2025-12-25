<?php

namespace Modules\Accounts\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Modules\Accounts\app\Models\Account;

class AccountRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        if ($this->account_type == 'bank') {
            return [
                'account_type' => 'required',
                'bank_id' => 'required|integer',
                'bank_account_type' => 'required|string',
                'bank_account_name' => 'required|string',
                'bank_account_number' => 'required|string',
                'bank_account_branch' => 'required|string',
            ];
        }
        if ($this->account_type == 'card') {
            return [
                'account_type' => 'required',
                'card_type' => 'required|string',
                'bank_id' => 'required|integer',
                'card_holder_name' => 'required|string',
                'card_number' => 'required',
            ];
        }
        if ($this->account_type == 'mobile_banking') {
            return [
                'account_type' => 'required',
                'mobile_bank_name' => 'required|string',
                'mobile_number' => 'required',
            ];
        }
        if ($this->account_type == 'cash') {
            return [];
        }

        return [
            'account_type' => 'required',
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
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($this->account_type === 'cash') {
                $query = Account::where('account_type', 'cash');

                // If updating, exclude current account
                if ($this->route('account')) {
                    $query->where('id', '!=', $this->route('account'));
                }

                if ($query->exists()) {
                    $validator->errors()->add('account_type', 'A cash account already exists. Only one cash account is allowed.');
                }
            }
        });
    }

    /**
     * Get the error messages for the defined validation rules.
     */

    public function messages(): array
    {
        return [
            'bank_id.required' => 'Bank Name is required',
            'bank_id.integer' => 'Bank must be an integer',
            'bank_account_type.required' => 'Bank account type is required',
            'bank_account_type.string' => 'Bank account type must be a string',
            'bank_account_name.required' => 'Bank account name is required',
            'bank_account_name.string' => 'Bank account name must be a string',
            'bank_account_number.required' => 'Bank account number is required',
            'bank_account_number.string' => 'Bank account number must be a string',
            'bank_account_branch.required' => 'Bank account branch is required',
            'bank_account_branch.string' => 'Bank account branch must be a string',
            'card_type.required' => 'Card type is required',
            'card_type.string' => 'Card type must be a string',
            'card_holder_name.required' => 'Card holder name is required',
            'card_holder_name.string' => 'Card holder name must be a string',
            'card_number.required' => 'Card number is required',
            'card_number.numeric' => 'Card number must be a number',
            'bank_account_type.required' => 'Bank account type is required',
            'bank_account_type.string' => 'Bank account type must be a string',
            'bank_account_name.required' => 'Bank account name is required',
            'bank_account_name.string' => 'Bank account name must be a string',
            'bank_account_number.required' => 'Bank account number is required',
            'bank_account_branch.required' => 'Bank account branch is required',
            'bank_account_branch.string' => 'Bank account branch must be a string',
            'mobile_bank_name.required' => 'Mobile bank name is required',
            'mobile_bank_name.string' => 'Mobile bank name must be a string',
            'mobile_number.required' => 'Mobile number is required'
        ];
    }
}
