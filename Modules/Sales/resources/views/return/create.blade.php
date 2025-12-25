@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Sales Return') }}</title>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">

            <form method="POST" action="{{ route('admin.sales.return.store') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="sale_id" value="{{ $sale->id }}">
                <div class="card">
                    <div class="card-header">
                        <div class="section_title">{{ __('Sales Return') }}</div>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('Customer Name') }}</label>
                                    <input type="text" class="form-control" name=""
                                        value="{{ $sale->user?->name ?? 'Guest' }}" disabled>
                                    <input type="hidden" name="customer_id" value="{{ $sale->customer_id }}">
                                    @error('customer_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('Invoice No') }}</label>
                                    <input type="text" class="form-control" name="invoice" value="{{ $sale->invoice }}"
                                        readonly>
                                    @error('invoice')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('Sale Date') }}</label>
                                    <input type="text" class="form-control" name="order_date"
                                        value="{{ formatDate($sale->order_date) }}" readonly>
                                    @error('order_date')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('Return Date') }}</label>
                                    <input type="text" class="form-control datepicker" name="return_date"
                                        value="{{ old('return_date', formatDate(now())) }}" autocomplete="off">
                                    @error('return_date')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label>{{ __('Note') }}</label>
                                    <textarea type="text" class="form-control height-80px" name="note">{{ old('note') }}</textarea>
                                    @error('note')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Product Name') }}</th>
                                                <th>{{ __('Unit Price') }}</th>
                                                <th>{{ __('Sell Quantity') }}</th>
                                                <th>{{ __('Return Quantity') }}</th>
                                                <th>{{ __('Return Subtotal') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody id="sale_return_table">
                                            @foreach ($sale->products as $product)
                                                <tr>
                                                    <td>{{ $product->product->name }}
                                                        <input type="hidden" name="product_id[]"
                                                            value="{{ $product->product_id }}">
                                                    </td>
                                                    <td>{{ $product->price }}
                                                        <input type="hidden" class="form-control" name="price[]"
                                                            value="{{ $product->price }}">
                                                    </td>
                                                    <td>{{ $product->quantity }}</td>
                                                    <td>
                                                        <input type="number" class="form-control" name="return_quantity[]">
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control" name="return_subtotal[]" step="0.01">
                                                    </td>

                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="row justify-content-end">
                            <div class="col-xl-5 col-lg-7 col-md-8">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label>{{ __('Paid Amount') }}</label>
                                            <input type="number" class="form-control" name="paid_amount"
                                                value="{{ $sale->payment->sum('amount') }}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label>{{ __('Return Amount') }}</label>
                                            <input type="return_amount" class="form-control" name="return_amount"
                                                value="0">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label>{{ __('Paying Amount') }}</label>
                                            <input type="paying_amount" class="form-control" name="paying_amount"
                                                value="0">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label>{{ __('Pay By') }}</label>
                                            <div class="pyment-method">
                                                <select name="payment_type" id="" class="form-control">
                                                    <option value="">{{ __('Select Payment Type') }}
                                                    </option>
                                                    @foreach (accountList() as $key => $list)
                                                        <option value="{{ $key }}"
                                                            @if ($key == 'cash') selected @endif
                                                            data-name="{{ $list }}">{{ $list }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="card-action d-flex justify-content-end">
                                            <a href="{{ route('admin.purchase.index') }}"
                                                class="btn me-2 btn-danger">{{ __('Cancel') }}</a>
                                            <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('js')
    <script>
        'use strict';

        $(document).ready(function() {
            // return quantity
            $(document).on('input', 'input[name="return_quantity[]"]', function() {
                let return_quantity = $(this).val();
                let purchase_price = $(this).closest('tr').find('td:eq(1)').text();
                let return_subtotal = return_quantity * purchase_price;
                $(this).closest('tr').find('input[name="return_subtotal[]"]').val(return_subtotal);
                calculateSummery();
            });

            // calculate summery
            function calculateSummery() {
                let total_return_subtotal = 0;
                let total_return_quantity = 0;
                let total_invoice_amount = 0;
                let total_received_amount = 0;
                let total_paid_amount = 0;
                let total_due_amount = 0;
                $('input[name="return_subtotal[]"]').each(function() {
                    total_return_subtotal += parseFloat($(this).val() ? $(this).val() : 0);
                });
                $('input[name="return_quantity[]"]').each(function() {
                    total_return_quantity += parseFloat($(this).val());
                });
                $('input[name="return_amount"]').val(total_return_subtotal);
                $('input[name="received_amount"]').val(total_return_subtotal);
            }

            // payment type
            $(document).on('change', 'select[name="payment_type"]', function() {
                let payment_type = $(this).val();
                let payment_method = $(this).find(':selected').data('name');
                $('input[name="payment_method"]').val(payment_method);
            });

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

                    $('.payment_methods').html(html);
                }

                if ($(this).val() == 'cash' || $(this).val() == 'advance') {
                    $('.payment_methods').html('');
                    const cash =
                        `<input type="hidden" name="account_id" class="form-control" value="${$(this).val()}" readonly>`;

                    $('.payment_methods').html(cash);
                }
            });
        });
    </script>
@endpush
