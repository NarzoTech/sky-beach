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
                                <h4 class="card-title text-primary fw-medium"> {{ currency($totalDeposits) }}</h4>
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
                                <h4 class="card-title text-primary fw-medium"> {{ currency($totalWithdraws) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xl-5 mb-5">
                        <div class="card-box">
                            @adminCan('deposit.withdraw.create')
                                <div class="card card-statistic-1">
                                    <div class="card-header">
                                        <h4 class="section_title">{{ __('Balance') }}</h4>
                                    </div>

                                    <div class="card-body">
                                        <form method="POST" action="{{ route('admin.opening-balance.store') }}" class="">
                                            @csrf
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="">Balance Type</label>
                                                        <select name="balance_type" class="form-control" required>
                                                            <option value="deposit">Deposit</option>
                                                            <option value="withdraw">Withdraw</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="">Date</label>
                                                        <input type="date" class="form-control" name="date"
                                                            value="{{ formatDate(now(), 'Y-m-d') }}" required>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label for="">Account Type</label>
                                                        <select name="payment_type" id="" class="form-control">
                                                            <option value="">{{ __('Payment Type') }}</option>
                                                            @foreach (accountList() as $key => $list)
                                                                <option value="{{ $key }}">
                                                                    {{ $list }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="accounts">

                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label for="">Amount</label>
                                                        <input type="text" class="form-control" name="amount" required
                                                            placeholder="Amount" autocomplete="off">
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label for="">Remark</label>
                                                        <textarea name="note" rows="2" class="form-control" placeholder="Note"></textarea>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="text-right">
                                                        <button class="btn btn-primary" type="submit">Save</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @endadminCan
                        </div>
                    </div>

                    <div class="col-xl-7">
                        <div class="card mb-6">
                            <div class="card-header p-0 nav-align-top">
                                <ul class="nav nav-tabs" role="tablist">
                                    <li class="nav-item">
                                        <button class="nav-link active" data-bs-toggle="tab"
                                            data-bs-target="#form-tabs-personal" role="tab"
                                            aria-selected="true">{{ __('Deposit') }}</button>
                                    </li>
                                    <li class="nav-item">
                                        <button class="nav-link " data-bs-toggle="tab" data-bs-target="#form-tabs-account"
                                            role="tab" aria-selected="false">{{ __('Withdraw') }}</button>
                                    </li>
                                </ul>
                            </div>

                            <div class="tab-content">
                                <div class="tab-pane fade active show" id="form-tabs-personal" role="tabpanel">
                                    <div class="card-box">
                                        <h4 class="section_title mb-3">{{ __('Deposit History') }}</h4>
                                        <div class="table-responsive table-invoice table_x_scroll">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th>{{ __('SN') }}</th>
                                                        <th>{{ __('Date') }}</th>
                                                        <th>{{ __('Account') }}</th>
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
                                                            <td>{{ ucfirst(str_replace('_', ' ', $deposit->account->account_type ?? 'N/A')) }}</td>
                                                            <td>{{ $deposit->note }}</td>
                                                            <td>{{ currency($deposit->amount) }}</td>
                                                            <td>
                                                                @if (checkAdminHasPermission('deposit.withdraw.edit') || checkAdminHasPermission('deposit.withdraw.delete'))
                                                                    <div class="btn-group" role="group">
                                                                        <button id="btnGroupDrop{{ $deposit->id }}"
                                                                            type="button"
                                                                            class="btn btn-primary dropdown-toggle"
                                                                            data-bs-toggle="dropdown" aria-haspopup="true"
                                                                            aria-expanded="false">
                                                                            {{ __('Action') }}
                                                                        </button>
                                                                        <div class="dropdown-menu"
                                                                            aria-labelledby="btnGroupDrop{{ $deposit->id }}">
                                                                            @adminCan('deposit.withdraw.edit')
                                                                                <a class="dropdown-item"
                                                                                    href="{{ route('admin.opening-balance.edit', $deposit->id) }}">{{ __('Edit') }}</a>
                                                                            @endadminCan
                                                                            @adminCan('deposit.withdraw.delete')
                                                                                <a href="javascript:;" data-bs-toggle="modal"
                                                                                    data-bs-target="#deleteModal"
                                                                                    class="dropdown-item"
                                                                                    onclick="deleteData({{ $deposit->id }})">
                                                                                    {{ __('Delete') }}</a>
                                                                            @endadminCan
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                    @if ($deposits->count() > 0)
                                                        <tr class="table-success">
                                                            <td colspan="4" class="text-end">
                                                                <strong>{{ __('Total:') }}</strong>
                                                            </td>
                                                            <td><strong>{{ currency($totalDeposits) }}</strong></td>
                                                            <td></td>
                                                        </tr>
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                        @if (request()->get('par-page') !== 'all')
                                            <div class="float-right">
                                                {{ $deposits->onEachSide(0)->links() }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="form-tabs-account" role="tabpanel">
                                    <div class="card-box">
                                        <h4 class="section_title mb-3">{{ __('Withdraw History') }}</h4>
                                        <div class="table-responsive table-invoice table_x_scroll">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th>{{ __('SN') }}</th>
                                                        <th>{{ __('Date') }}</th>
                                                        <th>{{ __('Account') }}</th>
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
                                                            <td>{{ ucfirst(str_replace('_', ' ', $withdraw->account->account_type ?? 'N/A')) }}</td>
                                                            <td>{{ $withdraw->note }}</td>
                                                            <td>{{ currency($withdraw->amount) }}</td>
                                                            <td>
                                                                @if (checkAdminHasPermission('deposit.withdraw.edit') || checkAdminHasPermission('deposit.withdraw.delete'))
                                                                    <div class="btn-group" role="group">
                                                                        <button id="btnGroupDrop{{ $withdraw->id }}"
                                                                            type="button"
                                                                            class="btn btn-primary dropdown-toggle"
                                                                            data-bs-toggle="dropdown" aria-haspopup="true"
                                                                            aria-expanded="false">
                                                                            {{ __('Action') }}
                                                                        </button>
                                                                        <div class="dropdown-menu"
                                                                            aria-labelledby="btnGroupDrop{{ $withdraw->id }}">
                                                                            @adminCan('deposit.withdraw.edit')
                                                                                <a class="dropdown-item"
                                                                                    href="{{ route('admin.opening-balance.edit', $withdraw->id) }}">{{ __('Edit') }}</a>
                                                                            @endadminCan
                                                                            @adminCan('deposit.withdraw.delete')
                                                                                <a href="javascript:;" data-bs-toggle="modal"
                                                                                    data-bs-target="#deleteModal"
                                                                                    class="dropdown-item"
                                                                                    onclick="deleteData({{ $withdraw->id }})">
                                                                                    {{ __('Delete') }}</a>
                                                                            @endadminCan
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                    @if ($withdraws->count() > 0)
                                                        <tr class="table-danger">
                                                            <td colspan="4" class="text-end">
                                                                <strong>{{ __('Total:') }}</strong>
                                                            </td>
                                                            <td><strong>{{ currency($totalWithdraws) }}</strong></td>
                                                            <td></td>
                                                        </tr>
                                                    @endif

                                                </tbody>
                                            </table>
                                        </div>
                                        @if (request()->get('par-page') !== 'all')
                                            <div class="float-right">
                                                {{ $withdraws->onEachSide(0)->links() }}
                                            </div>
                                        @endif
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
