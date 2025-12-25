@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Customer Due Receive List') }}</title>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body pb-0">
                    <form class="search_form " action="" method="GET">
                        <div class="row">
                            <div class="col-xxl-3 col-md-4">
                                <div class="form-group search-wrapper">
                                    <input type="text" name="keyword" value="{{ request()->get('keyword') }}"
                                        class="form-control" placeholder="Search...">
                                    <button type="submit">
                                        <i class="bx bx-search"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-xxl-2 col-md-4">
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
                            <div class="col-xxl-2 col-md-4">
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
                            <div class="col-xxl-3 col-md-4">
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
                            <div class="col-xxl-2 col-md-4">
                                <div class="form-group">
                                    <button type="button" class="btn bg-danger form-reset">Reset</button>
                                    <button type="submit" class="btn btn-primary">{{ __('Search') }}</button>
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
                <h4 class="section_title"> {{ __('Customer Due Receive List') }}</h4>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table style="width: 100%;" class="table common_table">
                    <thead>
                        <tr>
                            <th>
                                {{ __('SL') }}
                            </th>
                            <th>{{ __('Date') }}</th>
                            <th>{{ __('Invoice No') }}</th>
                            <th>{{ __('Customer') }}</th>
                            <th>{{ __('Amount') }}</th>
                            <th>{{ __('Receive By') }}</th>
                            <th>{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach ($payments as $payment)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ formatDate($payment->payment_date) }}
                                </td>
                                <td>{{ $payment->sale?->invoice }}</td>
                                <td>{{ $payment->customer->name }}</td>
                                <td>{{ $payment->amount }}</td>
                                <td>{{ $payment->createdBy->name }}</td>
                                <td>
                                    <div class="btn-group">
                                        <a href="javascript:;" class="btn btn-danger btn-sm"
                                            onclick="deleteData({{ $payment->id }})">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        @if ($payments->count() > 0)
                            <tr>
                                <td colspan="4" class="text-center fw-bold">
                                    {{ __('Total') }}
                                </td>
                                <td colspan="3" class="fw-bold">
                                    {{ currency($data['total']) }}
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            @if (request()->get('par-page') !== 'all')
                <div class="float-right">
                    {{ $payments->onEachSide(0)->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
@push('js')
    <script>
        function deleteData(id) {
            let url = "{{ route('admin.customer.due-receive.delete', ':id') }}"
            url = url.replace(':id', id);
            $("#deleteForm").attr("action", url);
            $('#deleteModal').modal('show');
        }
    </script>
@endpush
