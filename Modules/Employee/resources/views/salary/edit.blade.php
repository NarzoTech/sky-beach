@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Create Employee') }}</title>
@endsection


@section('content')
    <div class="main-content">
        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between">
                                <h4 class="section_title">{{ __('Edit Salary') }}</h4>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('admin.employee.salary.update', $payment->id) }}" method="post"
                                    enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="type" value="{{ request('pay') }}">

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="">{{ __('Employee Name') }}<span
                                                        class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="name"
                                                    value="{{ $employee->name }}" readonly required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="salary"
                                                    class="">{{ __('Employee Monthly Salary') }}</label>
                                                <input type="text" id="salary" name="salary"
                                                    value="{{ $employee->salary }}" class="form-control" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
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
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="month"
                                                    class="col-form-label p-0">{{ __('Month') }}</label>
                                                <select class="form-control select2" name="month" id="month">
                                                    <option value="">{{ __('Select') }}</option>
                                                    @foreach ($months as $key => $month)
                                                        <option value="{{ $month }}"
                                                            {{ $payment->month == $month ? 'selected' : '' }}>
                                                            {{ $month }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="salary" class="">{{ __('Salary Year') }}</label>
                                                <input type="number" id="year" name="year"
                                                    value="{{ $payment->year }}" placeholder="Salary Year"
                                                    class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="salary"
                                                    class="col-form-label p-0">{{ __('Paid Amount') }}</label>
                                                <input type="text" name="amount" id="amount"
                                                    value="{{ old('amount', $payment->amount) }}" placeholder="Pay Amount"
                                                    class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="">{{ __('Payment Type') }}</label>
                                                <select name="payment_type" id="payment_type"
                                                    class="form-control payment_type" required>
                                                    <option value="" disabled selected>
                                                        {{ __('Select Payment Type') }}
                                                    </option>
                                                    @foreach (accountList() as $key => $list)
                                                        <option value="{{ $key }}"
                                                            data-name="{{ $list }}"
                                                            {{ $key == $payment->payment_type ? 'selected' : '' }}>
                                                            {{ $list }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <div id="account">

                                                </div>
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
                                            <x-admin.update-button :text="__('Update')">
                                            </x-admin.update-button>
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

            manageAccount($('#payment_type'));
            $('[name="payment_type"]').on('change', function() {
                manageAccount(this)
            })

            $('#month, #year').on('change', function() {
                const month = $('#month').val();
                const year = $('#year').val();
                $.ajax({
                    type: "GET",
                    url: "{{ route('admin.employee.salary.info', $employee->id) }}",
                    data: {
                        month: month,
                        year: year
                    },
                    success: function(data) {
                        $('#already_salary').val(data.advanceAmount);
                        $('#amount').val(data.dueAmount);
                    }
                })
            })
        });

        function manageAccount(node) {
            const accountsList = @json($accounts);
            const accounts = accountsList.filter(account => account.account_type == $(node).val());
            const accountInput = $('#account');
            if (accounts) {
                let html = '<select name="account_id" id="" class="form-control">';
                accounts.forEach(account => {
                    const select = '{{ $payment->account_id }}';
                    switch ($(node).val()) {
                        case 'bank':
                            html +=
                                `<option value="${account.id}" ${select == account.id ? 'selected' : ''}>${account.bank_account_number} (${account.bank?.name})</option>`;
                            break;
                        case "mobile_banking":
                            html +=
                                `<option value="${account.id}" ${select == account.id ? 'selected' : ''}>${account.mobile_number}(${account.mobile_bank_name})</option>`;
                            break;
                        case 'card':
                            html +=
                                `<option value="${account.id}" ${select == account.id ? 'selected' : ''}>${account.card_number} (${account.bank?.name})</option>`;
                            break;
                        default:
                            break;
                    }

                });
                html += '</select>';


                accountInput.html(html);
            }

            if ($(node).val() == 'cash') {
                accountInput.html('');
                const cash =
                    `<input type="text" name="account_id" class="form-control" value="${$(node).val()}" readonly>`;

                accountInput.html(cash);
            }
        }
    </script>
@endpush
