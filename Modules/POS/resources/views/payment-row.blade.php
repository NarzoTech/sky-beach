<div class="payment-row mb-2 p-2 border rounded {{ isset($add) ? '' : 'bg-light' }}" data-counter="{{ $counter ?? 1 }}">
    <div class="row g-2 align-items-center">
        <div class="col-4">
            <select name="payment_type[]" class="form-select form-select-sm pay_by" required>
                @foreach (accountList() as $key => $list)
                    <option value="{{ $key }}" @if ($key == 'cash') selected @endif data-name="{{ $list }}">
                        {{ $list }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-4 account_info">
            <input type="text" name="account_id[]" class="form-control form-control-sm" value="cash" readonly>
        </div>
        <div class="col-3">
            <input type="number" name="paying_amount[]" class="form-control form-control-sm text-center paying_amount"
                placeholder="{{ __('Amount') }}" required autocomplete="off" step="0.01">
        </div>
        <div class="col-1 text-center">
            @if (isset($add))
                <button type="button" class="btn btn-sm btn-outline-danger remove-payment">
                    <i class="fas fa-times"></i>
                </button>
            @endif
        </div>
    </div>
</div>
