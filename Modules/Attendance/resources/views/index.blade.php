@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Attendance Sheet') }}</title>
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
                                    <input type="text" id="monthYearPicker" class="form-control"
                                        value="{{ request()->get('month_year') ?? formatDate(now(), 'm/Y') }}"
                                        name="month_year" autocomplete="off">
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


    <div class="row mt-5">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h4 class="section_title">{{ __('Employee List') }}</h4>
                </div>
                <div class="card-body pt-0">
                    <div class="table-responsive attendance_table_scroll">
                        <table class="table attendance-table">
                            <thead>
                                @php
                                    $month_year = request()->month_year ?? formatDate(now(), 'm/Y');

                                    $date = \Carbon\Carbon::createFromFormat('m/Y', $month_year);

                                    $month = $date->month;
                                    $year = $date->year;

                                    $totalDays = now()->month($month)->daysInMonth;
                                @endphp
                                <tr>
                                    <th rowspan="2" class="text-bottom sticky-1">{{ __('Name') }}</th>
                                    <th rowspan="2" class="sticky-2">{{ __('Mobile') }}</th>
                                    <th rowspan="1" colspan="3" class="text-center">
                                        {{ __('Total') }}
                                    </th>
                                    <th rowspan="1" colspan="{{ $totalDays }}" class="text-center">
                                        {{ __('Date') }}</th>
                                </tr>
                                <tr>
                                    <td colspan="1" class="text-white bg-green text-center th-present">P
                                    </td>
                                    <td colspan="1" class="text-white  bg-red  text-center th-absent">A
                                    </td>
                                    <td colspan="1" class="bg-warning  text-center th-absent text-white">W
                                    </td>
                                    @for ($i = 1; $i <= $totalDays; $i++)
                                        <td class="text-center  border-top">{{ $i }}</td>
                                    @endfor
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($employees as $key => $employee)
                                    @php
                                        $attendance = $employee->attendance;
                                        $present = $attendance->where('status', 'present');
                                        $absent = $attendance->where('status', 'absent');
                                        $weekend = $attendance->where('status', 'weekend');

                                    @endphp

                                    <tr>
                                        <td class="sticky-1">{{ ucwords($employee->name) }}</td>
                                        <td class="sticky-2">{{ $employee->mobile }}</td>
                                        <td class="text-center ">{{ $present->count() }}</td>
                                        <td class="text-center">{{ $absent->count() }}</td>
                                        <td class="text-center">{{ $weekend->count() }}</td>
                                        @for ($i = 1; $i <= $totalDays; $i++)
                                            @php
                                                $date = "$year-$month-$i";
                                                $date = now()->parse($date);

                                                $isPresent = $attendance
                                                    ->where('status', 'present')
                                                    ->where('date', $date->format('Y-m-d'))
                                                    ->first();
                                                $isAbsent = $attendance
                                                    ->where('status', 'absent')
                                                    ->where('date', $date->format('Y-m-d'))
                                                    ->first();
                                                $isWeekend = $attendance
                                                    ->where('status', 'weekend')
                                                    ->where('date', $date->format('Y-m-d'))
                                                    ->first();

                                                $joinDate = $employee->join_date ? \Carbon\Carbon::parse($employee->join_date) : null;
                                                $isBeforeJoinDate = $joinDate && $date->lt($joinDate);
                                            @endphp
                                            <td class="text-center">
                                                <div class="dropdown">
                                                    <a class="btn {{ !$isBeforeJoinDate ? 'dropdown-toggle' : '' }} {{ $isPresent ? 'present' : ($isAbsent ? 'absent' : ($isWeekend ? 'weekend' : '')) }}"
                                                        href="javascript:;" role="button" {{ !$isBeforeJoinDate ? 'data-bs-toggle=dropdown' : '' }}
                                                        aria-expanded="false"
                                                        style="background:{{ $isBeforeJoinDate ? '#ccc' : ($isPresent ? 'green' : ($isAbsent ? 'red' : ($isWeekend ? '#e69500' : ''))) }}; color:{{ $isPresent || $isAbsent ? 'white' : 'black' }}; {{ $isBeforeJoinDate ? 'cursor: not-allowed; opacity: 0.5;' : '' }}"
                                                        {{ $isBeforeJoinDate ? 'title=Before joining date' : '' }}>
                                                    </a>
                                                    @adminCan('attendance.create')
                                                        @if(!$isBeforeJoinDate)
                                                        <ul class="dropdown-menu">

                                                            <li><a class="dropdown-item attendance" href="javascript:;"
                                                                    data-employee-id={{ $employee->id }}
                                                                    data-date={{ $date->format('Y-m-d') }}
                                                                    data-join-date="{{ $employee->join_date ? \Carbon\Carbon::parse($employee->join_date)->format('Y-m-d') : '' }}"
                                                                    data-value="present">{{ __('Present') }}</a>
                                                            </li>

                                                            <li>
                                                                <a class="dropdown-item attendance" href="javascript:;"
                                                                    data-employee-id={{ $employee->id }}
                                                                    data-date={{ $date->format('Y-m-d') }}
                                                                    data-join-date="{{ $employee->join_date ? \Carbon\Carbon::parse($employee->join_date)->format('Y-m-d') : '' }}"
                                                                    data-value="absent">{{ __('Absent') }}</a>
                                                            </li>
                                                            <li>
                                                                <a class="dropdown-item attendance" href="javascript:;"
                                                                    data-employee-id={{ $employee->id }}
                                                                    data-date={{ $date->format('Y-m-d') }}
                                                                    data-join-date="{{ $employee->join_date ? \Carbon\Carbon::parse($employee->join_date)->format('Y-m-d') : '' }}"
                                                                    data-value="weekend">{{ __('Weekend') }}</a>
                                                            </li>
                                                            <li>
                                                                <a class="dropdown-item attendance text-danger"
                                                                    href="javascript:;" data-employee-id={{ $employee->id }}
                                                                    data-date={{ $date->format('Y-m-d') }}
                                                                    data-join-date="{{ $employee->join_date ? \Carbon\Carbon::parse($employee->join_date)->format('Y-m-d') : '' }}"
                                                                    data-value="clear">{{ __('Clear Attendance') }}</a>
                                                            </li>
                                                        </ul>
                                                        @endif
                                                    @endadminCan
                                                </div>
                                            </td>
                                        @endfor
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="float-right">
                        {{ $employees->onEachSide(3)->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection



@push('js')
    <script>
        'use strict';
        $(document).ready(function() {
            $(document).on('click', '.attendance', function() {
                const id = $(this).data('employee-id');
                const date = $(this).data('date');
                const joinDate = $(this).data('join-date');

                // check  if date is after today
                const today = new Date("{{ formatDate(now(), 'Y-m-d') }}");
                const selectedDate = new Date(date);

                if (selectedDate > today) {
                    toastr.warning("{{ __('You can not mark attendance for future date') }}", '', options);
                    return;
                }

                // check if date is before join date
                if (joinDate) {
                    const employeeJoinDate = new Date(joinDate);
                    if (selectedDate < employeeJoinDate) {
                        toastr.warning("{{ __('You can not mark attendance before employee joining date') }}", '', options);
                        return;
                    }
                }

                const value = $(this).data('value');

                const data = {
                    employee_id: [id],
                    date,
                    attendance: [value],
                }
                if (value === 'present') {
                    const a = $(this).parents('.dropdown-menu').siblings('a');
                    a.css({
                        'background': 'green',
                        'color': 'white'
                    }).addClass('present');

                    // check if a has any class of present or absent
                    if (a.hasClass('absent')) {
                        a.removeClass('absent');
                    }
                } else if (value === 'absent') {
                    const a = $(this).parents('.dropdown-menu').siblings('a');
                    a.css({
                        'background': 'red',
                        'color': 'white'
                    }).addClass('absent');

                    // check if a has any class of present or absent
                    if (a.hasClass('present')) {
                        a.removeClass('present');
                    }
                } else if (value === 'weekend') {
                    const a = $(this).parents('.dropdown-menu').siblings('a');
                    a.css({
                        'background': '#e69500',
                    }).addClass('weekend');
                } else {
                    const a = $(this).parents('.dropdown-menu').siblings('a');
                    a.css({
                        'background': '#ddd',
                        'color': 'black'
                    }).removeClass('present absent');
                }

                updateStatus(data)

            })

            $('#monthYearPicker').datepicker({
                format: "mm/yyyy", // Date format
                minViewMode: 1, // Only show months and years
                startView: 1, // Start with months view
                autoclose: true // Close picker after selection
            });
        })

        function updateStatus(data) {

            if (data.attendance.length > 0) {
                $.ajax({
                    type: "post",
                    data: {
                        _token: '{{ csrf_token() }}',
                        ...data
                    },
                    url: "{{ route('admin.attendance.store') }}",
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message, '', options);
                        } else {
                            toastr.warning(response.message, '', options);
                        }
                    },
                    error: function(error) {
                        handleError(error);
                    }
                })
            } else {
                toastr.warning("Please mark attendance", '', options);
            }
        }
    </script>
@endpush
