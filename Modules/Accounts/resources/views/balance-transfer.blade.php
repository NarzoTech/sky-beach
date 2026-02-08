@extends('admin.layouts.master')
@section('title', __('Balance Transfer List'))


@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body pb-0">
                    <form class="search_form" action="" method="GET">
                        <div class="row">
                            <div class="col-lg-3 col-md-6">
                                <div class="form-group search-wrapper">
                                    <input type="text" name="keyword" value="{{ request()->get('keyword') }}"
                                        class="form-control" placeholder="Search..." autocomplete="off">
                                    <button type="submit">
                                        <i class='bx bx-search'></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <div class="form-group">
                                    <select name="order_by" id="order_by" class="form-control">
                                        <option value="">{{ __('Order By') }}</option>
                                        <option value="asc" {{ request('order_by') == 'asc' ? 'selected' : '' }}>
                                            {{ __('ASC') }}
                                        </option>
                                        <option value="desc" {{ request('order_by') == 'desc' ? 'selected' : '' }}>
                                            {{ __('DESC') }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <div class="form-group">
                                    <select name="par-page" id="par-page" class="form-control">
                                        <option value="">{{ __('Per Page') }}</option>
                                        <option value="10" {{ '10' == request('par-page') ? 'selected' : '' }}>
                                            {{ __('10') }}
                                        </option>
                                        <option value="50" {{ '50' == request('par-page') ? 'selected' : '' }}>
                                            {{ __('50') }}
                                        </option>
                                        <option value="100" {{ '100' == request('par-page') ? 'selected' : '' }}>
                                            {{ __('100') }}
                                        </option>
                                        <option value="all" {{ 'all' == request('par-page') ? 'selected' : '' }}>
                                            {{ __('All') }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <div class="form-group">
                                    <button type="button" class="btn bg-danger form-reset">{{ __('Reset') }}</button>
                                    <button type="submit" class="btn bg-primary">{{ __('Search') }}</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-5">
        <div class="card-header">
            <div class="card-header-title">
                <h4 class="section_title"> {{ __('Balance Transfer List') }}</h4>
            </div>
            <div class="btn-actions-pane-right actions-icon-btn">
                @adminCan('balance.transfer.create')
                    <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#transferModal" class="btn btn-primary"><i
                            class="fa fa-plus"></i>
                        {{ __('Add New') }}</a>
                @endadminCan
                @adminCan('balance.transfer.excel.download')
                    <button type="button" class="btn bg-label-success export"><i class="fa fa-file-excel"></i>
                        {{ __('Excel') }}</button>
                @endadminCan
                @adminCan('balance.transfer.pdf.download')
                    <button type="button" class="btn bg-label-warning export-pdf"><i class="fa fa-file-pdf"></i>
                        {{ __('PDF') }}</button>
                @endadminCan
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive list_table">
                <table style="width: 100%;" class="table">
                    <thead>
                        <th> # </th>
                        <th>{{ __('From Account') }}</th>
                        <th>{{ __('To Account') }}</th>
                        <th>{{ __('Amount') }}</th>
                        <th>{{ __('Added By') }}</th>
                        <th>{{ __('Date') }}</th>
                        <th>{{ __('Remark') }}</th>
                        <th>{{ __('Action') }}</th>
                    </thead>
                    <tbody>
                        @foreach ($transfers as $key => $balanceTransfer)
                            @php
                                $fromAcc = $balanceTransfer->fromAccount;
                                $toAcc = $balanceTransfer->toAccount;

                                $fromLabel = accountList()[$fromAcc->account_type] ?? 'Unknown';
                                if ($fromAcc->account_type === 'bank') {
                                    $fromLabel = ($fromAcc->bank->name ?? 'Bank') . ' - ' . ($fromAcc->bank_account_number ?? '');
                                } elseif ($fromAcc->account_type === 'mobile_banking') {
                                    $fromLabel = ($fromAcc->mobile_bank_name ?? 'Mobile') . ' - ' . ($fromAcc->mobile_number ?? '');
                                } elseif ($fromAcc->account_type === 'card') {
                                    $fromLabel = ($fromAcc->card_holder_name ?? 'Card') . ' - ' . ($fromAcc->card_number ?? '');
                                }

                                $toLabel = accountList()[$toAcc->account_type] ?? 'Unknown';
                                if ($toAcc->account_type === 'bank') {
                                    $toLabel = ($toAcc->bank->name ?? 'Bank') . ' - ' . ($toAcc->bank_account_number ?? '');
                                } elseif ($toAcc->account_type === 'mobile_banking') {
                                    $toLabel = ($toAcc->mobile_bank_name ?? 'Mobile') . ' - ' . ($toAcc->mobile_number ?? '');
                                } elseif ($toAcc->account_type === 'card') {
                                    $toLabel = ($toAcc->card_holder_name ?? 'Card') . ' - ' . ($toAcc->card_number ?? '');
                                }
                            @endphp
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $fromLabel }}</td>
                                <td>{{ $toLabel }}</td>
                                <td>{{ currency($balanceTransfer->amount) }}</td>
                                <td>{{ $balanceTransfer->createdBy->name ?? '-' }}</td>
                                <td>{{ formatDate($balanceTransfer->date) }}</td>
                                <td>{{ $balanceTransfer->note }}</td>
                                <td>
                                    @if (checkAdminHasPermission('balance.transfer.edit') || checkAdminHasPermission('balance.transfer.delete'))
                                        <div class="btn-group" role="group">
                                            <button id="btnGroupDrop{{ $balanceTransfer->id }}" type="button"
                                                class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown"
                                                aria-haspopup="true" aria-expanded="false">
                                                {{ __('Action') }}
                                            </button>
                                            <div class="dropdown-menu"
                                                aria-labelledby="btnGroupDrop{{ $balanceTransfer->id }}">
                                                @adminCan('balance.transfer.edit')
                                                    <a class="dropdown-item" href="javascript:;" data-bs-toggle="modal"
                                                        data-bs-target="#editTransferModal-{{ $balanceTransfer->id }}">{{ __('Edit') }}</a>
                                                @endadminCan
                                                @adminCan('balance.transfer.delete')
                                                    <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#deleteModal"
                                                        class="dropdown-item" onclick="deleteData({{ $balanceTransfer->id }})">
                                                        {{ __('Delete') }}</a>
                                                @endadminCan
                                            </div>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if (request()->get('par-page') !== 'all')
                <div class="float-right">
                    {{ $transfers->onEachSide(0)->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- create balance transfer modal --}}
    <div class="modal fade" id="transferModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">{{ __('Create Balance Transfer') }}</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <!-- Modal body -->
                <div class="modal-body py-0">
                    <form action="{{ route('admin.balance.transfer.store') }}" method="POST" id="add-transfer-form">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="date">{{ __('Date') }}</label>
                                    <input type="text" class="form-control datepicker" id="date" name="date"
                                        autocomplete="off" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="amount">{{ __('Amount') }}</label>
                                    <input type="number" step="0.01" min="0.01" class="form-control" id="amount" name="amount" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="from_account_type">{{ __('From Account Type') }}</label>
                                    <select name="from_account_type" id="from_account_type" class="form-control me-2">
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
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="to_account_type">{{ __('To Account Type') }}</label>
                                    <select name="to_account_type" id="to_account_type" class="form-control me-2">
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
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="from_account">{{ __('From Account') }}</label>
                                    <select name="from_account" id="from_account" class="form-control">
                                        <option value="cash">{{ __('Cash') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="to_account">{{ __('To Account') }}</label>
                                    <select name="to_account" id="to_account" class="form-control">
                                        <option value="cash">{{ __('Cash') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="remark">{{ __('Remark') }}</label>
                                    <textarea name="note" id="remark" class="form-control height-80px" rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-primary" form="add-transfer-form">{{ __('Save') }}</button>
                </div>

            </div>
        </div>
    </div>


    {{-- edit balance transfer modal --}}
    @foreach ($transfers as $transfer)
        <div class="modal fade" id="editTransferModal-{{ $transfer->id }}">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title">{{ __('Edit Balance Transfer') }}</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body py-0">
                        <form action="{{ route('admin.balance.transfer.update', $transfer->id) }}" method="POST"
                            id="edit-transfer-form{{ $transfer->id }}">
                            @csrf
                            @method('PATCH')
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="date_{{ $transfer->id }}">{{ __('Date') }}</label>
                                        <input type="text" class="form-control datepicker" id="date_{{ $transfer->id }}"
                                            name="date" value="{{ formatDate($transfer->date) }}" autocomplete="off" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="amount_{{ $transfer->id }}">{{ __('Amount') }}</label>
                                        <input type="number" step="0.01" min="0.01" class="form-control" id="amount_{{ $transfer->id }}" name="amount"
                                            value="{{ $transfer->amount }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="from_account_type">{{ __('From Account Type') }}</label>
                                        <select name="from_account_type" data-id="{{ $transfer->id }}"
                                            class="form-control me-2 from_account_type">
                                            @foreach (accountList() as $key => $list)
                                                <option value="{{ $key }}"
                                                    @if ($key == $transfer->fromAccount->account_type) selected @endif
                                                    data-name="{{ $list }}">
                                                    {{ $list }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="to_account_type">{{ __('To Account Type') }}</label>
                                        <select name="to_account_type" data-id="{{ $transfer->id }}"
                                            class="form-control me-2 to_account_type">
                                            @foreach (accountList() as $key => $list)
                                                <option value="{{ $key }}"
                                                    @if ($key == $transfer->toAccount->account_type) selected @endif
                                                    data-name="{{ $list }}">
                                                    {{ $list }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="from_account_{{ $transfer->id }}">{{ __('From Account') }}</label>
                                        <select name="from_account" id="from_account_{{ $transfer->id }}" class="form-control from_account_select">
                                            {!! selectedAccount($transfer->fromAccount->account_type, $transfer->from_account_id) !!}
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="to_account_{{ $transfer->id }}">{{ __('To Account') }}</label>
                                        <select name="to_account" id="to_account_{{ $transfer->id }}" class="form-control to_account_select">
                                            {!! selectedAccount($transfer->toAccount->account_type, $transfer->to_account_id) !!}
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="remark_{{ $transfer->id }}">{{ __('Remark') }}</label>
                                        <textarea name="note" id="remark_{{ $transfer->id }}" class="form-control height-80px" rows="3">{{ $transfer->note }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">{{ __('Close') }}</button>
                        <button type="submit" class="btn btn-primary"
                            form="edit-transfer-form{{ $transfer->id }}">{{ __('Save') }}</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endsection


@push('js')
    <script>
        'use strict';

        $(document).ready(function() {
            const accountsList = @json($accounts);
            $(document).on('change',
                'select[name="from_account_type"], select[name="to_account_type"]',
                function() {
                    let placeName = $(this).attr('name');
                    if (placeName) {
                        placeName = placeName.replaceAll('_type', '');
                    }
                    const accounts = accountsList.filter(account => account.account_type == $(this).val());
                    const accountInput = $(`#${placeName}`);
                    if (accounts) {
                        let html = ``;
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
                        accountInput.html(html);
                    }

                    if ($(this).val() == 'cash') {
                        accountInput.html('');
                        const cash =
                            `<option value="cash">{{ __('Cash') }}</option>`;
                        accountInput.html(cash);
                    }
                    accountInput.niceSelect('destroy');
                    accountInput.niceSelect();
                });

            $(document).on('change', '.from_account_type, .to_account_type', function() {
                let placeName = $(this).attr('name');
                if (placeName) {
                    placeName = placeName.replaceAll('_type', '');
                }

                const accounts = accountsList.filter(account => account.account_type == $(this).val());
                // Use closest to find the modal-body, then find the select by class
                const selectClass = placeName === 'from_account' ? '.from_account_select' : '.to_account_select';
                const accountInput = $(this).closest('.modal-body').find(selectClass);
                if (accounts) {
                    let html = ``;
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
                    accountInput.html(html);
                }

                if ($(this).val() == 'cash') {
                    accountInput.html('');
                    const cash =
                        `<option value="cash">{{ __('Cash') }}</option>`;
                    accountInput.html(cash);
                }
                accountInput.niceSelect('destroy');
                accountInput.niceSelect();
            })
        })

        function deleteData(id) {
            let url = "{{ route('admin.balance.transfer.destroy', ':id') }}"
            url = url.replace(':id', id);
            $("#deleteForm").attr("action", url);
            $('#deleteModal').modal('show');
        }
    </script>
@endpush
