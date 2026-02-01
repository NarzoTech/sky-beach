@extends('admin.layouts.master')

@section('title', __('Suppliers List'))

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
                            <div class="col-xxl-2 col-md-6 col-lg-4">
                                <div class="form-group">
                                    <div class="input-group input-daterange" id="bs-datepicker-daterange">
                                        <input type="text" id="dateRangePicker" placeholder="From Date"
                                            class="form-control datepicker" name="from_date"
                                            value="{{ request()->get('from_date') }}" autocomplete="off">
                                        <span class="input-group-text">to</span>
                                        <input type="text" placeholder="To Date" class="form-control datepicker"
                                            name="to_date" value="{{ request()->get('to_date') }}" autocomplete="off">
                                    </div>
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
                <h4 class="section_title">{{ __('Suppliers List') }}</h4>
            </div>
            <div class="btn-actions-pane-right actions-icon-btn">
                @adminCan('supplier.create')
                    <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#addSupplier" class="btn bg-label-primary"> <i
                            class="fa fa-plus"></i> {{ __('Add Supplier') }}</a>
                @endadminCan
                @adminCan('supplier.excel.download')
                    <button type="button" class="btn bg-label-success export"><i class="fa fa-file-excel"></i>
                        {{ __('Excel') }}</button>
                @endadminCan
                @adminCan('supplier.pdf.download')
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
                            <th>{{ __('Supplier Company') }}</th>
                            <th>{{ __('Supplier Name') }}</th>
                            <th>{{ __('Supplier Phone') }}</th>
                            <th>{{ __('Supplier Area') }}</th>
                            <th>{{ __('Purchase Total') }}</th>
                            <th>{{ __('Purchase Payment') }}</th>
                            <th>{{ __('Total Due') }}</th>
                            <th>{{ __('Advance') }}</th>
                            <th>{{ __('Due Dismiss') }}</th>
                            <th>{{ __('Action') }}</th>
                        </tr>

                    </thead>
                    <tbody>
                        @foreach ($suppliers as $index => $supplier)
                            @php
                                $totalReturn = $supplier->purchaseReturn->sum('return_amount');
                                $totalReturnPaid = $supplier->purchaseReturn->sum('received_amount');
                            @endphp
                            <tr>
                                <td>{{ $suppliers->firstItem() + $index }}</td>
                                <td>{{ $supplier->company }}</td>
                                <td>{{ $supplier->name }}</td>
                                <td>{{ $supplier->phone }}</td>
                                <td>{{ $supplier->area->name }}</td>
                                <td>{{ currency($supplier->purchases->sum('total_amount')) }}</td>
                                <td>{{ currency($supplier->payments->sum('amount')) }}</td>
                                <td>{{ currency($supplier->total_due - $totalReturn) }}</td>
                                <td>{{ currency($supplier->advance) }}</td>
                                <td>{{ currency($supplier->total_due_dismiss) }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        @if (checkAdminHasPermission('supplier.edit') ||
                                                checkAdminHasPermission('supplier.delete') ||
                                                checkAdminHasPermission('supplier.advance') ||
                                                checkAdminHasPermission('supplier.ledger') ||
                                                checkAdminHasPermission('supplier.status') ||
                                                checkAdminHasPermission('supplier.due.pay') ||
                                                checkAdminHasPermission('supplier.purchase.list'))
                                            <button id="btnGroupDrop{{ $supplier->id }}" type="button"
                                                class="btn bg-label-primary dropdown-toggle" data-bs-toggle="dropdown"
                                                aria-haspopup="true" aria-expanded="false">
                                                {{ __('Action') }}
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop{{ $supplier->id }}">
                                                <a class="dropdown-item" href="javascript:;" data-bs-toggle="modal"
                                                    data-bs-target="#showSupplier{{ $supplier->id }}">{{ __('Show') }}</a>
                                                @adminCan('supplier.edit')
                                                    <a class="dropdown-item" href="javascript:;" data-bs-toggle="modal"
                                                        data-bs-target="#editSupplier{{ $supplier->id }}">{{ __('Edit') }}</a>
                                                @endadminCan
                                                @adminCan('supplier.advance')
                                                    <a class="dropdown-item"
                                                        href="{{ route('admin.suppliers.advance', $supplier->id) }}">{{ __('Advance') }}</a>
                                                @endadminCan
                                                @adminCan('supplier.ledger')
                                                    <a class="dropdown-item"
                                                        href="{{ route('admin.suppliers.ledger', $supplier->id) }}">{{ __('Ledger') }}</a>
                                                @endadminCan
                                                @adminCan('supplier.status')
                                                    <a class="dropdown-item" href="javascript:;"
                                                        onclick="status('{{ $supplier->id }}')"
                                                        data-status="{{ $supplier->id }}">
                                                        {{ $supplier->status == 1 ? 'Deactivated' : 'Activate' }}
                                                    </a>
                                                @endadminCan
                                                @adminCan('supplier.due.pay')
                                                    @if ($supplier->total_due - $totalReturn)
                                                        <a class="dropdown-item"
                                                            href="{{ route('admin.suppliers.due-pay', $supplier->id) }}">{{ __('Pay') }}</a>
                                                    @endif
                                                @endadminCan
                                                @adminCan('supplier.purchase.list')
                                                    <a class="dropdown-item"
                                                        href="{{ route('admin.purchase.index') }}?supplier_id={{ $supplier->id }}">{{ __('Purchase') }}</a>
                                                @endadminCan
                                                @adminCan('supplier.delete')
                                                    <a href="javascript:;" class="dropdown-item"
                                                        onclick="deleteData({{ $supplier->id }})">
                                                        {{ __('Delete') }}</a>
                                                @endadminCan
                                            </div>
                                            @endadminCan
                                    </div>
                                </td>
                            </tr>
                        @endforeach

                        <tr>
                            <td colspan="5" class="text-center">
                                <b> {{ __('Total') }}</b>
                            </td>
                            <td colspan="1">
                                <b>{{ currency($data['totalPurchase']) }}</b>
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
                            <td colspan="1">
                                <b>{{ currency($data['total_due_dismiss']) }}</b>
                            </td>
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

    {{-- add Supplier --}}
    <div class="modal fade" id="addSupplier" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">{{ __('Add Supplier') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-0">
                    <form action="{{ route('admin.suppliers.store') }}" method="POST" id="add-supplier-form">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">{{ __('Supplier Name') }}<span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        placeholder="Name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="company">{{ __('Company') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text" id="basic-addon11"><i
                                                class="fa fa-briefcase"></i></span>
                                        <input type="text" class="form-control" id="company" name="company"
                                            placeholder="Company">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone">{{ __('Phone') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text" id="basic-addon11"><i
                                                class="fas fa-phone-alt"></i></span>
                                        <input type="text" class="form-control" id="phone" name="phone"
                                            placeholder="Phone">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">{{ __('Email') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text" id="basic-addon11"><i
                                                class="fas fa-envelope"></i></span>
                                        <input type="email" class="form-control" id="email" name="email"
                                            placeholder="Email">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    <label for="group_id">{{ __('Supplier Group') }}</label>
                                    <select name="group_id" id="group_id" class="form-select">
                                        <option value="">{{ __('Select Group') }}</option>
                                        @foreach ($groups as $group)
                                            <option value="{{ $group->id }}">{{ $group->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    <label for="area_id">{{ __('Area') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text" id="basic-addon11"><i
                                                class="fas fa-map-marker-alt"></i></span>

                                        <select name="area_id" id="area_id" class="form-control">
                                            <option value="">{{ __('Select Area') }}</option>
                                            @foreach ($areaList as $list)
                                                <option value="{{ $list->id }}">{{ $list->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="date">{{ __('Date') }}</label>
                                    <input type="text" class="form-control datepicker" id="date" name="date"
                                        placeholder="MM/DD/YY" autocomplete="off">
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
                                <div class="guest_customer_check mt-0">
                                    <label class="switch switch-square">
                                        <input type="checkbox" name="guest" class="switch-input">
                                        <span class="switch-toggle-slider">
                                            <span class="switch-on"><i class="bx bx-check"></i></span>
                                            <span class="switch-off"><i class="bx bx-x"></i></span>
                                        </span>
                                        <span class="switch-label">{{ __('Guest Supplier') }}</span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group mt-4 mb-0">
                                    <label for="address">{{ __('Address') }}</label>
                                    <textarea name="address" id="address" class="form-control" rows="4"></textarea>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-primary"
                        form="add-supplier-form">{{ __('Save') }}</button>
                </div>
            </div>
        </div>
    </div>

    {{-- edit Supplier --}}
    @foreach ($suppliers as $index => $supplier)
        <div class="modal fade" id="editSupplier{{ $supplier->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel1">{{ __('Edit Supplier') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body pt-0">
                        <form action="{{ route('admin.suppliers.update', $supplier->id) }}" method="POST"
                            id="edit-supplier-form{{ $supplier->id }}">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">{{ __('Supplier Name') }}<span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="name" name="name"
                                            value="{{ $supplier->name }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="company">{{ __('Company') }}</label>
                                        <input type="text" class="form-control" id="company" name="company"
                                            value="{{ $supplier->company }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="phone">{{ __('Phone') }}</label>
                                        <input type="text" class="form-control" id="phone" name="phone"
                                            value="{{ $supplier->phone }}">
                                    </div>
                                </div>
                                <div class="col-md-6 ">
                                    <div class="form-group">
                                        <label for="email">{{ __('Email') }}</label>
                                        <input type="email" class="form-control" id="email" name="email"
                                            value="{{ $supplier->email }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-0">
                                        <label for="group_id">{{ __('Supplier Group') }}</label>
                                        <select name="group_id" id="group_id" class="form-select">
                                            <option value="">{{ __('Select Group') }}</option>
                                            @foreach ($groups as $group)
                                                <option value="{{ $group->id }}" @selected($supplier->group_id == $group->id)>
                                                    {{ $group->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-0">
                                        <label for="area_id">{{ __('Area') }}</label>
                                        <div class="input-group">
                                            <span class="input-group-text" id="basic-addon11"><i
                                                    class="fas fa-map-marker-alt"></i></span>

                                            <select name="area_id" id="area_id" class="form-control">
                                                <option value="">{{ __('Select Area') }}</option>
                                                @foreach ($areaList as $list)
                                                    <option value="{{ $list->id }}" @selected($supplier->area_id == $list->id)>
                                                        {{ $list->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="date">{{ __('Date') }}</label>
                                        <input type="text" class="form-control datepicker" id="date"
                                            name="date" placeholder="MM/DD/YY" autocomplete="off"
                                            value="{{ $supplier->date ?? '' }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="status">{{ __('Status') }}</label>
                                        <select name="status" id="status" class="form-control">
                                            <option value="1" @if ($supplier->status == 1) selected @endif>
                                                {{ __('Active') }}</option>
                                            <option value="0" @if ($supplier->status == 0) selected @endif>
                                                {{ __('Inactive') }}</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group mb-0">
                                        <label for="address">{{ __('Address') }}</label>
                                        <textarea name="address" id="address" class="form-control" rows="4">{{ $supplier->address }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger"
                            data-bs-dismiss="modal">{{ __('Close') }}</button>
                        <button type="submit" class="btn btn-primary"
                            form="edit-supplier-form{{ $supplier->id }}">{{ __('Update') }}</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach


    {{-- Show Supplier --}}
    @foreach ($suppliers as $index => $supplier)
        <div class="modal fade" id="showSupplier{{ $supplier->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel1">{{ __('Supplier') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body py-0">
                        <div class="row">
                            {{-- table --}}
                            <div class="col-md-12">
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
                                            <th>{{ __('City') }}</th>
                                            <td>{{ $supplier->city }}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ __('State') }}</th>
                                            <td>{{ $supplier->state }}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ __('Country') }}</th>
                                            <td>{{ $supplier->country }}</td>
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
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger"
                            data-bs-dismiss="modal">{{ __('Close') }}</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endsection


@push('js')
    <script>
        $('.export').on('click', function() {
            // get full url including query string
            var fullUrl = window.location.href;
            if (fullUrl.includes('?')) {
                fullUrl += '&export=true';
            } else {
                fullUrl += '?export=true';
            }

            window.location.href = fullUrl;
        })

        function deleteData(id) {
            let url = "{{ route('admin.suppliers.destroy', ':id') }}"
            url = url.replace(':id', id);
            $("#deleteForm").attr("action", url);
            $('#deleteModal').modal('show');
        }

        function status(id) {
            handleStatus("{{ route('admin.suppliers.status', '') }}/" + id)

            let status = $('[data-status=' + id + ']').text()
            // remove whitespaces using regex
            status = status.replaceAll(/\s/g, '');
            $('[data-status=' + id + ']').text(status != 'Deactivated' ? 'Deactivated' :
                'Activate')
        }
    </script>
@endpush
