@extends('admin.layouts.master')
@section('title', __('Supplier Advance Pay'))
@section('content')
    <div class="main-content">
        <section class="section">


            <div class="section-body">
                <div class="row">
                    <div class="col-12 col-md-12 col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="card-header-title">
                                    <h4 class="section_title">
                                        {{ __('Suppliers Pay Advance') }} </h4>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <div class="well">
                                            <strong class="me-2">{{ __('Name:') }}</strong>{{ $supplier->name }}<br>
                                            <strong class="me-2">{{ __('Mobile:') }}</strong>{{ $supplier->phone }}<br>
                                            <strong class="me-2">{{ __('Email:') }}</strong>{{ $supplier->email }}<br>
                                        </div>
                                    </div>
                                </div>

                                <form class="suppliers_adv_form"
                                    action="{{ route('admin.supplier.advance.pay', $supplier->id) }}" method="POST">
                                    @csrf
                                    <div class="row mt-4">
                                        <div class="col-lg-6">
                                            <label for="note" class="">{{ __('Note') }}</label>
                                            <textarea name="note" class="form-control" placeholder="Note" id="note" rows="5"></textarea>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="row">
                                                <div class="col-xl-6">
                                                    <div class="form-group">
                                                        <label>{{ __('Previous Advance') }}</label>
                                                        <div class="input-group">
                                                            <div class="input-group-text">
                                                                <i class="fas fa-money-check-alt"></i>
                                                            </div>
                                                            <input class="form-control input_number valid" placeholder="0"
                                                                type="text" id="advance"
                                                                value="{{ $supplier->advance }}" aria-required="true"
                                                                aria-invalid="false" autocomplete="off" disabled
                                                                name="advance">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xl-6">
                                                    <div class="form-group">
                                                        <label>{{ __('Paying Advance') }}</label>
                                                        <div class="input-group">
                                                            <div class="input-group-text">
                                                                <i class="far fa-money-bill-alt"></i>
                                                            </div>
                                                            <input class="form-control input_number valid"
                                                                placeholder="Paying Advance" type="number"
                                                                id="paying_amount" aria-required="true" aria-invalid="false"
                                                                autocomplete="off" name="paying_amount" step="0.01">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xl-6">
                                                    <div class="form-group">
                                                        <label>{{ __('Refund Advance') }}</label>
                                                        <div class="input-group">
                                                            <div class="input-group-text">
                                                                <i class="far fa-money-bill-alt"></i>
                                                            </div>
                                                            <input class="form-control input_number valid"
                                                                placeholder="Refund Advance" type="number"
                                                                id="refund_amount" aria-required="true" aria-invalid="false"
                                                                autocomplete="off" name="refund_amount" min="0"
                                                                step="0.01">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xl-6">
                                                    <div class="form-group">
                                                        <label>{{ __('Total Advance') }}</label>
                                                        <div class="input-group">
                                                            <div class="input-group-text">
                                                                <i class="far fa-money-bill-alt"></i>
                                                            </div>
                                                            <input class="form-control input_number valid" readonly
                                                                placeholder="Total Advance" type="number" id="total_amount"
                                                                aria-required="true" aria-invalid="false" required
                                                                autocomplete="off" name="total_amount" step="0.01"
                                                                value="{{ $supplier->advance }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xl-6">
                                                    <div class="form-group">
                                                        <label>{{ __('Date') }}</label>
                                                        <div class="input-group">
                                                            <div class="input-group-text">
                                                                <i class="far fa-calendar-check"></i>
                                                            </div>
                                                            <input class="form-control input_number datepicker"
                                                                name="date" type="text" value="{{ formatDate(now()) }}"
                                                                id="date" aria-required="true" aria-invalid="false" autocomplete="off">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xl-6">
                                                    <div class="form-group">
                                                        <label>{{ __('Paying With') }}</label>
                                                        <div class="input-group">
                                                            <div class="input-group-text">
                                                                <i class="far fa-credit-card"></i>
                                                            </div>
                                                            <select name="payment_type" id=""
                                                                class="form-control">
                                                                <option value="">{{ __('Select Payment Type') }}
                                                                </option>
                                                                @foreach (accountList() as $key => $list)
                                                                    <option value="{{ $key }}"
                                                                        @if ($key == 'cash') selected @endif
                                                                        data-name="{{ $list }}">
                                                                        {{ $list }}
                                                                    </option>
                                                                @endforeach
                                                            </select>

                                                        </div>
                                                        <div class="account">
                                                            <input type="hidden" name="account_id" class="form-control"
                                                                value="cash" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="">
                                                        <button class="btn btn-primary"
                                                            type="submit">{{ __('Pay') }}</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection


@push('js')
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
                $('.account').html(html);
            }

            if ($(this).val() == 'cash' || $(this).val() == 'advance') {
                $('.account').html('');
                $cash =
                    `<input type="hidden" name="account_id" class="form-control" value="${$(this).val()}" readonly>`;
            }
        });

        $('#advance, #paying_amount, #refund_amount').on('input', function() {
            calculateTotalAdvance();
        })


        // calculate total advance
        function calculateTotalAdvance() {
            let total_advance = 0;
            let advance = $('#advance').val() || 0;
            let payingAmount = $('#paying_amount').val() || 0;
            let refund = $('#refund_amount').val() || 0;

            total_advance = parseFloat(advance) + parseFloat(payingAmount) - parseFloat(refund);
            $('#total_amount').val(total_advance.toFixed(2));

        }
    </script>
@endpush
