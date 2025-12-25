@extends('admin.layouts.master')

@section('title')
    <title>{{ __('Expense Suppliers List') }}</title>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body pb-0">
                    <form class="search_form" action="" method="GET">
                        <div class="row">
                            <div class="col-xxl-2 col-md-6 col-lg-4">
                                <div class="form-group search-wrapper">
                                    <input type="text" name="keyword" value="{{ request()->get('keyword') }}"
                                        class="form-control" placeholder="Search..." autocomplete="off">
                                    <button type="submit">
                                        <i class='bx bx-search'></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-xxl-2 col-md-6 col-lg-4">
                                <div class="form-group">
                                    <select name="order_type" id="order_type" class="form-control">
                                        <option value="">{{ __('Order Type') }}</option>
                                        <option value="due" {{ request('order_type') == 'due' ? 'selected' : '' }}>
                                            {{ __('Due') }}</option>
                                        <option value="total" {{ request('order_type') == 'total' ? 'selected' : '' }}>
                                            {{ __('Total') }}</option>
                                        <option value="paid" {{ request('order_type') == 'paid' ? 'selected' : '' }}>
                                            {{ __('Paid') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xxl-2 col-md-6 col-lg-4">
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
                            <div class="col-xxl-2 col-md-6 col-lg-4">
                                <div class="form-group">
                                    <select name="par-page" id="par-page" class="form-control">
                                        <option value="">{{ __('Per Page') }}</option>
                                        <option value="10" {{ '10' == request('par-page') ? 'selected' : '' }}>10</option>
                                        <option value="50" {{ '50' == request('par-page') ? 'selected' : '' }}>50</option>
                                        <option value="100" {{ '100' == request('par-page') ? 'selected' : '' }}>100</option>
                                        <option value="all" {{ 'all' == request('par-page') ? 'selected' : '' }}>{{ __('All') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xxl-2 col-md-6 col-lg-4">
                                <div class="form-group">
                                    <button type="button" class="btn bg-danger form-reset">{{ __('Reset') }}</button>
                                    <button type="submit" class="btn bg-label-primary">{{ __('Search') }}</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-5 mb-5">
        <div class="card-header">
            <div class="card-header-title font-size-lg text-capitalize font-weight-normal">
                <h4 class="section_title">{{ __('Expense Suppliers List') }}</h4>
            </div>
            <div class="btn-actions-pane-right actions-icon-btn">
                @adminCan('expense_supplier.create')
                    <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#addSupplier" class="btn bg-label-primary">
                        <i class="fa fa-plus"></i> {{ __('Add Expense Supplier') }}
                    </a>
                @endadminCan
                @adminCan('expense_supplier.excel.download')
                    <button type="button" class="btn bg-label-success export"><i class="fa fa-file-excel"></i>
                        {{ __('Excel') }}</button>
                @endadminCan
                @adminCan('expense_supplier.pdf.download')
                    <button type="button" class="btn bg-label-warning export-pdf"><i class="fa fa-file-pdf"></i>
                        {{ __('PDF') }}</button>
                @endadminCan
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive list_table">
                <table style="width: 100%;" class="table mb-3">
                    <thead>
                        <tr>
                            <th>{{ __('SN') }}</th>
                            <th>{{ __('Supplier Name') }}</th>
                            <th>{{ __('Company') }}</th>
                            <th>{{ __('Phone') }}</th>
                            <th>{{ __('Area') }}</th>
                            <th>{{ __('Total Expense') }}</th>
                            <th>{{ __('Paid') }}</th>
                            <th>{{ __('Due') }}</th>
                            <th>{{ __('Advance') }}</th>
                            <th>{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($suppliers as $index => $supplier)
                            <tr>
                                <td>{{ $suppliers->firstItem() + $index }}</td>
                                <td>{{ $supplier->name }}</td>
                                <td>{{ $supplier->company }}</td>
                                <td>{{ $supplier->phone }}</td>
                                <td>{{ $supplier->area->name }}</td>
                                <td>{{ currency($supplier->expenses->sum('amount')) }}</td>
                                <td>{{ currency($supplier->payments->whereIn('payment_type', ['expense', 'due_pay'])->sum('amount')) }}</td>
                                <td>{{ currency($supplier->total_due) }}</td>
                                <td>{{ currency($supplier->advance) }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        @if (checkAdminHasPermission('expense_supplier.edit') ||
                                                checkAdminHasPermission('expense_supplier.delete') ||
                                                checkAdminHasPermission('expense_supplier.advance') ||
                                                checkAdminHasPermission('expense_supplier.ledger') ||
                                                checkAdminHasPermission('expense_supplier.due_pay'))
                                            <button id="btnGroupDrop{{ $supplier->id }}" type="button"
                                                class="btn bg-label-primary dropdown-toggle" data-bs-toggle="dropdown"
                                                aria-haspopup="true" aria-expanded="false">
                                                {{ __('Action') }}
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop{{ $supplier->id }}">
                                                <a class="dropdown-item" href="javascript:;" data-bs-toggle="modal"
                                                    data-bs-target="#showSupplier{{ $supplier->id }}">{{ __('Show') }}</a>
                                                @adminCan('expense_supplier.edit')
                                                    <a class="dropdown-item" href="javascript:;" data-bs-toggle="modal"
                                                        data-bs-target="#editSupplier{{ $supplier->id }}">{{ __('Edit') }}</a>
                                                @endadminCan
                                                @adminCan('expense_supplier.advance')
                                                    <a class="dropdown-item"
                                                        href="{{ route('admin.expense-suppliers.advance', $supplier->id) }}">{{ __('Advance') }}</a>
                                                @endadminCan
                                                @adminCan('expense_supplier.ledger')
                                                    <a class="dropdown-item"
                                                        href="{{ route('admin.expense-suppliers.ledger', $supplier->id) }}">{{ __('Ledger') }}</a>
                                                @endadminCan
                                                @adminCan('expense_supplier.edit')
                                                    <a class="dropdown-item" href="javascript:;"
                                                        onclick="status('{{ $supplier->id }}')"
                                                        data-status="{{ $supplier->id }}">
                                                        {{ $supplier->status == 1 ? 'Deactivate' : 'Activate' }}
                                                    </a>
                                                @endadminCan
                                                @adminCan('expense_supplier.due_pay')
                                                    @if ($supplier->total_due > 0)
                                                        <a class="dropdown-item"
                                                            href="{{ route('admin.expense-suppliers.due-pay', $supplier->id) }}">{{ __('Pay Due') }}</a>
                                                    @endif
                                                @endadminCan
                                                @adminCan('expense_supplier.delete')
                                                    <a href="javascript:;" class="dropdown-item"
                                                        onclick="deleteData({{ $supplier->id }})">
                                                        {{ __('Delete') }}</a>
                                                @endadminCan
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach

                        <tr>
                            <td colspan="5" class="text-center">
                                <b>{{ __('Total') }}</b>
                            </td>
                            <td colspan="1">
                                <b>{{ currency($data['totalExpense']) }}</b>
                            </td>
                            <td colspan="1">
                                <b>{{ currency($data['pay']) }}</b>
                            </td>
                            <td colspan="1">
                                <b>{{ currency($data['total_due']) }}</b>
                            </td>
                            <td colspan="1">
                                <b>{{ currency($data['total_advance']) }}</b>
                            </td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            @if (request()->get('par-page') !== 'all')
                <div class="float-right">
                    {{ $suppliers->onEachSide(0)->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Add Supplier Modal --}}
    <div class="modal fade" id="addSupplier" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Add Expense Supplier') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-0">
                    <form action="{{ route('admin.expense-suppliers.store') }}" method="POST" id="add-supplier-form">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">{{ __('Name') }}<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" placeholder="Name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="company">{{ __('Company') }}</label>
                                    <input type="text" class="form-control" id="company" name="company" placeholder="Company">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone">{{ __('Phone') }}</label>
                                    <input type="text" class="form-control" id="phone" name="phone" placeholder="Phone">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">{{ __('Email') }}</label>
                                    <input type="email" class="form-control" id="email" name="email" placeholder="Email">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="area_id">{{ __('Area') }}</label>
                                    <select name="area_id" id="area_id" class="form-control">
                                        <option value="">{{ __('Select Area') }}</option>
                                        @foreach ($areaList as $list)
                                            <option value="{{ $list->id }}">{{ $list->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status">{{ __('Status') }}</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="1">{{ __('Active') }}</option>
                                        <option value="0">{{ __('Inactive') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group mb-0">
                                    <label for="address">{{ __('Address') }}</label>
                                    <textarea name="address" id="address" class="form-control" rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-primary" form="add-supplier-form">{{ __('Save') }}</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Edit Supplier Modals --}}
    @foreach ($suppliers as $supplier)
        <div class="modal fade" id="editSupplier{{ $supplier->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('Edit Expense Supplier') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body pt-0">
                        <form action="{{ route('admin.expense-suppliers.update', $supplier->id) }}" method="POST"
                            id="edit-supplier-form{{ $supplier->id }}">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">{{ __('Name') }}<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="name" value="{{ $supplier->name }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="company">{{ __('Company') }}</label>
                                        <input type="text" class="form-control" name="company" value="{{ $supplier->company }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="phone">{{ __('Phone') }}</label>
                                        <input type="text" class="form-control" name="phone" value="{{ $supplier->phone }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email">{{ __('Email') }}</label>
                                        <input type="email" class="form-control" name="email" value="{{ $supplier->email }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="area_id">{{ __('Area') }}</label>
                                        <select name="area_id" class="form-control">
                                            <option value="">{{ __('Select Area') }}</option>
                                            @foreach ($areaList as $list)
                                                <option value="{{ $list->id }}" @selected($supplier->area_id == $list->id)>
                                                    {{ $list->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="status">{{ __('Status') }}</label>
                                        <select name="status" class="form-control">
                                            <option value="1" @selected($supplier->status == 1)>{{ __('Active') }}</option>
                                            <option value="0" @selected($supplier->status == 0)>{{ __('Inactive') }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group mb-0">
                                        <label for="address">{{ __('Address') }}</label>
                                        <textarea name="address" class="form-control" rows="3">{{ $supplier->address }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">{{ __('Close') }}</button>
                        <button type="submit" class="btn btn-primary" form="edit-supplier-form{{ $supplier->id }}">{{ __('Update') }}</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    {{-- Show Supplier Modals --}}
    @foreach ($suppliers as $supplier)
        <div class="modal fade" id="showSupplier{{ $supplier->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('Expense Supplier Details') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body py-0">
                        <div class="table-responsive">
                            <table class="table mb-0">
                                <tr>
                                    <th>{{ __('Name') }}</th>
                                    <td>{{ $supplier->name }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Company') }}</th>
                                    <td>{{ $supplier->company }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Phone') }}</th>
                                    <td>{{ $supplier->phone }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Email') }}</th>
                                    <td>{{ $supplier->email }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Area') }}</th>
                                    <td>{{ $supplier->area->name }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Status') }}</th>
                                    <td>{{ $supplier->status == 1 ? 'Active' : 'Inactive' }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Address') }}</th>
                                    <td>{{ $supplier->address }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endsection

@push('js')
    <script>
        $('.export').on('click', function() {
            var fullUrl = window.location.href;
            if (fullUrl.includes('?')) {
                fullUrl += '&export=true';
            } else {
                fullUrl += '?export=true';
            }
            window.location.href = fullUrl;
        })

        $('.export-pdf').on('click', function() {
            var fullUrl = window.location.href;
            if (fullUrl.includes('?')) {
                fullUrl += '&export_pdf=true';
            } else {
                fullUrl += '?export_pdf=true';
            }
            window.location.href = fullUrl;
        })

        function deleteData(id) {
            let url = "{{ route('admin.expense-suppliers.destroy', ':id') }}"
            url = url.replace(':id', id);
            $("#deleteForm").attr("action", url);
            $('#deleteModal').modal('show');
        }

        function status(id) {
            handleStatus("{{ route('admin.expense-suppliers.status', '') }}/" + id)

            let status = $('[data-status=' + id + ']').text()
            status = status.replaceAll(/\s/g, '');
            $('[data-status=' + id + ']').text(status != 'Deactivate' ? 'Deactivate' : 'Activate')
        }
    </script>
@endpush
