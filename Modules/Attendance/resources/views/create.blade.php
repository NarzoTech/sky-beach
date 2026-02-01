@extends('admin.layouts.master')
@section('title', __('Attendance'))
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
                                    <input type="text" name="date" class="form-control datepicker"
                                        value="{{ request()->get('date') ?? formatDate(now(), 'Y-m-d') }}"
                                        placeholder="{{ __('Date') }}" data-date-end-date="0d" autocomplete="off">
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
    <div class="row mt-5">
        @php
            $currentDate = request()->get('date') ?? formatDate(now(), 'Y-m-d');
        @endphp
        <div class="col-12">
            <div class="card  {{ !$currentDate ? 'd-none' : '' }}">
                <div class="card-header d-flex justify-content-between">
                    <h4 class="section_title">{{ __('Employee List') }}</h4>
                    <div class="attendance_type d-none d-flex justify-content-center align-items-center">
                        <div class="selectgroup w-100">
                            <label class="selectgroup-item">
                                <input type="radio" name="attendance_type" value="present" class="selectgroup-input">
                                <span class="selectgroup-button selectgroup-button-icon">{{ __('Present') }}</span>
                            </label>
                            <label class="selectgroup-item">
                                <input type="radio" name="attendance_type" value="absent" class="selectgroup-input">
                                <span class="selectgroup-button selectgroup-button-icon">{{ __('Absent') }}</span>
                            </label>
                        </div>
                        @adminCan('attendance.create')
                            <div class="button-container d-none">
                                <button class="btn btn-success ms-2 submit-button" type="submit">{{ __('Apply') }}</button>
                            </div>
                        @endadminCan
                    </div>

                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>

                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Designation') }}</th>
                                    <th>{{ __('Mobile') }}</th>
                                    <th>{{ __('Status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($currentDate)
                                    @foreach ($employees as $key => $employee)
                                        @php
                                            $atten = $employee->attendance->where('date', $currentDate)->first();
                                        @endphp
                                        <tr>

                                            <td>{{ ucwords($employee->name) }}</td>
                                            <td>{{ $employee->designation }}</td>
                                            <td>{{ $employee->mobile }}</td>
                                            <td>
                                                @adminCan('attendance.create')
                                                    <div class="selectgroup w-100" data-id="{{ $employee->id }}">
                                                        <label class="selectgroup-item">
                                                            <input type="radio" name="attendance[{{ $key }}]"
                                                                value="present" class="selectgroup-input"
                                                                {{ $atten?->status == 'present' ? 'checked' : '' }}>
                                                            <span
                                                                class="selectgroup-button selectgroup-button-icon">{{ __('Present') }}</span>
                                                        </label>
                                                        <label class="selectgroup-item">
                                                            <input type="radio" name="attendance[{{ $key }}]"
                                                                value="absent" class="selectgroup-input"
                                                                {{ $atten?->status == 'absent' ? 'checked' : '' }}>
                                                            <span
                                                                class="selectgroup-button selectgroup-button-icon">{{ __('Absent') }}</span>
                                                        </label>
                                                    </div>
                                                @endadminCan
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="float-right">
                        @if (request()->get('date'))
                            {{ $employees->onEachSide(3)->links() }}
                        @endif
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
            $(document).on('change', '.selectgroup-input', function() {
                var id = $(this).closest('.selectgroup').data('id');
                var value = $(this).val();

                if ($('[data-checkboxes="mygroup"]:checked').length > 0) {
                    return;
                }
                const data = {
                    employee_id: [id],
                    attendance: [value],
                }

                updateStatus(data)
            })

            $('#checkbox-all').on('change', function() {
                $('[data-checkboxes="mygroup"]').prop('checked', $(this).prop('checked'));

                // enable and disabled the input fields based on checkbox state
                $('[data-checkboxes="mygroup"]').each(function() {
                    var isChecked = $(this).prop('checked');
                    var index = $(this).attr('id').split('-')[
                        1]; // Extract index from checkbox ID

                    if (isChecked) {
                        $('.attendance_type').removeClass('d-none');
                    } else {
                        $('.attendance_type').addClass('d-none');
                    }
                });
                const val = $('[name="attendance_type"]:checked').val();
                changeSelectedValue(val)
            });

            $('[data-checkboxes="mygroup"]').on('change', updateSelectAllCheckbox);

            $('[name="attendance_type"]').on('change', function() {
                const val = $(this).val();
                changeSelectedValue(val)

                $('.button-container').removeClass('d-none');
            })

            $('.submit-button').on('click', function(e) {
                const data = {
                    employee_id: [],
                    attendance: [],
                }
                $('[class="selectgroup-input"]:checked').each(function() {
                    const input = $(this).parents('tr').find('[data-checkboxes="mygroup"]:checked');
                    if (input.length && $(this).closest('.selectgroup').data('id')) {

                        data.employee_id.push($(this).closest('.selectgroup').data('id'));
                        data.attendance.push($(this).val());
                    }
                })
                updateStatus(data)
            });

            $('.filter-button').on('click', function(e) {
                e.preventDefault();

                // check if date is selected
                if ($('.datepicker').val() == '') {
                    toastr.warning("{{ __('Please select date') }}", '', options);
                } else {
                    $('#search-form').submit();
                }
            })
        })

        function updateStatus(data) {
            const date = "{{ $currentDate }}"
            if (data.attendance.length > 0) {
                $.ajax({
                    type: "post",
                    data: {
                        _token: '{{ csrf_token() }}',
                        date,
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

        function updateSelectAllCheckbox() {
            var allChecked = true;
            let count = 0;
            $('[data-checkboxes="mygroup"]').each(function() {
                if (!$(this).is(':checked')) {
                    allChecked = false;
                } else {
                    count++;
                }
            });

            if (count > 0) {
                $('.attendance_type').removeClass('d-none');
                $('.button-container').removeClass('d-none');
            } else {
                $('.attendance_type').addClass('d-none');
                $('.button-container').addClass('d-none');
            }

            $('#checkbox-all').prop('checked', allChecked);

            // check if this is checked

            const input = $(this).closest('tr').find('.selectgroup-input');
            const val = $('[name="attendance_type"]:checked').val();

            if ($(this).is(':checked')) {
                input.each(function() {
                    if ($(this).val() == val) {
                        $(this).prop('checked', true);
                        $(this).trigger('change');
                    }
                })
            } else {
                input.each(function() {
                    if ($(this).val() == val) {
                        $(this).prop('checked', false);
                        $(this).trigger('change');
                    }
                })
            }
        }

        function changeSelectedValue(val) {

            $('[data-checkboxes="mygroup"]:checked').each(function() {
                const input = $(this).closest('tr').find('.selectgroup-input');
                input.each(function() {
                    if ($(this).val() == val) {
                        $(this).prop('checked', true);
                        $(this).trigger('change');
                    }
                })
            });

            $('[data-checkboxes="mygroup"]:not(:checked)').each(function() {
                const input = $(this).closest('tr').find('.selectgroup-input');
                input.each(function() {
                    if ($(this).is(':checked')) {
                        $(this).prop('checked', false);
                        $(this).trigger('change');
                    }
                })
            });
        }
    </script>
@endpush
