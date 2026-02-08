@extends('admin.layouts.master')
@section('title', __('Opening Balance'))


@section('content')
    <div class="main-content">
        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4 mb-5">
                        <div class="card dashboard_card">
                            <div class="card-body">
                                <div class="card-title d-flex align-items-start justify-content-between mb-0">
                                    <div class="avatar flex-shrink-0">
                                        <i class='bx bx-dollar'></i>
                                    </div>
                                </div>
                                <h5 class="mb-1">{{ __('Current Balance') }}</h5>
                                <h4 class="card-title text-primary fw-medium"> {{ currency($accountBalance) }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4 mb-5">
                        <div class="card dashboard_card">
                            <div class="card-body">
                                <div class="card-title d-flex align-items-start justify-content-between mb-0">
                                    <div class="avatar flex-shrink-0">
                                        <i class='bx bx-dollar'></i>
                                    </div>
                                </div>
                                <h5 class="mb-1">{{ __('Total Deposit') }}</h5>
                                <h4 class="card-title text-primary fw-medium">{{ currency($totalDeposits) }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4 mb-5">
                        <div class="card dashboard_card">
                            <div class="card-body">
                                <div class="card-title d-flex align-items-start justify-content-between mb-0">
                                    <div class="avatar flex-shrink-0">
                                        <i class='bx bx-dollar'></i>
                                    </div>
                                </div>
                                <h5 class="mb-1">{{ __('Total Withdraw') }}</h5>
                                <h4 class="card-title text-primary fw-medium">{{ currency($totalWithdraws) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xl-5 mb-5">
                        <div class="card-box">
                            <div class="card card-statistic-1">
                                <div class="card-header">
                                    <h4 class="section_title">{{ __('Balance') }}</h4>
                                </div>
                                <div class="card-body">
                                    <form method="POST" action="{{ route('admin.opening-balance.update', $balance->id) }}"
                                        class="">
                                        @csrf
                                        @method('PUT')
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>{{ __('Balance Type') }}</label>
                                                    <select name="balance_type" class="form-control" required>
                                                        <option value="deposit"
                                                            {{ $balance->balance_type == 'deposit' ? 'selected' : '' }}>
                                                            {{ __('Deposit') }}
                                                        </option>
                                                        <option value="withdraw"
                                                            {{ $balance->balance_type == 'withdraw' ? 'selected' : '' }}>
                                                            {{ __('Withdraw') }}
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>{{ __('Date') }}</label>
                                                    <input type="date" class="form-control" name="date"
                                                        value="{{ formatDate($balance->date, 'Y-m-d') }}" required>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label>{{ __('Account Type') }}</label>
                                                    <select name="payment_type" class="form-control" required>
                                                        <option value="">{{ __('Payment Type') }}</option>
                                                        @foreach (accountList() as $key => $list)
                                                            <option value="{{ $key }}"
                                                                {{ $balance->payment_type == $key ? 'selected' : '' }}>
                                                                {{ $list }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="accounts">
                                                    <input type="hidden" name="account_id"
                                                        value="{{ $balance->account_id }}">
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label>{{ __('Amount') }}</label>
                                                    <input type="number" step="0.01" min="0.01" class="form-control" name="amount" required
                                                        placeholder="{{ __('Amount') }}" autocomplete="off"
                                                        value="{{ $balance->amount }}">
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label>{{ __('Remark') }}</label>
                                                    <textarea name="note" rows="2" class="form-control" placeholder="{{ __('Note') }}">{{ $balance->note }}</textarea>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="text-right">
                                                    <button class="btn btn-primary" type="submit">{{ __('Save') }}</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-7">
                        <div class="card">
                            <div class="card-header">
                                <ul class="nav nav-tabs gap-2 pb-1">
                                    <li class="nav-item">
                                        <a href="#home1" class="btn btn-success nav-link active" data-bs-toggle="tab" aria-expanded="false">
                                            {{ __('Deposit') }}
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#profile1" class="btn btn-primary ml-2 nav-link" data-bs-toggle="tab"
                                            aria-expanded="true">
                                            {{ __('Withdraw') }}
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <div class="card-body pt-0">
                                <div class="tab-content p-0">
                                    <div role="tabpanel" class="tab-pane fade active show" id="home1">
                                        <div class="card-box">
                                            <h4 class="section_title mb-3">{{ __('Deposit History') }}</h4>
                                            <div class="table-responsive table-invoice table_x_scroll">
                                                <table class="table common_table">
                                                    <thead>
                                                        <tr>
                                                            <th>{{ __('SN') }}</th>
                                                            <th>{{ __('Date') }}</th>
                                                            <th>{{ __('Note') }}</th>
                                                            <th>{{ __('Amount') }}</th>
                                                            <th>{{ __('Action') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($deposits as $deposit)
                                                            <tr>
                                                                <td>{{ $loop->iteration }}</td>
                                                                <td>{{ formatDate($deposit->date) }}</td>
                                                                <td>{{ $deposit->note }}</td>
                                                                <td>{{ currency($deposit->amount) }}</td>
                                                                <td>
                                                                    <div class="d-flex gap-2">
                                                                        <a href="{{ route('admin.opening-balance.edit', $deposit->id) }}"
                                                                            class="btn btn-primary btn-sm">
                                                                            <i class="fas fa-edit"></i>
                                                                        </a>
                                                                        <a href="javascript:void(0)"
                                                                            onclick="deleteData({{ $deposit->id }})"
                                                                            class="btn btn-danger btn-sm"
                                                                            data-bs-toggle="modal"
                                                                            data-bs-target="#deleteModal">
                                                                            <i class="fas fa-trash"></i>
                                                                        </a>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div role="tabpanel" class="tab-pane fade" id="profile1">
                                        <div class="card-box">
                                            <h4 class="section_title mb-3">{{ __('Withdraw History') }}</h4>
                                            <div class="table-responsive table-invoice table_x_scroll">
                                                <table class="table common_table">
                                                    <thead>
                                                        <tr>
                                                            <th>{{ __('SN') }}</th>
                                                            <th>{{ __('Date') }}</th>
                                                            <th>{{ __('Note') }}</th>
                                                            <th>{{ __('Amount') }}</th>
                                                            <th>{{ __('Action') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($withdraws as $withdraw)
                                                            <tr>
                                                                <td>{{ $loop->iteration }}</td>
                                                                <td>{{ formatDate($withdraw->date) }}</td>
                                                                <td>{{ $withdraw->note }}</td>
                                                                <td>{{ currency($withdraw->amount) }}</td>
                                                                <td>
                                                                    <div class="d-flex gap-2">
                                                                        <a href="{{ route('admin.opening-balance.edit', $withdraw->id) }}"
                                                                            class="btn btn-primary btn-sm">
                                                                            <i class="fas fa-edit"></i>
                                                                        </a>
                                                                        <a href="javascript:void(0)"
                                                                            onclick="deleteData({{ $withdraw->id }})"
                                                                            class="btn btn-danger btn-sm">
                                                                            <i class="fas fa-trash"></i>
                                                                        </a>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
        'use strict';
        $(document).ready(function() {

            let accounts = @json($accounts);
            $('select[name="payment_type"]').on('change', function() {
                const paymentType = $(this).val();
                console.log(paymentType);
                let html = `<label for="account_id">{{ __('Select Account') }}<span class="text-danger">*</span></label>
                    <select name="account_id" id="" class="form-control form-group" required>`;
                const filterAccount = accounts.filter(account => account.account_type === paymentType);
                html = accountsType(filterAccount, html, paymentType);
                $('.accounts').html(html);

                if ($(this).val() == 'cash' || $(this).val() == 'advance') {
                    const cash =
                        `<input type="hidden" name="account_id" class="form-control" value="${$(this).val()}" readonly>`;
                    $('.accounts').html(cash);
                }
            });
        });

        function deleteData(id) {
            let url = "{{ route('admin.opening-balance.destroy', ':id') }}"
            url = url.replace(':id', id);
            $("#deleteForm").attr("action", url);
            $('#deleteModal').modal('show');
        }
    </script>
@endpush
