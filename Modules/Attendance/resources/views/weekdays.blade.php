@extends('admin.layouts.master')
@section('title', __('Weekdays Setup'))
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body pb-0">
                    <form class="form_search" action="" method="GET" id="search-form" onchange="this.submit();">
                        <div class="row">
                            <div class="col-lg-3 col-md-6">
                                <div class="form-group search-wrapper">
                                    <input type="text" name="keyword" value="{{ request()->get('keyword') }}"
                                        class="form-control" placeholder="{{ __('Search by name or ID') }}">
                                    <button class="search_button" type="submit">
                                        <i class='bx bx-search'></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <div class="form-group">
                                    <select name="subscription_status" id="subscription_status" class="form-select">
                                        <option value="">{{ __('Subscription Status') }}</option>
                                        <option value="all">{{ __('All') }}</option>
                                        <option value="1"
                                            {{ request('subscription_status') == '1' ? 'selected' : '' }}>
                                            {{ __('Active') }}
                                        </option>
                                        <option value="expired"
                                            {{ request('subscription_status') == 'expired' ? 'selected' : '' }}>
                                            {{ __('Expired') }}
                                        </option>
                                        <option value="no_plan"
                                            {{ request('subscription_status') == 'no_plan' ? 'selected' : '' }}>
                                            {{ __('No Plan') }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <div class="form-group">
                                    <select name="par-page" id="par-page" class="form-select">
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
                                    <button type="button" class="btn bg-danger form-reset">Reset</button>
                                    <button type="submit" class="btn bg-label-primary">Search</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @php
            $currentDate = request()->get('date') ?? formatDate(now(), 'Y-m-d');
        @endphp

        <div class="col-12 mt-5">
            <div class="card  {{ !$currentDate ? 'd-none' : '' }}">
                <div class="card-header d-flex justify-content-between">
                    <h4 class="section_title">{{ __('Weekdays Setup') }}</h4>
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
                        <div class="button-container d-none">
                            <button class="btn btn-success ms-2 submit-button" type="submit">{{ __('Apply') }}</button>
                        </div>
                    </div>

                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table common_table">
                            <thead>
                                <tr>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Weekend') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach ($days as $key => $day)
                                    <tr>
                                        <td>{{ $day->name }}</td>
                                        <td>{{ $day->is_weekend ? 'Yes' : 'No' }}</td>
                                        <td>{{ $day->status ? 'Active' : 'Inactive' }}</td>
                                        <td>
                                            @adminCan('attendance.setting.edit')
                                                <a href="javascript:;" class="btn bg-label-primary" data-bs-toggle="modal"
                                                    data-bs-target="#edit_weekday_{{ $day->id }}">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endadminCan
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>


    @foreach ($days as $day)
        <div class="modal fade" id="edit_weekday_{{ $day->id }}">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header card-header">
                        <h4 class="section_title">{{ __('Edit Weekday') }}</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body py-0">
                        <form action="{{ route('admin.attendance.settings.weekdays.update', $day->id) }}" method="POST"
                            id="update-weekday-form_{{ $day->id }}">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="name">{{ __('Name') }}<b class="text-danger">*</b></label>
                                        <input type="text" class="form-control" id="name" name="name"
                                            value="{{ $day->name }}">
                                    </div>
                                </div>


                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="is_weekend">{{ __('Is Weekend') }}</label>
                                        <select name="is_weekend" id="is_weekend" class="form-control">
                                            <option value="1" {{ $day->is_weekend ? 'selected' : '' }}>
                                                {{ __('Yes') }}</option>
                                            <option value="0" {{ !$day->is_weekend ? 'selected' : '' }}>
                                                {{ __('No') }}</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="status">{{ __('Status') }}</label>
                                        <select name="status" id="status" class="form-control">
                                            <option value="1" {{ $day->status ? 'selected' : '' }}>
                                                {{ __('Active') }}</option>
                                            <option value="0" {{ !$day->status ? 'selected' : '' }}>
                                                {{ __('Inactive') }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary"
                            form="update-weekday-form_{{ $day->id }}">Save</button>
                    </div>

                </div>
            </div>
        </div>
    @endforeach
@endsection
