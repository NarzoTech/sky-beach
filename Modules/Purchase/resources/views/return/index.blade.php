@extends('admin.layouts.master')
@section('title', __('Purchases Return List'))

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body pb-0">
                    <form action="" method="GET" class="search_form">
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
                            <div class="col-lg-2 col-md-6">
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
                            <div class="col-lg-2 col-md-6">
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
                            <div class="col-xxl-2 col-md-6 col-lg-2">
                                <div class="form-group">
                                    <div class="input-group input-daterange" id="bs-datepicker-daterange">
                                        <input type="text" id="dateRangePicker" placeholder="From Date"
                                            class="form-control datepicker" name="from_date"
                                            value="{{ request('from_date') }}" autocomplete="off">
                                        <span class="input-group-text">to</span>
                                        <input type="text" placeholder="To Date" class="form-control datepicker"
                                            name="to_date" value="{{ request('to_date') }}" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6">
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
    <div class="card mt-5">
        <div class="card-header">
            <div class="section_title">{{ __('Purchases Return List') }}</div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>
                                        {{ __('SL') }}
                                    </th>
                                    <th>{{ __('Invoice') }}</th>
                                    <th>{{ __('Return Date') }}</th>
                                    <th>{{ __('Return Type') }}</th>
                                    <th>{{ __('Supplier') }}</th>
                                    <th>{{ __('Total Amount') }}</th>
                                    <th>{{ __('Total Received') }}</th>
                                    <th>{{ __('Return By') }}</th>
                                    <th>{{ __('Updated By') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($returns as $list)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $list->invoice }}</td>
                                        <td>{{ formatDate($list->return_date) }}</td>
                                        <td>{{ $list->returnType?->name }}</td>
                                        <td>{{ $list->purchase?->supplier?->name }}</td>
                                        <td>{{ currency($list->return_amount) }}</td>
                                        <td>{{ currency($list->received_amount) }}</td>
                                        <td>{{ $list->createdBy->name }}</td>
                                        <td>{{ $list->updatedBy->name }}</td>
                                        <td>
                                            @if (checkAdminHasPermission('purchase.return.edit') || checkAdminHasPermission('purchase.return.delete'))
                                                <div class="btn-group" role="group">
                                                    <button id="btnGroupDrop{{ $list->id }}" type="button"
                                                        class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown"
                                                        aria-haspopup="true" aria-expanded="false">
                                                        {{ __('Action') }}
                                                    </button>
                                                    <div class="dropdown-menu"
                                                        aria-labelledby="btnGroupDrop{{ $list->id }}">
                                                        @adminCan('purchase.return.edit')
                                                            <a class="dropdown-item"
                                                                href="{{ route('admin.purchase.return.edit', $list->id) }}">{{ __('Edit') }}</a>
                                                        @endadminCan
                                                        @adminCan('purchase.return.delete')
                                                            <a href="javascript:;" class="dropdown-item"
                                                                onclick="deleteData({{ $list->id }})">
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
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
    <script>
        function deleteData(id) {
            let url = "{{ route('admin.purchase.return.destroy', ':id') }}"
            url = url.replace(':id', id);
            $("#deleteForm").attr("action", url);
            $('#deleteModal').modal('show');
        }
    </script>
@endpush
