@php
    $type = request()->pay;
@endphp
@extends('admin.layouts.master')
@section('title')
    <title>{{ $type == 1 ? __('Pay Salary') : __('Pay Advance') }}</title>
@endsection


@section('content')
    <div class="main-content">
        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between">
                                <h4 class="section_title">{{ $type == 1 ? __('Pay Salary') : __('Pay Advance') }}</h4>
                                <div>
                                    <a href="{{ route('admin.employee.index') }}" class="btn btn-primary"><i
                                            class="fas fa-arrow-left"></i>{{ __('Back') }}</a>
                                </div>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('admin.employee.salary.store', $employee->id) }}" method="post"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="type" value="{{ request('pay') }}">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="">{{ __('Employee Name') }}<span
                                                        class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="name"
                                                    value="{{ $employee->name }}" readonly required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="salary"
                                                    class="">{{ __('Employee Monthly Salary') }}</label>
                                                <input type="text" id="salary" name="salary"
                                                    value="{{ $employee->salary }}" class="form-control" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="payable_salary"
                                                    class="">{{ __('Employee Payable Salary') }}</label>
                                                <input type="text" id="payable_salary" name="payable_salary"
                                                    value="{{ $payableSalary }}" class="form-control" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="date"
                                                    class="col-form-label p-0">{{ __('Salary Date') }}</label>
                                                <input type="text" name="date" id="date"
                                                    value="{{ old('date', formatDate(now())) }}"
                                                    class="form-control datepicker" autocomplete="off">
                                            </div>
                                        </div>
                                        @php
                                            $months = [
                                                'January',
                                                'February',
                                                'March',
                                                'April',
                                                'May',
                                                'June',
                                                'July',
                                                'August',
                                                'September',
                                                'October',
                                                'November',
                                                'December',
                                            ];
                                        @endphp
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="month"
                                                    class="col-form-label p-0">{{ __('Month') }}</label>
                                                <select class="form-control select2" name="month" id="month">
                                                    <option value="">{{ __('Select') }}</option>
                                                    @foreach ($months as $key => $month)
                                                        <option value="{{ $month }}"
                                                            {{ formatDate(now(), 'F') == $month ? 'selected' : '' }}>
                                                            {{ $month }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="salary" class="">{{ __('Salary Year') }}</label>
                                                <input type="number" id="year" name="year"
                                                    value="{{ now()->year }}" placeholder="Salary Year"
                                                    class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="salary"
                                                    class="col-form-label p-0">{{ __('Already Taken') }}</label>
                                                <input type="number" name="already_salary" id="already_salary"
                                                    value="{{ $paidAmount }}" placeholder="0" class="form-control" step="0.01">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="">{{ __('Payment Type') }}<span
                                                        class="text-danger">*</span></label>
                                                <select name="payment_type" id="payment_type"
                                                    class="form-control payment_type" required>
                                                    <option value="" disabled selected>
                                                        {{ __('Select Payment Type') }}
                                                    </option>
                                                    @foreach (accountList() as $key => $list)
                                                        <option value="{{ $key }}"
                                                            data-name="{{ $list }}">
                                                            {{ $list }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div id="account">

                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="salary" class="col-form-label">{{ __('Pay Amount') }}</label>
                                                <input type="text" name="amount" id="amount"
                                                    value="{{ old('amount', $payableSalary - $employee->paidAmount) }}"
                                                    placeholder="Pay Amount" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="">{{ __('Note') }}</label>
                                                <textarea name="note" id="" rows="3" class="form-control" placeholder="Note">{{ old('note') }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="text-center offset-md-2 col-md-8">
                                            <x-admin.save-button :text="__('Save')">
                                            </x-admin.save-button>
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
        $(document).ready(function() {

            const accountsList = @json($accounts);
            $('[name="payment_type"]').on('change', function() {
                const accounts = accountsList.filter(account => account.account_type == $(this).val());
                const accountInput = $('#account');
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


                    accountInput.html(html);
                }

                if ($(this).val() == 'cash') {
                    accountInput.html('');
                    const cash =
                        `<input type="text" name="account_id" class="form-control" value="${$(this).val()}" readonly>`;

                    accountInput.html(cash);
                }
            })

            $('#month, #year').on('change', function() {
                const month = $('#month').val();
                const year = $('#year').val();
                $.ajax({
                    type: "GET",
                    url: "{{ route('admin.employee.salary.info', $employee->id) }}",
                    data: {
                        month: month,
                        year: year,
                        type: "{{ request('pay') }}"
                    },
                    success: function(data) {
                        $('#already_salary').val(data.advanceAmount);
                        $('#amount').val(data.dueAmount);
                        $('#payable_salary').val(data.payableSalary);
                    }
                })
            })
        });
    </script>
@endpush
