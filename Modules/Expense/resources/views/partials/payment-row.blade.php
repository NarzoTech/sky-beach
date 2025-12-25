<tr class="payment-row" data-counter="{{ $counter ?? 1 }}">
    <td>
        <select name="payment_type[]" class="form-control expense-pay-type" required>
            <option value="">{{ __('Select') }}</option>
            @foreach (accountList() as $key => $list)
                <option value="{{ $key }}" @if ($key == 'cash') selected @endif>{{ $list }}</option>
            @endforeach
        </select>
    </td>
    <td class="expense-account-info">
        <input type="hidden" name="account_id[]" value="cash">
        <span class="text-muted">Cash</span>
    </td>
    <td>
        <input type="number" step="0.01" name="paying_amount[]" class="form-control expense-paying-amount" placeholder="{{ __('Amount') }}" required>
    </td>
    <td>
        <div class="btn-group btn-group-sm">
            @if (isset($add) && $add)
                <a href="javascript:;" class="btn btn-sm btn-danger remove-expense-payment">
                    <i class="fa fa-trash"></i>
                </a>
            @else
                <a href="javascript:;" class="btn btn-sm btn-primary add-expense-payment">
                    <i class="fa fa-plus"></i>
                </a>
            @endif
        </div>
    </td>
</tr>
