@extends('admin.layouts.master')
@section('title', __('Edit Sales Return'))

@section('content')
    <div class="row">
        <div class="col-md-12">

            <form method="POST" action="{{ route('admin.sales.return.update', $return->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="sale_id" value="{{ $return->sale_id }}">
                <div class="card">
                    <div class="card-header">
                        <div class="section_title">{{ __('Edit Sales Return') }}</div>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('Customer Name') }}</label>
                                    <input type="text" class="form-control" name=""
                                        value="{{ $return->customer?->name ?? 'Guest' }}" disabled>
                                    <input type="hidden" name="customer_id" value="{{ $return->customer_id }}">
                                    @error('customer_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('Invoice No') }}</label>
                                    <input type="text" class="form-control" name="invoice" value="{{ $return->invoice }}"
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
                                        value="{{ formatDate($return->order_date) }}" readonly>
                                    @error('order_date')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('Return Date') }}</label>
                                    <input type="text" class="form-control datepicker" name="return_date"
                                        value="{{ old('return_date', formatDate($return->return_date)) }}" autocomplete="off">
                                    @error('return_date')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label>{{ __('Note') }}</label>
                                    <textarea type="text" class="form-control height-80px" name="note">{{ old('note', $return->note) }}</textarea>
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
                                                <th>{{ __('Unit') }}</th>
                                                <th>{{ __('Unit Price') }}</th>
                                                <th>{{ __('Sell Quantity') }}</th>
                                                <th>{{ __('Return Quantity') }}</th>
                                                <th>{{ __('Return Subtotal') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody id="sale_return_table">
                                            @foreach ($return->details as $returnDetail)
                                                @php
                                                    $ingredient = $returnDetail->ingredient;
                                                    $returnUnit = $returnDetail->unit ?? $ingredient?->purchaseUnit ?? $ingredient?->unit;

                                                    // Get original sale detail for this ingredient
                                                    $originalSaleDetail = $sale->products()
                                                        ->where('ingredient_id', $returnDetail->ingredient_id)
                                                        ->first();

                                                    $salePrice = $originalSaleDetail?->price ?? $returnDetail->price;
                                                    $saleQty = $originalSaleDetail?->quantity ?? 0;
                                                @endphp
                                                <tr>
                                                    <td>
                                                        {{ $ingredient?->name ?? 'Unknown' }}
                                                        <input type="hidden" name="ingredient_id[]"
                                                            value="{{ $returnDetail->ingredient_id }}">
                                                        <input type="hidden" name="return_unit_id[]"
                                                            value="{{ $returnUnit?->id }}">
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-info">{{ $returnUnit?->ShortName ?? '-' }}</span>
                                                    </td>
                                                    <td class="sale-price">
                                                        {{ number_format($salePrice, 2) }}
                                                        <input type="hidden" class="form-control" name="price[]"
                                                            value="{{ $salePrice }}">
                                                    </td>
                                                    <td>{{ $saleQty }}</td>
                                                    <td>
                                                        <input type="number" class="form-control return-quantity"
                                                            name="return_quantity[]"
                                                            value="{{ $returnDetail->quantity }}"
                                                            min="0" max="{{ $saleQty }}" step="0.01">
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control return-subtotal"
                                                            name="return_subtotal[]"
                                                            value="{{ number_format($returnDetail->sub_total, 2, '.', '') }}"
                                                            step="0.01" readonly>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            @if($return->details->isEmpty())
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted">
                                                        {{ __('No returnable products found.') }}
                                                    </td>
                                                </tr>
                                            @endif
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
                                                value="{{ $return->return_amount - $return->return_due }}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label>{{ __('Return Amount') }}</label>
                                            <input type="number" class="form-control" name="return_amount"
                                                value="{{ $return->return_amount }}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label>{{ __('Paying Amount') }}</label>
                                            <input type="number" class="form-control" name="paying_amount"
                                                value="{{ $return->return_amount - $return->return_due }}">
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
                                                            @if ($key == ($payment?->account?->account_type ?? 'cash')) selected @endif
                                                            data-name="{{ $list }}">{{ $list }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group payment_methods">
                                            @if ($payment && $payment->account && $payment->account->account_type != 'cash')
                                                @php
                                                    $filteredAccounts = $accounts->where('account_type', $payment->account->account_type);
                                                @endphp
                                                <select name="account_id" class="form-control">
                                                    @foreach($filteredAccounts as $account)
                                                        <option value="{{ $account->id }}" @if($account->id == $payment->account_id) selected @endif>
                                                            @if($account->account_type == 'bank')
                                                                {{ $account->bank_account_number }} ({{ $account->bank?->name }})
                                                            @elseif($account->account_type == 'mobile_banking')
                                                                {{ $account->mobile_number }} ({{ $account->mobile_bank_name }})
                                                            @elseif($account->account_type == 'card')
                                                                {{ $account->card_number }} ({{ $account->bank?->name }})
                                                            @else
                                                                {{ $account->name ?? $account->id }}
                                                            @endif
                                                        </option>
                                                    @endforeach
                                                </select>
                                            @else
                                                <input type="hidden" name="account_id" class="form-control" value="cash" readonly>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="card-action d-flex justify-content-end">
                                            <a href="{{ route('admin.sales.return.list') }}"
                                                class="btn me-2 btn-danger">{{ __('Cancel') }}</a>
                                            <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
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
            // Initial calculation
            calculateSummary();

            // return quantity calculation
            $(document).on('input', '.return-quantity', function() {
                let return_quantity = parseFloat($(this).val()) || 0;
                let price = parseFloat($(this).closest('tr').find('input[name="price[]"]').val()) || 0;
                let return_subtotal = return_quantity * price;
                $(this).closest('tr').find('.return-subtotal').val(return_subtotal.toFixed(2));
                calculateSummary();
            });

            // calculate summary
            function calculateSummary() {
                let total_return_subtotal = 0;
                $('.return-subtotal').each(function() {
                    total_return_subtotal += parseFloat($(this).val()) || 0;
                });
                $('input[name="return_amount"]').val(total_return_subtotal.toFixed(2));
            }

            const accountsList = @json($accounts);

            // payment type change handler
            $(document).on('change', 'select[name="payment_type"]', function() {
                let payment_type = $(this).val();
                let payment_method = $(this).find(':selected').data('name');
                $('input[name="payment_method"]').val(payment_method);

                const accounts = accountsList.filter(account => account.account_type == payment_type);

                if (payment_type == 'cash' || payment_type == 'advance') {
                    $('.payment_methods').html(
                        `<input type="hidden" name="account_id" class="form-control" value="${payment_type}" readonly>`
                    );
                } else if (accounts.length > 0) {
                    let html = '<select name="account_id" class="form-control">';
                    accounts.forEach(account => {
                        let label = '';
                        switch (payment_type) {
                            case 'bank':
                                label = `${account.bank_account_number} (${account.bank?.name || ''})`;
                                break;
                            case 'mobile_banking':
                                label = `${account.mobile_number} (${account.mobile_bank_name || ''})`;
                                break;
                            case 'card':
                                label = `${account.card_number} (${account.bank?.name || ''})`;
                                break;
                            default:
                                label = account.name || account.id;
                        }
                        html += `<option value="${account.id}">${label}</option>`;
                    });
                    html += '</select>';
                    $('.payment_methods').html(html);
                } else {
                    $('.payment_methods').html('');
                }
            });
        });
    </script>
@endpush
