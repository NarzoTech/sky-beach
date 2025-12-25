@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Supplier Due Pay') }}</title>
@endsection

@section('content')
    <div class="main-content">
        <section class="section">

            <div class="section-body">
                <div class="row">
                    <div class="col-md-12">

                        <form method="POST" action="{{ route('admin.suppliers.due-pay-store', $supplier->id) }}"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="card">
                                <div class="card-header">
                                    <div class="card-header-title">
                                        <h4 class="section_title">{{ __('Supplier Due Pay') }}</h4>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6 class="mb-2">
                                                {{ __('Name') }}: {{ $supplier->name }}
                                            </h6>
                                            <h6 class="mb-2">
                                                {{ __('Phone') }}: {{ $supplier->phone }}
                                            </h6>
                                            <h6 class="mb-2">
                                                {{ __('Address') }}: {{ $supplier->address }}
                                            </h6>
                                        </div>
                                    </div>
                                    <div class="row mt-5">
                                        <div class="col-md-12">
                                            <div class="table-responsive">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>
                                                                <div class="custom-checkbox custom-control">
                                                                    <input type="checkbox" data-checkboxes="checkgroup"
                                                                        data-checkbox-role="dad"
                                                                        class="custom-control-input" id="checkbox-all">
                                                                    <label for="checkbox-all"
                                                                        class="custom-control-label">&nbsp;</label>
                                                                </div>
                                                            </th>
                                                            <th>{{ __('Invoice No') }}</th>
                                                            <th>{{ __('Purchase Date') }}</th>
                                                            <th>{{ __('Invoice Amount') }}</th>
                                                            <th>{{ __('Due Amount') }}</th>
                                                            <th>{{ __('Paying Amount') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="purchase_table">
                                                        @foreach ($supplier->duePurchase as $purchase)
                                                            <tr>
                                                                <td>
                                                                    <div class="custom-checkbox custom-control">
                                                                        <input type="checkbox" data-checkboxes="checkgroup"
                                                                            class="custom-control-input"
                                                                            id="checkbox-{{ $purchase->id }}"
                                                                            name="select">
                                                                        <label for="checkbox-{{ $purchase->id }}"
                                                                            class="custom-control-label">&nbsp;</label>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <input type="text" class="form-control"
                                                                        name="invoice_no[]"
                                                                        value="{{ $purchase->invoice_number }}" readonly>
                                                                </td>
                                                                <td>
                                                                    {{ formatDate($purchase->purchase_date) }}
                                                                </td>
                                                                <td>
                                                                    {{ currency($purchase->total_amount) }}
                                                                </td>
                                                                <td>
                                                                    {{ currency($purchase->due_amount) }}
                                                                </td>
                                                                <td>
                                                                    <input type="text" class="form-control"
                                                                        name="amount[]" value="0">
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-5 justify-content-end">
                                        <div class="col-md-5">
                                            {{-- summery --}}
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label>{{ __('Total Payable') }}</label>
                                                        <div class="input-group">
                                                            <div class="input-group-text">
                                                                <i class="fas fa-money-check-alt"></i>
                                                            </div>
                                                            <input type="number" class="form-control" name="total_payable"
                                                                value="{{ $supplier->duePurchase->sum('due_amount') }}"
                                                                readonly step="0.01">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label>{{ __('Paying Amount') }}</label>
                                                        <input type="number" class="form-control" name="paying_amount" step="0.01">
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label>{{ __('Paying Date') }}</label>
                                                        <input type="text" class="form-control datepicker"
                                                            name="payment_date" value="{{ formatDate(now()) }}"
                                                            autocomplete="off">
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    @include('components.account-type')
                                                </div>
                                            </div>

                                            <div class="card-action d-flex justify-content-end">
                                                <a href="{{ route('admin.purchase.index') }}"
                                                    class="btn btn-danger me-2">{{ __('Cancel') }}</a>
                                                <button type="submit" class="btn btn-success">{{ __('Submit') }}</button>
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
        $(document).ready(function() {
            'use strict';

            //check all checkboxes
            $('#checkbox-all').on('click', function() {
                var $this = $(this);
                var check = $this.prop('checked');
                $('input[name="select"]').each(function() {
                    $(this).prop('checked', check);

                    // change the count number
                    if (check) {
                        $('.number').text($('input[name="select"]').length);
                        $('.delete-section').removeClass('d-none');
                        $('.delete-section').addClass('d-flex');


                        // get the due amount of selected items
                        let total_due = 0;
                        $('input[name="select"]:checked').each(function() {
                            let due = $(this).closest('tr').find('td:eq(4)').text();
                            // remove icon
                            due = due.replace(/[^0-9.]/g, '');
                            total_due += parseFloat(due);
                        });

                        // set the total due amount to nearest input field
                        $('input[name="amount[]"]').val(total_due);

                    } else {
                        $('.number').text(0);
                        $('.delete-section').addClass('d-none');
                        $('.delete-section').removeClass('d-flex');

                        $('input[name="amount[]"]').val(0);
                    }

                    totalAmount()
                });
            });

            $('input[name="select"]').on('click', function() {
                var total = $('input[name="select"]').length;
                var number = $('input[name="select"]:checked').length;
                if (total == number) {
                    $('#checkbox-all').prop('checked', true);
                } else {
                    $('#checkbox-all').prop('checked', false);
                }
                $('.number').text(number);

                if (number > 0) {
                    $('.delete-section').removeClass('d-none');
                    $('.delete-section').addClass('d-flex');
                } else {
                    $('.delete-section').addClass('d-none');
                    $('.delete-section').removeClass('d-flex');
                }


                // get the due amount of selected items
                let total_due = 0;
                $('input[name="select"]:checked').each(function() {
                    let due = $(this).closest('tr').find('td:eq(4)').text();
                    // remove icon
                    due = due.replace(/[^0-9.]/g, '');
                    total_due += parseFloat(due);

                    // set the total due amount to nearest input field
                    $(this).closest('tr').find('input[name="amount[]"]').val(due);
                });



                // $('input[name="amount[]"]').val(total_due);

                if (number == 0) {
                    $('input[name="paying_amount"]').val(0);
                }

                totalAmount()
            });

            $('[name="amount[]"]').on('input', function() {
                const value = $(this).val();

                if (value > 0) {
                    $(this).closest('tr').find('input[name="select"]').prop('checked', true);
                } else {
                    $(this).closest('tr').find('input[name="select"]').prop('checked', false);
                }

                // check checkbox-all if all are checked
                var total = $('input[name="select"]').length;
                var number = $('input[name="select"]:checked').length;
                if (total == number) {
                    $('#checkbox-all').prop('checked', true);
                } else {
                    $('#checkbox-all').prop('checked', false);
                }

                totalAmount()
            })

            $('input[name="paying_amount"]').on('input', function() {
                let value = parseFloat($(this).val());


                // reset all the amount
                $('input[name="amount[]"]').val(0);

                // uncheck checkbox-all
                $('#checkbox-all').prop('checked', false);

                // uncheck all the checkbox
                $('input[name="select"]').prop('checked', false);
                $('.number').text(0);
                $('.delete-section').addClass('d-none');
                $('.delete-section').removeClass('d-flex');


                // get all the row
                $('input[name="amount[]"]').each(function() {
                    // due amount the previous sibling
                    let due = $(this).closest('tr').find('td:eq(4)').text();
                    // remove icon
                    due = parseFloat(due.replace(/[^0-9.]/g, ''));

                    // calculate the due amount
                    if (value <= due) {
                        if (value > 0) {
                            $(this).val(value);
                            $(this).closest('tr').find('input[name="select"]').prop('checked',
                                true);
                            value = value - due;
                        }

                    } else {
                        if (due > 0) {
                            $(this).val(due);
                            value = value - due;
                            $(this).closest('tr').find('input[name="select"]').prop('checked',
                                true);
                        }
                    }
                });

                // check checkbox-all if all are checked
                var total = $('input[name="select"]').length;
                var number = $('input[name="select"]:checked').length;
                if (total == number) {
                    $('#checkbox-all').prop('checked', true);
                } else {
                    $('#checkbox-all').prop('checked', false);
                }

            })
        });


        function totalAmount() {
            let total = 0;
            $('input[name="amount[]"]').each(function() {
                total += parseFloat($(this).val() || 0);
            });
            $('input[name="paying_amount"]').val(total);
        }
    </script>
@endpush
