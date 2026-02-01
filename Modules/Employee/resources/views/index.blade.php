@extends('admin.layouts.master')
@section('title', __('Employee List'))


@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body pb-0">
                    <form class="search_form" action="" method="GET">
                        <div class="row">
                            <div class="col-xxl-4 col-md-6">
                                <div class="form-group search-wrapper">
                                    <input type="text" name="keyword" value="{{ request()->get('keyword') }}"
                                        class="form-control" placeholder="Search..." autocomplete="off">
                                    <button type="submit">
                                        <i class='bx bx-search'></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-xxl-2 col-md-6">
                                <div class="form-group">
                                    <select name="order_type" id="order_type" class="form-control">
                                        <option value="id" {{ request('order_type') == 'id' ? 'selected' : '' }}>
                                            {{ __('Serial') }}</option>
                                        <option value="name" {{ request('order_type') == 'name' ? 'selected' : '' }}>
                                            {{ __('Name') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xxl-2 col-md-6">
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
                            <div class="col-xxl-2 col-md-6">
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
                            <div class="col-xxl-2 col-md-6">
                                <div class="form-group">
                                    <button type="button" class="btn bg-danger form-reset">Reset</button>
                                    <button type="submit" class="btn bg-label-primary">Search</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card mt-5">
                <div class="card-header">
                    <div class="card-header-title font-size-lg text-capitalize font-weight-normal">
                        <h4 class="section_title"> {{ __('Employee List') }}</h4>
                    </div>
                    <div class="btn-actions-pane-right actions-icon-btn">
                        @adminCan('employee.create')
                            <a href="{{ route('admin.employee.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i>
                                {{ __('Add New Employee') }}</a>
                        @endadminCan
                        <button type="button" class="btn bg-label-success export"><i class="fa fa-file-excel"></i>
                            Excel</button>
                        <button type="button" class="btn bg-label-warning export-pdf"><i class="fa fa-file-pdf"></i>
                            PDF</button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive list_table">
                        <table style="width: 100%;" class="table mb-3">
                            <thead>
                                <tr>
                                    <th>{{ __('Sl') }}</th>
                                    <th>{{ __('Employee Name') }}</th>
                                    <th>{{ __('Employee Picture') }}</th>
                                    <th>{{ __('Designation') }}</th>
                                    <th>{{ __('Phone') }}</th>
                                    <th>{{ __('Email') }}</th>
                                    <th>{{ __('Base Salary') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Joining Date') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($employees as $index => $employee)
                                    <tr>
                                        <td>{{ ++$index }}</td>
                                        <td>{{ $employee->name }}</td>
                                        <td>
                                            <img src="{{ $employee->image ? asset($employee->image) : asset('/uploads/employee/default.png') }}"
                                                alt="" width="50px" height="50px">
                                        </td>
                                        <td>{{ $employee->designation }}</td>
                                        <td>{{ $employee->mobile }}</td>
                                        <td>{{ $employee->email }}</td>
                                        <td>{{ $employee->salary }}</td>
                                        <td>
                                            @if ($employee->status == 1)
                                                <span class="badge badge-success">
                                                    {{ __('Active') }}
                                                </span>
                                            @else
                                                <span class="badge badge-danger">
                                                    {{ __('Inactive') }}
                                                </span>
                                            @endif
                                        </td>
                                        <td>{{ formatDate($employee->join_date) }}</td>
                                        <td>
                                            @if (checkAdminHasPermission('employee.view.payment') ||
                                                    checkAdminHasPermission('employee.pay.salary') ||
                                                    checkAdminHasPermission('employee.pay.advance') ||
                                                    checkAdminHasPermission('employee.status') ||
                                                    checkAdminHasPermission('employee.delete'))
                                                <div class="btn-group" role="group">
                                                    <button id="btnGroupDrop{{ $employee->id }}" type="button"
                                                        class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown"
                                                        aria-haspopup="true"
                                                        aria-expanded="false">{{ __('Action') }}</button>
                                                    <div class="dropdown-menu"
                                                        aria-labelledby="btnGroupDrop{{ $employee->id }}">
                                                        @adminCan('employee.edit')
                                                            <a class="dropdown-item"
                                                                href="{{ route('admin.employee.edit', $employee->id) }}">{{ __('Edit') }}</a>
                                                        @endadminCan
                                                        @adminCan('employee.view.payment')
                                                            <a class="dropdown-item view-payment" href="javascript:;"
                                                                data-id="{{ $employee->id }}">{{ __('View Payments') }}</a>
                                                        @endadminCan
                                                        @adminCan('employee.pay.salary')
                                                            <a class="dropdown-item"
                                                                href="{{ route('admin.employee.salary.create', $employee->id) }}?pay=1">{{ __('Pay Salary') }}</a>
                                                        @endadminCan
                                                        @adminCan('employee.pay.advance')
                                                            <a class="dropdown-item"
                                                                href="{{ route('admin.employee.salary.create', $employee->id) }}?pay=2">{{ __('Pay Advance') }}</a>
                                                        @endadminCan
                                                        @adminCan('employee.status')
                                                            <a class="dropdown-item"
                                                                href="{{ route('admin.employee.status', $employee->id) }}">{{ $employee->status == 1 ? __('Inactive') : __('Active') }}</a>
                                                        @endadminCan
                                                        @adminCan('employee.delete')
                                                            <a href="javascript:;" class="dropdown-item"
                                                                onclick="deleteData({{ $employee->id }})">{{ __('Delete') }}</a>
                                                        @endadminCan
                                                    </div>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <x-empty-table :name="__('Employee List')" route="" create="no" :message="__('No data found!')"
                                        colspan="10"></x-empty-table>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if (request()->get('par-page') !== 'all')
                        <div class="float-right">
                            {{ $employees->onEachSide(0)->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>




    <div tabindex="-1" role="dialog" id="viewDate" class ='modal'>
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Pick Date') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body py-0">
                    <div id="calendar">
                        <form id="viewDateForm" action="" method="get">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('Months') }}</label>
                                        @php
                                            $months = [];
                                            for ($i = 1; $i <= 12; $i++) {
                                                $m = date('m', mktime(0, 0, 0, $i, 1));
                                                $months[$m] = date('F', mktime(0, 0, 0, $i, 1));
                                            }
                                        @endphp
                                        <select name="month" id="month" class="form-control">
                                            <option value="">{{ __('Select Month') }}</option>
                                            @foreach ($months as $key => $month)
                                                <option value="{{ $month }}"
                                                    {{ $month == date('F') ? 'selected' : '' }}>{{ $month }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('Year') }}</label>
                                        @php
                                            $years = [];
                                            for ($i = 0; $i < 5; $i++) {
                                                $years[] = date('Y') - $i;
                                            }

                                        @endphp
                                        <select name="year" id="year" class="form-control">
                                            <option value="">{{ __('Select Year') }}</option>
                                            @foreach ($years as $year)
                                                <option value="{{ $year }}"
                                                    {{ $year == date('Y') ? 'selected' : '' }}>{{ $year }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="modal-footer bg-whitesmoke br">
                    <button type="submit" class="btn btn-primary" form="viewDateForm">{{ __('Show') }}</button>
                </div>
            </div>
        </div>
    </div>


    @push('js')
        <script>
            function deleteData(id) {
                let url = "{{ route('admin.employee.destroy', ':id') }}"
                url = url.replace(':id', id);
                $("#deleteForm").attr("action", url);
                $('#deleteModal').modal('show');
            }


            $('.view-payment').on('click', function() {
                var id = $(this).data('id');
                $('#viewDate').modal('show');
                let url = "{{ route('admin.employee.salary.view', ':id') }}";
                url = url.replace(':id', id);
                $('#viewDateForm').attr('action', url);
            })
        </script>
    @endpush
@endsection
