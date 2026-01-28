@foreach ($sale->payment as $index => $payment)
<div class="payment-row mb-2 p-2 border rounded bg-light" data-counter="{{ $index + 1 }}">
    <div class="row g-2 align-items-center">
        <div class="col-4">
            <select name="payment_type[]" class="form-select form-select-sm pay_by" required>
                @foreach (accountList() as $key => $list)
                    <option value="{{ $key }}" @if ($key == $payment->account->account_type) selected @endif
                        data-name="{{ $list }}">{{ $list }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-4 account_info">
            @php
                $account = $accounts->where('account_type', $payment->account->account_type);
            @endphp
            @if ($account)
                @if ($payment->account->account_type == 'cash')
                    <input type="text" name="account_id[]" class="form-control form-control-sm" value="Cash" readonly>
                @else
                    <select name="account_id[]" class="form-control form-control-sm" required>
                    @foreach ($account as $key => $list)
                        @php
                            $selected = $payment->account_id == $list->id ? 'selected' : '';
                            $displayText = '';
                            switch($list->account_type) {
                                case 'bank':
                                    $displayText = $list->bank_account_number . ' (' . ($list->bank->name ?? '') . ')';
                                    break;
                                case 'mobile_banking':
                                    $displayText = $list->mobile_number . ' (' . $list->mobile_bank_name . ')';
                                    break;
                                case 'card':
                                    $displayText = $list->card_number . ' (' . ($list->bank->name ?? '') . ')';
                                    break;
                                default:
                                    $displayText = $list->name ?? $list->account_type;
                            }
                        @endphp
                        <option value="{{ $list->id }}" {{ $selected }}>{{ $displayText }}</option>
                    @endforeach
                    </select>
                @endif
            @endif
        </div>
        <div class="col-3">
            <input type="number" name="paying_amount[]" class="form-control form-control-sm text-center paying_amount"
                placeholder="{{ __('Amount') }}" required autocomplete="off" step="0.01" value="{{ $payment->amount }}">
        </div>
        <div class="col-1 text-center">
            @if ($index > 0)
                <button type="button" class="btn btn-sm btn-outline-danger remove-payment">
                    <i class="fas fa-times"></i>
                </button>
            @endif
        </div>
    </div>
</div>
@endforeach
