<div class="payment-row mb-2 p-2 border rounded {{ isset($add) ? '' : 'bg-light' }}" data-counter="{{ $counter ?? 1 }}">
    <div class="row g-2 align-items-center">
        <div class="col-5">
            <select name="payment_type[]" class="form-select form-select-sm pay_by" required>
                @foreach (accountList() as $key => $list)
                    <option value="{{ $key }}" @if ($key == 'cash') selected @endif data-name="{{ $list }}">
                        {{ $list }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-5">
            <input type="number" name="paying_amount[]" class="form-control form-control-sm text-center paying_amount"
                id="payingAmount" placeholder="{{ __('Amount') }}" required autocomplete="off" step="0.01">
            <input type="hidden" name="account_id[]" value="Cash" class="account_id_input">
        </div>
        <div class="col-2 text-center">
            @if (isset($add))
                <button type="button" class="btn btn-sm btn-outline-danger remove-payment">
                    <i class="fas fa-times"></i>
                </button>
            @endif
        </div>
    </div>
</div>
