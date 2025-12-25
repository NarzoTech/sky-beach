<div class="row payment-row mb-5">
    <div class="col-md-12 mt-0">
        <div class="input-group">
            <select name="payment_type[]" id="" class="form-select nice-select rounded me-2">
                <option value="">
                    {{ __('Select Payment Type') }}
                </option>
                @foreach (accountList() as $key => $list)
                    <option value="{{ $key }}" @if ($key == 'cash') selected @endif
                        data-name="{{ $list }}">
                        {{ $list }}
                    </option>
                @endforeach
            </select>
            <div class="input-group-append">
                @if (isset($add))
                    <button class="btn btn-danger removePayment" type="button"><i class="fas fa-trash"></i></button>
                @else
                    <button class="btn btn-success addPayment" type="button"><i class="fa fa-plus"></i></button>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-12 ml-auto">
        <div class="row">
            <div class="col-5 account">
                <input type="text" class="form-control" name="account_id[]" value="cash" readonly>
            </div>
            <div class="col-7">
                <input type="text" class="form-control" name="paid_amount[]" value="0">
            </div>
        </div>
    </div>
</div>
