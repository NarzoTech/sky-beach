<tr data-counter="1">
    <td>
        <select name="payment_type[]" class="form-control pay_by" required>
            @foreach (accountList() as $key => $list)
                <option value="{{ $key }}" @if ($key == 'cash') selected @endif
                    data-name="{{ $list }}">{{ $list }}
                </option>
            @endforeach
        </select>
    </td>
    <td class="account_info">
        <div class="form-group mb-0">
            <input type="text" name="account_id[]" value="Cash" class="form-control" readonly>
        </div>
    </td>
    <td>
        <div class="form-group mb-0">
            <input type="text" name="paying_amount[]" class="form-control text-center paying_amount"
                id="payingAmount" placeholder="Amount" required autocomplete="off">
        </div>
    </td>
    <td>
        <div class="btn-group btn-group-sm">
            @if (isset($add))
                <a href="javascript:0" class="btn btn-sm btn-danger remove-payment">
                    <i class="fa fa-trash"></i>
                </a>
            @else
                <a href="javascript:0" class="btn btn-sm btn-primary add-payment">
                    <i class="fa fa-plus"></i>
                </a>
            @endif


        </div>
    </td>
</tr>
