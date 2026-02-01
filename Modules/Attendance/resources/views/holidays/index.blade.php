@extends('admin.layouts.master')
@section('title', __('Holiday Setup'))
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
                                        <option value="id" {{ request('order_type') == 'id' ? 'selected' : '' }}>
                                            {{ __('ID') }}</option>

                                        <option value="name" {{ request('order_type') == 'name' ? 'selected' : '' }}>
                                            {{ __('Name') }}</option>

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
                                    <button type="submit" class="btn bg-label-primary">Search</button>
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
            <h4 class="section_title">Holiday list</h4>

            <div class="btn-actions-pane-right actions-icon-btn">

                @adminCan('attendance.setting.create')
                    <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#addHoliday" class="btn bg-label-primary"> <i
                            class="fa fa-plus"></i> {{ __('Add Holiday') }}</a>
                @endadminCan
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive list_table">
                <table style="width: 100%;" class="table">
                    <thead>
                        <tr>
                            <th>{{ __('ID') }}</th>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Description') }}</th>
                            <th>{{ __('Start date') }}</th>
                            <th>{{ __('End date') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Action') }}</th>
                        </tr>

                    </thead>
                    <tbody>
                        @foreach ($holidays as $index => $day)
                            <tr>
                                <td>{{ $holidays->firstItem() + $index }}</td>
                                <td>{{ $day->name }}</td>
                                <td>{{ $day->description }}</td>
                                <td>{{ formatDate($day->start_date) }}</td>
                                <td>{{ formatDate($day->end_date) }}</td>
                                <td>{{ $day->status ? 'Active' : 'Inactive' }}</td>
                                <td>
                                    @if (checkAdminHasPermission('attendance.setting.edit') || checkAdminHasPermission('attendance.setting.delete'))
                                        <div class="btn-group" role="group">
                                            <button id="btnGroupDrop{{ $day->id }}" type="button"
                                                class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown"
                                                aria-haspopup="true" aria-expanded="false">
                                                Action
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop{{ $day->id }}">
                                                @adminCan('attendance.setting.edit')
                                                    <a href="javascript:;" data-bs-toggle="modal"
                                                        data-bs-target="#editHoliday{{ $day->id }}"
                                                        class="dropdown-item">{{ __('Edit') }}</a>
                                                @endadminCan
                                                @adminCan('attendance.setting.delete')
                                                    <a href="javascript:;" class="dropdown-item"
                                                        onclick="deleteData({{ $day->id }})">
                                                        {{ __('Delete') }}
                                                    </a>
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
                    {{ $holidays->onEachSide(0)->links() }}
                </div>
            @endif
        </div>
    </div>


    <div class="modal fade" id="addHoliday" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">{{ __('Add Holiday') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-0">
                    <form action="{{ route('admin.attendance.settings.holidays.store') }}" method="POST"
                        id="add-holiday-form">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="name">{{ __('Holiday Name') }}<span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        placeholder="Name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="date">{{ __('Start Date') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control datepicker" id="start_date"
                                        name="start_date" placeholder="MM/DD/YY" autocomplete="off" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="date">{{ __('End Date') }}<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control datepicker" id="end_date" name="end_date"
                                        placeholder="MM/DD/YY" autocomplete="off" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="status">{{ __('Status') }}</label>
                                    <select name="status" id="status" class="form-control mb-0" required>
                                        <option value="1">{{ __('Active') }}</option>
                                        <option value="0">{{ __('Inactive') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group mt-4 mb-0">
                                    <label for="description">{{ __('Description') }}<span
                                            class="text-danger">*</span></label>
                                    <textarea name="description" id="description" class="form-control" rows="4" required></textarea>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-primary" form="add-holiday-form">{{ __('Create') }}</button>
                </div>
            </div>
        </div>
    </div>

    {{-- edit modal --}}
    @foreach ($holidays as $day)
        <div class="modal fade" id="editHoliday{{ $day->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel1">{{ __('Edit Holiday') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body pt-0">
                        <form action="{{ route('admin.attendance.settings.holidays.update', $day->id) }}" method="POST"
                            id="edit-holiday-form{{ $day->id }}">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="name">{{ __('Holiday Name') }}<span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="name" name="name"
                                            placeholder="Name" value="{{ $day->name }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="date">{{ __('Start Date') }} <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control datepicker" id="start_date"
                                            name="start_date" placeholder="MM/DD/YY" value="{{ formatDate($day->start_date) }}"
                                            autocomplete="off" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="date">{{ __('End Date') }}<span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control datepicker" id="end_date"
                                            name="end_date" placeholder="MM/DD/YY" value="{{ formatDate($day->end_date) }}"
                                            autocomplete="off" required>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="status">{{ __('Status') }}</label>
                                        <select name="status" id="status" class="form-control mb-0">
                                            <option value="1" @if ($day->status == 1) selected @endif>
                                                {{ __('Active') }}</option>
                                            <option value="0" @if ($day->status == 0) selected @endif>
                                                {{ __('Inactive') }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group mt-4 mb-0">
                                        <label for="description">{{ __('Description') }}<span
                                                class="text-danger">*</span></label>
                                        <textarea name="description" id="description" class="form-control" rows="4" required>{{ $day->description }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger"
                            data-bs-dismiss="modal">{{ __('Close') }}</button>
                        <button type="submit" class="btn btn-primary"
                            form="edit-holiday-form{{ $day->id }}">{{ __('Update') }}</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endsection



@push('js')
    <script>
        'use strict';

        function deleteData(id) {
            let url = "{{ route('admin.attendance.settings.holidays.destroy', ':id') }}"
            url = url.replace(':id', id);
            $("#deleteForm").attr("action", url);
            $('#deleteModal').modal('show');
        }
    </script>
@endpush
