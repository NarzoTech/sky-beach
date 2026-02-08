@extends('admin.layouts.master')
@section('title', __('Account List'))

@push('css')
    <style>
        .account-summary-card {
            border: none;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
        }

        .account-summary-card .card-body {
            padding: 1.25rem;
        }

        .account-summary-card .icon-wrapper {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .account-summary-card .amount {
            font-size: 1.4rem;
            font-weight: 700;
            color: #566a7f;
        }

        .account-summary-card .label {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
            color: #8592a3;
        }

        .account-section-card {
            border: none;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
        }

        .account-section-card .card-header {
            border-bottom: 1px solid #e9ecef;
            padding: 1rem 1.25rem;
            background: #fff;
        }

        .account-section-card .section-icon {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
        }

        .account-section-card .table thead th {
            border-top: none;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            color: #8592a3;
            padding: 0.875rem 0.75rem;
        }

        .account-section-card .table tbody td {
            vertical-align: middle;
            padding: 0.75rem;
        }

        .amount-badge {
            display: inline-block;
            padding: 0.3rem 0.65rem;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .amount-positive {
            background-color: rgba(113, 221, 55, 0.15);
            color: #71dd37;
        }

        .amount-negative {
            background-color: rgba(255, 62, 29, 0.15);
            color: #ff3e1d;
        }

        .filter-card {
            border: none;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
        }
    </style>
@endpush

@section('content')
    {{-- Filter Section --}}
    <div class="row">
        <div class="col-12">
            <div class="card filter-card">
                <div class="card-body pb-0">
                    <form class="search_form" action="" method="GET">
                        <div class="row align-items-end">
                            <div class="col-xxl-3 col-md-4">
                                <div class="form-group">
                                    <label class="form-label text-muted small">{{ __('Search') }}</label>
                                    <input type="text" name="keyword" value="{{ request()->get('keyword') }}"
                                        class="form-control" placeholder="{{ __('Search') }}..." autocomplete="off">
                                </div>
                            </div>
                            <div class="col-xxl-4 col-md-4">
                                <div class="form-group">
                                    <label class="form-label text-muted small">{{ __('Date Range') }}</label>
                                    <div class="input-group input-daterange" id="bs-datepicker-daterange">
                                        <input type="text" id="dateRangePicker" placeholder="{{ __('From Date') }}"
                                            class="form-control datepicker" name="from_date"
                                            value="{{ request()->get('from_date') }}" autocomplete="off">
                                        <span class="input-group-text bg-light">{{ __('to') }}</span>
                                        <input type="text" placeholder="{{ __('To Date') }}" class="form-control datepicker"
                                            name="to_date" value="{{ request()->get('to_date') }}" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                            <div class="col-xxl-2 col-md-2">
                                <div class="form-group">
                                    <label class="form-label text-muted small">{{ __('Per Page') }}</label>
                                    <select name="par-page" id="par-page" class="form-control">
                                        <option value="">{{ __('All') }}</option>
                                        <option value="10" {{ '10' == request('par-page') ? 'selected' : '' }}>10
                                        </option>
                                        <option value="50" {{ '50' == request('par-page') ? 'selected' : '' }}>50
                                        </option>
                                        <option value="100" {{ '100' == request('par-page') ? 'selected' : '' }}>100
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xxl-3 col-md-4">
                                <div class="form-group">
                                    <button type="button" class="btn btn-outline-danger form-reset">
                                        <i class="fas fa-redo me-1"></i>{{ __('Reset') }}
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search me-1"></i>{{ __('Search') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="row mt-4">
        {{-- Cash Amount Card --}}
        <div class="col-12 col-md-6 col-xl-3 mb-4">
            <div class="card account-summary-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="icon-wrapper me-3" style="background: rgba(113, 221, 55, 0.15);">
                            <i class="fas fa-money-bill-wave" style="color: #71dd37;"></i>
                        </div>
                        <div>
                            <p class="label mb-0">{{ __('Cash in Hand') }}</p>
                            <h3 class="amount mb-0">{{ currency($cashAccount?->getBalanceBetween()) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bank Accounts Total --}}
        <div class="col-12 col-md-6 col-xl-3 mb-4">
            <div class="card account-summary-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="icon-wrapper me-3" style="background: rgba(105, 108, 255, 0.15);">
                            <i class="fas fa-university" style="color: #696cff;"></i>
                        </div>
                        <div>
                            @php
                                $bankTotal = $bankAccounts->sum(fn($acc) => $acc->getBalanceBetween());
                            @endphp
                            <p class="label mb-0">{{ __('Bank Accounts') }}</p>
                            <h3 class="amount mb-0">{{ currency($bankTotal) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Mobile Banking Total --}}
        <div class="col-12 col-md-6 col-xl-3 mb-4">
            <div class="card account-summary-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="icon-wrapper me-3" style="background: rgba(3, 195, 236, 0.15);">
                            <i class="fas fa-mobile-alt" style="color: #03c3ec;"></i>
                        </div>
                        <div>
                            @php
                                $mobileTotal = $mobileAccounts->sum(fn($acc) => $acc->getBalanceBetween());
                            @endphp
                            <p class="label mb-0">{{ __('Mobile Banking') }}</p>
                            <h3 class="amount mb-0">{{ currency($mobileTotal) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total Balance Card --}}
        <div class="col-12 col-md-6 col-xl-3 mb-4">
            <div class="card account-summary-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="icon-wrapper me-3" style="background: rgba(255, 171, 0, 0.15);">
                            <i class="fas fa-wallet" style="color: #ffab00;"></i>
                        </div>
                        <div>
                            <p class="label mb-0">{{ __('Total Balance') }}</p>
                            <h3 class="amount mb-0">{{ currency($accountBalance) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Bank Accounts Section --}}
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card account-section-card">
                <div class="card-header bg-white d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="section-icon" style="background: rgba(105, 108, 255, 0.15);">
                            <i class="fas fa-university" style="color: #696cff;"></i>
                        </div>
                        <h5 class="mb-0 fw-bold">{{ __('Bank Accounts') }}</h5>
                    </div>
                    <span class="badge bg-primary rounded-pill">{{ $bankAccounts->count() }} {{ __('Accounts') }}</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('SN') }}</th>
                                    <th>{{ __('Bank Name') }}</th>
                                    <th>{{ __('Account Type') }}</th>
                                    <th>{{ __('Account Name') }}</th>
                                    <th>{{ __('Account Number') }}</th>
                                    <th>{{ __('Branch') }}</th>
                                    <th class="text-end">{{ __('Balance') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($bankAccounts as $index => $account)
                                    @php
                                        $balance = $account->getBalanceBetween();
                                    @endphp
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="fw-medium">{{ $account?->bank?->name ?? '-' }}</span>
                                            </div>
                                        </td>
                                        <td><span
                                                class="badge bg-light text-dark">{{ $account->bank_account_type ?? '-' }}</span>
                                        </td>
                                        <td>{{ $account->bank_account_name ?? '-' }}</td>
                                        <td><code>{{ $account->bank_account_number ?? '-' }}</code></td>
                                        <td>{{ $account->bank_account_branch ?? '-' }}</td>
                                        <td class="text-end">
                                            <span
                                                class="amount-badge {{ $balance >= 0 ? 'amount-positive' : 'amount-negative' }}">
                                                {{ currency($balance) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if (checkAdminHasPermission('account.edit') || checkAdminHasPermission('account.delete'))
                                                <div class="btn-group" role="group">
                                                    <button id="btnGroupDrop{{ $account->id }}" type="button"
                                                        class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown"
                                                        aria-haspopup="true"
                                                        aria-expanded="false">{{ __('Action') }}</button>
                                                    <div class="dropdown-menu"
                                                        aria-labelledby="btnGroupDrop{{ $account->id }}">
                                                        @adminCan('account.edit')
                                                            <a class="dropdown-item"
                                                                href="{{ route('admin.accounts.edit', $account->id) }}">{{ __('Edit') }}</a>
                                                        @endadminCan
                                                        @adminCan('account.delete')
                                                            <a href="javascript:;" class="dropdown-item"
                                                                onclick="deleteData({{ $account->id }})">{{ __('Delete') }}</a>
                                                        @endadminCan
                                                    </div>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-university fa-3x mb-2 d-block"></i>
                                                {{ __('No bank accounts found') }}
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Mobile Accounts & Card Accounts Row --}}
    <div class="row">
        {{-- Mobile Accounts Section --}}
        <div class="col-12 col-xl-6 mb-4">
            <div class="card account-section-card h-100">
                <div class="card-header bg-white d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="section-icon" style="background: rgba(3, 195, 236, 0.15);">
                            <i class="fas fa-mobile-alt" style="color: #03c3ec;"></i>
                        </div>
                        <h5 class="mb-0 fw-bold">{{ __('Mobile Banking') }}</h5>
                    </div>
                    <span class="badge bg-info rounded-pill">{{ $mobileAccounts->count() }} {{ __('Accounts') }}</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('SN') }}</th>
                                    <th>{{ __('Provider') }}</th>
                                    <th>{{ __('Mobile Number') }}</th>
                                    <th class="text-end">{{ __('Balance') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($mobileAccounts as $index => $account)
                                    @php
                                        $balance = $account->getBalanceBetween();
                                    @endphp
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="fw-medium">{{ $account->mobile_bank_name }}</span>
                                            </div>
                                        </td>
                                        <td><code>{{ $account->mobile_number }}</code></td>
                                        <td class="text-end">
                                            <span
                                                class="amount-badge {{ $balance >= 0 ? 'amount-positive' : 'amount-negative' }}">
                                                {{ currency($balance) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if (checkAdminHasPermission('account.edit') || checkAdminHasPermission('account.delete'))
                                                <div class="btn-group" role="group">
                                                    <button id="btnGroupDropMobile{{ $account->id }}" type="button"
                                                        class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown"
                                                        aria-haspopup="true"
                                                        aria-expanded="false">{{ __('Action') }}</button>
                                                    <div class="dropdown-menu"
                                                        aria-labelledby="btnGroupDropMobile{{ $account->id }}">
                                                        @adminCan('account.edit')
                                                            <a class="dropdown-item"
                                                                href="{{ route('admin.accounts.edit', $account->id) }}">{{ __('Edit') }}</a>
                                                        @endadminCan
                                                        @adminCan('account.delete')
                                                            <a href="javascript:;" class="dropdown-item"
                                                                onclick="deleteData({{ $account->id }})">{{ __('Delete') }}</a>
                                                        @endadminCan
                                                    </div>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-mobile-alt fa-3x mb-2 d-block"></i>
                                                {{ __('No mobile accounts found') }}
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card Accounts Section --}}
        <div class="col-12 col-xl-6 mb-4">
            <div class="card account-section-card h-100">
                <div class="card-header bg-white d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="section-icon" style="background: rgba(255, 62, 29, 0.15);">
                            <i class="fas fa-credit-card" style="color: #ff3e1d;"></i>
                        </div>
                        <h5 class="mb-0 fw-bold">{{ __('Bank Cards') }}</h5>
                    </div>
                    <span class="badge bg-danger rounded-pill">{{ $cardAccounts->count() }} {{ __('Cards') }}</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('SN') }}</th>
                                    <th>{{ __('Card Info') }}</th>
                                    <th>{{ __('Card Number') }}</th>
                                    <th class="text-end">{{ __('Balance') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($cardAccounts as $index => $account)
                                    @php
                                        $balance = $account->getBalanceBetween();
                                    @endphp
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div>
                                                    <span
                                                        class="fw-medium d-block">{{ $account->card_holder_name }}</span>
                                                    <small class="text-muted">{{ $account->card_type }} -
                                                        {{ $account->bank?->name }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><code>{{ $account->card_number }}</code></td>
                                        <td class="text-end">
                                            <span
                                                class="amount-badge {{ $balance >= 0 ? 'amount-positive' : 'amount-negative' }}">
                                                {{ currency($balance) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if (checkAdminHasPermission('account.edit') || checkAdminHasPermission('account.delete'))
                                                <div class="btn-group" role="group">
                                                    <button id="btnGroupDropCard{{ $account->id }}" type="button"
                                                        class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown"
                                                        aria-haspopup="true"
                                                        aria-expanded="false">{{ __('Action') }}</button>
                                                    <div class="dropdown-menu"
                                                        aria-labelledby="btnGroupDropCard{{ $account->id }}">
                                                        @adminCan('account.edit')
                                                            <a class="dropdown-item"
                                                                href="{{ route('admin.accounts.edit', $account->id) }}">{{ __('Edit') }}</a>
                                                        @endadminCan
                                                        @adminCan('account.delete')
                                                            <a href="javascript:;" class="dropdown-item"
                                                                onclick="deleteData({{ $account->id }})">{{ __('Delete') }}</a>
                                                        @endadminCan
                                                    </div>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-credit-card fa-3x mb-2 d-block"></i>
                                                {{ __('No bank cards found') }}
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('js')
        <script>
            function deleteData(id) {
                let url = "{{ route('admin.accounts.destroy', ':id') }}"
                url = url.replace(':id', id);
                $("#deleteForm").attr("action", url);
                $('#deleteModal').modal('show');
            }
        </script>
    @endpush
@endsection
