@if ($value != 'cash')
    <select name="{{ isset($name) ? $name : 'account_id[]' }}" class="form-control" required>
        @foreach ($account as $key => $account)
            @php
                switch ($value) {
                    case 'bank':
                        $html =
                            '<option value="' .
                            $account->id .
                            '"' .
                            ($payment->account_id == $account->id ? ' selected' : '') .
                            '>' .
                            $account->bank_account_number .
                            ' (' .
                            ($account->bank->name ?? '') .
                            ')</option>';

                        echo $html;
                        break;

                    case 'mobile_banking':
                        $html =
                            '<option value="' .
                            $account->id .
                            '"' .
                            ($payment->account_id == $account->id ? ' selected' : '') .
                            '>' .
                            $account->mobile_number .
                            ' (' .
                            $account->mobile_bank_name .
                            ')</option>';
                        echo $html;
                        break;

                    case 'card':
                        $html =
                            '<option value="' .
                            $account->id .
                            '">' .
                            $account->card_number .
                            ' (' .
                            ($account->bank->name ?? '') .
                            ')</option>';
                        break;

                    default:
                        // Handle any other cases if necessary
                        break;
                }
            @endphp
        @endforeach
    </select>
@else
    @php
        if ($value == 'cash' || $value == 'advance') {
            $val = isset($name) ? $name : 'account_id[]';
            $cash = '<input type="text" name="' . $val . '" class="form-control" value="' . $value . '" readonly>';
            echo $cash;
        }
    @endphp
@endif
