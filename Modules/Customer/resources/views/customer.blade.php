@extends('admin.layouts.master')
@section('title', __('Customers List'))
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
                                    <button type="button" class="btn bg-danger form-reset">Reset</button>
                                    <button type="submit" class="btn bg-primary">Search</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-5 mb-5">
        <div class="card-header-tab card-header">
            <div class="card-header-title font-size-lg text-capitalize font-weight-normal">
                <h4 class="section_title"> {{ __('Customers List') }}</h4>
            </div>
            <div class="btn-actions-pane-right actions-icon-btn">

                @adminCan('customer.bulk.import')
                    <a href="{{ route('admin.customers.import') }}" class="btn btn-primary"><i class="fa fa-upload"></i>
                        {{ __('Import Customers') }}</a>
                @endadminCan
                @adminCan('customer.bulk.delete')
                    <a href="javascript:;" class="btn btn-danger" onclick="deleteAllCustomers()" data-bs-toggle="modal"
                        data-bs-target="#deleteAllCustomers">{{ __('Delete All Customer') }}</a>
                @endadminCan
                @adminCan('customer.create')
                    <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#addCustomer" class="btn btn-primary"> <i
                            class="fa fa-plus"></i>
                        {{ __('Add Customer') }}</a>
                @endadminCan
                @adminCan('customer.excel.download')
                    <button type="button" class="btn btn-primary export"><i class="fa fa-file-excel"></i>
                        Excel</button>
                @endadminCan
                @adminCan('customer.pdf.download')
                    <button type="button" class="btn btn-success export-pdf"><i class="fa fa-file-pdf"></i>
                        PDF</button>
                @endadminCan
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table style="width: 100%;" class="table mb-5 customer-table">
                    <thead>
                        <tr>
                            <th>{{ __('SN') }}</th>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Phone') }}</th>
                            <th>{{ __('Area') }}</th>
                            <th>{{ __('Total Sale') }}</th>
                            <th>{{ __('Sale Payment') }}</th>
                            <th>{{ __('Sale Due') }}</th>
                            <th>{{ __('Advance') }}</th>
                            <th>{{ __('Total Due') }}</th>
                            <th>{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $index => $user)
                            <tr>
                                <td>{{ $users->firstItem() + $index }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->phone }}</td>
                                <td>{{ $user->area->name }}</td>
                                <td>{{ currency($user->sales->sum('grand_total')) }}</td>
                                <td>{{ currency($user->total_paid) }}</td>
                                <td>{{ currency($user->total_due) }}</td>
                                <td>{{ currency($user->advances()) }}</td>
                                <td>{{ currency($user->total_due - $user->total_sale_return_due - $user->advances()) }}
                                </td>

                                <td>
                                    <div class="btn-group" role="group">
                                        <button id="btnGroupDrop{{ $user->id }}" type="button"
                                            class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                            Action
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="btnGroupDrop{{ $user->id }}">

                                            @adminCan('customer.view')
                                                <a class="dropdown-item" href="javascript:;" data-bs-toggle="modal"
                                                    data-bs-target="#showCustomer{{ $user->id }}">Show</a>
                                            @endadminCan
                                            @adminCan('customer.edit')
                                                <a class="dropdown-item" href="javascript:;" data-bs-toggle="modal"
                                                    data-bs-target="#editCustomer{{ $user->id }}">Edit</a>
                                            @endadminCan

                                            @adminCan('customer.due.receive')
                                                @if ($user->total_due)
                                                    <a class="dropdown-item"
                                                        href="{{ route('admin.customer.due-receive') }}?customer={{ $user->id }}">Due
                                                        Receive</a>
                                                @endif
                                            @endadminCan
                                            @adminCan('customer.due.receive.list')
                                                <a class="dropdown-item"
                                                    href="{{ route('admin.customers.due-receive.list') }}?customer={{ $user->id }}">Due
                                                    Receive List</a>
                                            @endadminCan
                                            @adminCan('sales.return')
                                                <a class="dropdown-item"
                                                    href="{{ route('admin.sales.return.list') }}?customer={{ $user->id }}">Sales
                                                    Return</a>
                                            @endadminCan
                                            @adminCan('customer.due.receive')
                                                <a class="dropdown-item"
                                                    href="{{ route('admin.customer.due-receive') }}?customer={{ $user->id }}">Dismiss</a>
                                            @endadminCan
                                            @adminCan('customer.status')
                                                <a class="dropdown-item" href="javascript:;"
                                                    onclick="status('{{ $user->id }}')"
                                                    data-status="{{ $user->id }}">
                                                    {{ $user->status == 1 ? 'Deactivated' : 'Activate' }}
                                                </a>
                                            @endadminCan
                                            @adminCan('customer.sales.list')
                                                <a class="dropdown-item"
                                                    href="{{ route('admin.sales.index') }}?customer={{ $user->id }}">Sales</a>
                                            @endadminCan
                                            @adminCan('customer.ledger')
                                                <a class="dropdown-item"
                                                    href="{{ route('admin.customers.ledger', $user->id) }}">{{ __('Ledger') }}</a>
                                            @endadminCan
                                            @adminCan('customer.advance')
                                                <a class="dropdown-item"
                                                    href="{{ route('admin.customers.advance', $user->id) }}">{{ __('Advance') }}</a>
                                            @endadminCan
                                            @adminCan('customer.delete')
                                                <a href="javascript:;" class="dropdown-item"
                                                    onclick="deleteData({{ $user->id }})">
                                                    Delete</a>
                                            @endadminCan
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach

                        <tr>
                            <td colspan="4" class="text-center fw-bold">
                                {{ __('Total') }}
                            </td>
                            <td class="fw-bold">
                                {{ currency($data['totalSale']) }}
                            </td>
                            <td class="fw-bold">
                                {{ currency($data['pay']) }}
                            </td>
                            <td class="fw-bold">
                                {{ currency($data['total_due']) }}
                            </td>
                            <td class="fw-bold">
                                {{ currency($data['total_advance']) }}
                            </td>
                            <td class="fw-bold" colspan="2">
                                {{ currency($data['total_due'] - $data['total_return_due']) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            @if (request()->get('par-page') !== 'all')
                <div class="float-right">
                    {{ $users->onEachSide(0)->links() }}
                </div>
            @endif
        </div>
    </div>


    {{-- add customer --}}
    @include('customer::customer-modal')


    {{-- edit customer --}}
    @foreach ($users as $index => $user)
        <div class="modal fade" id="editCustomer{{ $user->id }}">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title">{{ __('Edit Customer') }}</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body pt-0">
                        <form class="edit_customer_form" action="{{ route('admin.customers.update', $user->id) }}"
                            method="POST" id="edit-customer-form{{ $user->id }}">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">{{ __('Customer Name') }}<span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="name" name="name"
                                            value="{{ $user->name }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="group_id">{{ __('Customer Group') }}</label>
                                        <select name="group_id" id="group_id" class="form-control">
                                            <option value="">{{ __('Select Group') }}</option>
                                            @foreach ($groups as $group)
                                                <option value="{{ $group->id }}"
                                                    {{ $user->group_id == $group->id ? 'selected' : '' }}>
                                                    {{ $group->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="phone">{{ __('Phone') }}</label>
                                        <input type="text" class="form-control" id="phone" name="phone"
                                            value="{{ $user->phone }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email">{{ __('Email') }}</label>
                                        <input type="email" class="form-control" id="email" name="email"
                                            value="{{ $user->email }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="area_id">{{ __('Area') }}</label>
                                        <select name="area_id" id="area_id" class="form-control">
                                            <option value="">{{ __('Select Area') }}</option>
                                            @foreach ($areaList as $list)
                                                <option value="{{ $list->id }}"
                                                    {{ $user->area_id == $list->id ? 'selected' : '' }}>
                                                    {{ $list->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="plate_number">{{ __('Plate Number') }}</label>
                                        <input type="text" class="form-control" id="plate_number" name="plate_number"
                                            value="{{ $user->plate_number }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="membership">{{ __('Membership') }}</label>
                                        <input type="text" class="form-control" id="membership" name="membership"
                                            value="{{ $user->membership }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="date">{{ __('Date') }}</label>
                                        <input type="text" class="form-control datepicker" id="date"
                                            name="date" value="{{ formatDate($user->date) }}" autocomplete="off">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="due">{{ __('Initial Due') }}</label>
                                        <input type="number" step="0.01" class="form-control" id="due" name="due"
                                            value="{{ $user->wallet_balance }}" placeholder="0.00">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="initial_advance">{{ __('Initial Advance') }}</label>
                                        <input type="number" step="0.01" class="form-control" id="initial_advance" name="initial_advance"
                                            value="{{ $user->initial_advance }}" placeholder="0.00">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="status">{{ __('Status') }}</label>
                                        <select name="status" id="status" class="form-control">
                                            <option value="1" {{ $user->status == 1 ? 'selected' : '' }}>
                                                {{ __('Active') }}</option>
                                            <option value="0" {{ $user->status == 0 ? 'selected' : '' }}>
                                                {{ __('Inactive') }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">

                                    <div class="form-group mb-0">
                                        <div class="guest_customer_check mt-0">
                                            <label class="switch switch-square">
                                                <input type="checkbox" name="guest" class="switch-input"
                                                    value="1" @if ($user->guest) checked @endif />
                                                <span class="switch-toggle-slider">
                                                    <span class="switch-on"><i class="bx bx-check"></i></span>
                                                    <span class="switch-off"><i class="bx bx-x"></i></span>
                                                </span>
                                                <span class="switch-label">{{ __('Guest Customer') }}</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary"
                            form="edit-customer-form{{ $user->id }}">{{ __('Update') }}</button>
                    </div>

                </div>
            </div>
        </div>
    @endforeach


    {{-- Show customer --}}
    @foreach ($users as $index => $user)
        <div class="modal fade" id="showCustomer{{ $user->id }}">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title">{{ __('Customer') }}</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body py-0">
                        <div class="row">
                            {{-- table --}}
                            <div class="col-md-12">
                                <table class="table table-bordered">
                                    <tr>
                                        <th>{{ __('Name') }}</th>
                                        <td>{{ $user->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Phone') }}</th>
                                        <td>{{ $user->phone }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Email') }}</th>
                                        <td>{{ $user->email }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('City') }}</th>
                                        <td>{{ $user->city }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Tax Number') }}</th>
                                        <td>{{ $user->tax_number }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Status') }}</th>
                                        <td>{{ $user->status == 1 ? 'Active' : 'Inactive' }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Address') }}</th>
                                        <td>{{ $user->address }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    </div>

                </div>
            </div>
        </div>
    @endforeach



    <div tabindex="-1" role="dialog" id="deleteAllCustomers" class ='modal fade'>
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Item Delete Confirmation') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body">
                    <p>{{ __('Are You sure want to delete all Customers ?') }}</p>
                    <form id="allDeleteForm" action="{{ route('admin.delete.all-customers') }}" method="POST">

                        @csrf
                        @method('DELETE')
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <input type="text" class="form-control" name="password" id="password"
                                        placeholder="Enter Password *">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer bg-whitesmoke br">

                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-primary"
                        form="allDeleteForm">{{ __('Yes, Delete') }}</button>
                </div>
            </div>
        </div>
    </div>

    @push('js')
        <script>
            function deleteData(id) {
                let url = '{{ route('admin.customers.destroy', ':id') }}';
                url = url.replace(':id', id);
                $("#deleteForm").attr('action', url);
                $('#deleteModal').modal('show');
            }

            function deleteAllCustomers() {
                $("#deleteAllCustomers").attr("action", '{{ route('admin.delete.all-customers') }}')
            }

            function status(id) {
                handleStatus("{{ route('admin.customers.status', '') }}/" + id)

                let status = $('[data-status=' + id + ']').text()
                // remove whitespaces using regex
                status = status.replaceAll(/\s/g, '');
                $('[data-status=' + id + ']').text(status != 'Deactivated' ? 'Deactivated' :
                    'Activate')
            }
        </script>
    @endpush
@endsection
