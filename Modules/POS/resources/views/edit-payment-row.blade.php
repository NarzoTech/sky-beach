@foreach ($sale->payment as $index => $payment)
    <tr data-counter="1">
        <td>
            <select name="payment_type[]" class="form-control pay_by" required>
                @foreach (accountList() as $key => $list)
                    <option value="{{ $key }}" @if ($key == $payment->account->account_type) selected @endif
                        data-name="{{ $list }}">{{ $list }}
                    </option>
                @endforeach
            </select>
        </td>
        <td class="account_info">
            <div class="form-group mb-0">
                @php
                    $account = $accounts->where('account_type', $payment->account->account_type);
                    $html = '';
                @endphp
                @if ($account)
                    @if ($payment->account->account_type == 'cash')
                        <input type="text" name="account_id[]" class="form-control" value="Cash" readonly>
                    @else
                        <select name="account_id[]" class="form-control" required>
                        @foreach ($account as $key => $list)
                            @include('accounts::payment', [
                                'html' => $html,
                                'account' => $list,
                                'value' => $payment->account->account_type,
                            ])
                        @endforeach
                        </select>
                    @endif
                @endif
            </div>
        </td>
        <td>
            <div class="form-group mb-0">
                <input type="text" name="paying_amount[]" class="form-control text-center paying_amount"
                    id="payingAmount" placeholder="Amount" required autocomplete="off" value="{{ $payment->amount }}">
            </div>
        </td>
        <td>
            <div class="btn-group btn-group-sm">
                @if ($index > 0)
                    <a href="javascript:0" class="btn btn-sm btn-danger remove-payment">
                        <i class="fa fa-trash"></i>
                    </a>
                @endif
                @if ($index == 0)
                    <a href="javascript:0" class="btn btn-sm btn-primary add-payment">
                        <i class="fa fa-plus"></i>
                    </a>
                @endif

            </div>
        </td>
    </tr>
@endforeach
