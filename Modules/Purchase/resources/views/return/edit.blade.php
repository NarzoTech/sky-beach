@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Edit Purchase Return') }}</title>
@endsection

@section('content')
    <div class="main-content">
        <section class="section">

            <div class="section-body">
                <div class="row">
                    <div class="col-md-12">

                        <form method="POST" action="{{ route('admin.purchase.return.update', $return->id) }}"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="purchase_id" value="{{ $return->purchase_id }}">
                            <input type="hidden" name="warehouse_id" value="{{ $purchase->warehouse_id }}">
                            <div class="card">
                                <div class="card-header">
                                    <div class="section_title">{{ __('Edit Purchase Return') }}</div>
                                </div>

                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 col-lg-4">
                                            <div class="form-group">
                                                <label>{{ __('Supplier Name') }}</label>
                                                <input type="text" class="form-control" name=""
                                                    value="{{ $purchase->supplier?->name }}" disabled>
                                                <input type="hidden" name="supplier_id"
                                                    value="{{ $purchase->supplier_id }}">
                                                @error('supplier_id')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-4">
                                            <div class="form-group">
                                                <label>{{ __('Return Date') }}</label>
                                                <input type="text" class="form-control datepicker" name="return_date"
                                                    value="{{ old('return_date', formatDate($return->return_date)) }}" autocomplete="off">
                                                @error('return_date')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-4">
                                            <div class="form-group">
                                                <label>{{ __('Shipping Cost') }}</label>
                                                <input type="text" class="form-control" name="shipping_cost"
                                                    value="{{ $return->shipping_cost }}">
                                                @error('shipping_cost')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-4">
                                            <div class="form-group">
                                                <label>{{ __('Return Type') }}</label>
                                                <select name="return_type_id" id="" class="form-control">
                                                    @foreach ($returnTypes as $type)
                                                        <option value="{{ $type->id }}"
                                                            @if ($type->id == $return->return_type_id) selected @endif>
                                                            {{ $type->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('return_type_id')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-4">
                                            <div class="form-group">
                                                <label>{{ __('Attachment') }}</label>
                                                <input type="file" class="form-control" name="attachment"
                                                    value="{{ old('attachment') }}">
                                                @error('attachment')
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
                                    <div class="row mt-5">
                                        <div class="col-md-12">
                                            <div class="table-responsive">
                                                <table class="table">
                                                    <thead>
                                                        <tr>
                                                            <th>{{ __('Ingredient Name') }}</th>
                                                            <th>{{ __('Unit') }}</th>
                                                            <th>{{ __('Purchase Price') }}</th>
                                                            <th>{{ __('Purchase Qty') }}</th>
                                                            <th>{{ __('Stock Qty') }}</th>
                                                            <th>{{ __('Return Qty') }}</th>
                                                            <th>{{ __('Return Subtotal') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="purchase_table">
                                                        @foreach ($return->purchaseDetails as $returnDetail)
                                                            @php
                                                                $ingredient = $returnDetail->ingredient;
                                                                $returnUnit = $returnDetail->unit ?? $ingredient?->purchaseUnit ?? $ingredient?->unit;

                                                                // Get original purchase detail for this ingredient
                                                                $originalPurchaseDetail = $purchase->purchaseDetails()
                                                                    ->where('ingredient_id', $returnDetail->ingredient_id)
                                                                    ->first();

                                                                $purchasePrice = $originalPurchaseDetail?->purchase_price ?? 0;
                                                                $purchaseQty = $originalPurchaseDetail?->quantity ?? 0;
                                                            @endphp
                                                            <tr>
                                                                <td>
                                                                    {{ $ingredient?->name }}
                                                                    <input type="hidden" name="ingredient_id[]"
                                                                        value="{{ $returnDetail->ingredient_id }}">
                                                                    <input type="hidden" name="return_unit_id[]"
                                                                        value="{{ $returnUnit?->id }}">
                                                                </td>
                                                                <td>
                                                                    <span class="badge bg-info">{{ $returnUnit?->ShortName ?? '-' }}</span>
                                                                </td>
                                                                <td class="purchase-price">
                                                                    {{ number_format($purchasePrice, 2) }}
                                                                </td>
                                                                <td>
                                                                    {{ $purchaseQty }}
                                                                </td>
                                                                <td>
                                                                    {{ $ingredient?->stock ?? 0 }}
                                                                    <small class="text-muted">{{ $ingredient?->purchaseUnit?->ShortName ?? $ingredient?->unit?->ShortName ?? '' }}</small>
                                                                </td>
                                                                <td>
                                                                    <input type="number" class="form-control return-quantity"
                                                                        name="return_quantity[]"
                                                                        value="{{ $returnDetail->quantity }}"
                                                                        min="0" max="{{ $purchaseQty }}" step="0.01">
                                                                </td>
                                                                <td>
                                                                    <input type="number" class="form-control return-subtotal"
                                                                        name="return_subtotal[]"
                                                                        value="{{ number_format($purchasePrice * $returnDetail->quantity, 2, '.', '') }}"
                                                                        readonly step="0.01">
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- summery --}}
                                    <div class="row justify-content-end mt-5">
                                        <div class="col-xxl-5 col-xl-6 col-lg-7">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label>{{ __('Paid Amount') }}</label>
                                                        <input type="number" class="form-control" name="paid_amount"
                                                            value="{{ $purchase->paid_amount }}" readonly step="0.01">
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label>{{ __('Invoice Amount') }}</label>
                                                        <input type="number" class="form-control"
                                                            name="invoice_amount"
                                                            value="{{ $return->return_amount }}" readonly step="0.01">
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label>{{ __('Received Amount') }}</label>
                                                        <input type="number" class="form-control"
                                                            name="received_amount"
                                                            value="{{ $return->received_amount ?? 0 }}" step="0.01">
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label>{{ __('Payment Type') }}</label>
                                                        <select name="payment_type" id="" class="form-control">
                                                            <option value="">{{ __('Select Payment Type') }}
                                                            </option>
                                                            @foreach (accountList() as $key => $list)
                                                                <option value="{{ $key }}"
                                                                    @if ($key == ($payment?->account?->account_type ?? 'cash')) selected @endif
                                                                    data-name="{{ $list }}">
                                                                    {{ $list }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-group row">
                                                        <div class="col-4">
                                                            <input type="text" class="form-control"
                                                                name="payment_method" value="{{ $payment?->account?->account_type ?? 'cash' }}" readonly>
                                                        </div>
                                                        <div class="col-8 account-select">
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
                                                                <input type="text" class="form-control"
                                                                    name="account_id" value="cash" readonly>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="card-action d-flex justify-content-end">
                                                <a href="{{ route('admin.purchase.index') }}"
                                                    class="btn me-2 btn-danger">{{ __('Cancel') }}</a>
                                                <button type="submit"
                                                    class="btn btn-primary">{{ __('Submit') }}</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('js')
    <script>
        'use strict';

        $(document).ready(function() {
            const accounts = @json($accounts ?? []);

            // Initial calculation
            calculateSummery();

            // return quantity
            $(document).on('input', '.return-quantity', function() {
                let return_quantity = parseFloat($(this).val()) || 0;
                let purchase_price = parseFloat($(this).closest('tr').find('.purchase-price').text().replace(/,/g, '')) || 0;
                let return_subtotal = return_quantity * purchase_price;
                $(this).closest('tr').find('.return-subtotal').val(return_subtotal.toFixed(2));
                calculateSummery();
            });

            // calculate summery
            function calculateSummery() {
                let total_return_subtotal = 0;
                $('.return-subtotal').each(function() {
                    total_return_subtotal += parseFloat($(this).val()) || 0;
                });
                $('input[name="invoice_amount"]').val(total_return_subtotal.toFixed(2));
            }

            // payment type
            $(document).on('change', 'select[name="payment_type"]', function() {
                let payment_type = $(this).val();
                let payment_method = $(this).find(':selected').data('name');
                $('input[name="payment_method"]').val(payment_method);

                // Update account selection based on payment type
                const accountSelect = $('.account-select');
                if (payment_type === 'cash') {
                    accountSelect.html('<input type="text" class="form-control" name="account_id" value="cash" readonly>');
                } else {
                    const filteredAccounts = accounts.filter(a => a.account_type === payment_type);
                    if (filteredAccounts.length > 0) {
                        let html = '<select name="account_id" class="form-control">';
                        filteredAccounts.forEach(account => {
                            let label = '';
                            switch(payment_type) {
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
                        accountSelect.html(html);
                    } else {
                        accountSelect.html('<input type="text" class="form-control" name="account_id" value="" placeholder="No accounts available" readonly>');
                    }
                }
            });
        });
    </script>
@endpush
