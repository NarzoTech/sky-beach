@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Purchase Return') }}</title>
@endsection

@section('content')
    <div class="main-content">
        <section class="section">

            <div class="section-body">
                <div class="row">
                    <div class="col-md-12">

                        <form method="POST" action="{{ route('admin.purchase.return.store', $purchase->id) }}"
                            enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="purchase_id" value="{{ $purchase->id }}">
                            <div class="card">
                                <div class="card-header">
                                    <div class="section_title">{{ __('Purchase Return') }}</div>
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
                                                    value="{{ old('return_date', formatDate(now())) }}"
                                                    autocomplete="off">
                                                @error('return_date')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-4">
                                            <div class="form-group">
                                                <label>{{ __('Shipping Cost') }}</label>
                                                <input type="text" class="form-control" name="shipping_cost"
                                                    value="{{ old('shipping_cost', 0) }}">
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
                                                        <option value="{{ $type->id }}">
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
                                                <textarea type="text" class="form-control height-80px" name="note">{{ old('note') }}</textarea>
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
                                                            <th>{{ __('Product Name') }}</th>
                                                            <th>{{ __('Purchase Price') }}</th>
                                                            <th>{{ __('Purchase Quantity') }}</th>
                                                            <th>{{ __('Returned, Sale') }}</th>
                                                            <th>{{ __('Stock Quantity') }}</th>
                                                            <th>{{ __('Return Quantity') }}</th>
                                                            <th>{{ __('Return Subtotal') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="purchase_table">
                                                        @foreach ($purchase->purchaseDetails as $purchaseDetail)
                                                            <tr>
                                                                <td>
                                                                    {{ $purchaseDetail->product?->name }}
                                                                    <input type="hidden" name="product_id[]"
                                                                        value="{{ $purchaseDetail->product_id }}">
                                                                </td>
                                                                <td>
                                                                    {{ $purchaseDetail->purchase_price }}
                                                                </td>
                                                                <td>
                                                                    {{ $purchaseDetail->quantity }}
                                                                </td>
                                                                <td>
                                                                    {{ $purchaseDetail->returned_sale }}
                                                                </td>
                                                                <td>
                                                                    {{ $purchaseDetail->product?->total_stock }}
                                                                </td>
                                                                <td>
                                                                    <input type="number" class="form-control"
                                                                        name="return_quantity[]" value="0"
                                                                        min="0">
                                                                </td>
                                                                <td>
                                                                    <input type="number" class="form-control"
                                                                        name="return_subtotal[]" value="0" readonly
                                                                        step="0.01">
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
                                                        <input type="invoice_amount" class="form-control"
                                                            name="invoice_amount" value="0" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label>{{ __('Received Amount') }}</label>
                                                        <input type="received_amount" class="form-control"
                                                            name="received_amount" value="0" step="0.01">
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
                                                                    @if ($key == 'cash') selected @endif
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
                                                                name="payment_method" value="cash" readonly>
                                                        </div>
                                                        <div class="col-8">
                                                            <input type="text" class="form-control"
                                                                name="paid_amount">
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
                    total_return_subtotal += parseFloat($(this).val());
                });
                $('input[name="return_quantity[]"]').each(function() {
                    total_return_quantity += parseFloat($(this).val());
                });
                $('input[name="invoice_amount"]').val(total_return_subtotal);
                $('input[name="received_amount"]').val(total_return_subtotal);
            }

            // payment type
            $(document).on('change', 'select[name="payment_type"]', function() {
                let payment_type = $(this).val();
                let payment_method = $(this).find(':selected').data('name');
                $('input[name="payment_method"]').val(payment_method);
            });
        });
    </script>
@endpush
