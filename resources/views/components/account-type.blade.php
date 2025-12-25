<div class="form-group">
    <label>
        @if (isset($text))
            {{ $text . 'With' }}
        @else
            {{ __('Paying With') }}
        @endif
    </label>
    <select name="payment_type" id="" class="form-control">
        <option value="">{{ __('Select Payment Type') }}
        </option>
        @foreach (accountList() as $key => $list)
            <option value="{{ $key }}" @if ($key == 'cash') selected @endif
                data-name="{{ $list }}">{{ $list }}
            </option>
        @endforeach
    </select>
    <div class="mt-2 account">
        <input type="hidden" name="account_id" class="form-control" value="cash" readonly>
    </div>
</div>


<script>
    const accountsList = @json($accounts);

    $(document).on('change', 'select[name="payment_type"]', function() {
        const accounts = accountsList.filter(account => account.account_type == $(this).val());

        if (accounts) {
            let html = '<select name="account_id" id="" class="form-control">';
            accounts.forEach(account => {
                switch ($(this).val()) {
                    case 'bank':
                        html +=
                            `<option value="${account.id}">${account.bank_account_number} (${account.bank?.name})</option>`;
                        break;
                    case "mobile_banking":
                        html +=
                            `<option value="${account.id}">${account.mobile_number}(${account.mobile_bank_name})</option>`;
                        break;
                    case 'card':
                        html +=
                            `<option value="${account.id}">${account.card_number} (${account.bank?.name})</option>`;
                        break;
                    default:
                        break;
                }
            });
            html += '</select>';
            $(this).closest('.form-group').find('.account').html(html);
        }

        if ($(this).val() == 'cash' || $(this).val() == 'advance') {
            $(this).closest('.form-group').find('.account').html('');
            $cash =
                `<input type="hidden" name="account_id" class="form-control" value="${$(this).val()}" readonly>`;
        }
    });
</script>
