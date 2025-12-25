@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Supplier Due Pay List') }}</title>
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
                                        <span class="input-group-text">{{ __('to') }}</span>
                                        <input type="text" placeholder="To Date" class="form-control datepicker"
                                            name="to_date" value="{{ request()->get('to_date') }}" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                            <div class="col-xxl-2 col-md-4">
                                <div class="form-group">
                                    <button type="button" class="btn bg-danger form-reset">{{ __('Reset') }}</button>
                                    <button type="submit" class="btn btn-primary">{{ __('Search') }}</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="card mt-5">
        <div class="card-header-tab card-header">
            <div class="card-header-title font-size-lg text-capitalize font-weight-normal">
                <h4 class="section_title"> {{ __('Supplier Due Pay List') }}</h4>
            </div>
            <div class="btn-actions-pane-right actions-icon-btn">
                <button type="button" class="btn btn-primary export"><i class="fa fa-file-excel"></i>
                    {{ __('Excel') }}</button>
                <button type="button" class="btn btn-success export-pdf"><i class="fa fa-file-pdf"></i>
                    {{ __('PDF') }}</button>
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
                            <th>{{ __('Supplier') }}</th>
                            <th>{{ __('Amount') }}</th>
                            <th>{{ __('Paid By') }}</th>
                            <th>{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($payments as $payment)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ formatDate($payment->payment_date) }}
                                </td>
                                <td>{{ $payment->purchase?->invoice_number }}</td>
                                <td>{{ $payment->supplier->name }}</td>
                                <td>{{ currency($payment->amount) }}</td>
                                <td>{{ $payment->createdBy->name }}</td>
                                <td>
                                    @adminCan('supplier.due.pay.delete')
                                        <div class="btn-group">
                                            <a href="javascript:;" class="btn btn-danger btn-sm"
                                                onclick="deleteData({{ $payment->id }})">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    @endadminCan
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
            let url = "{{ route('admin.supplier.due-receive.delete', ':id') }}"
            url = url.replace(':id', id);
            $("#deleteForm").attr("action", url);
            $('#deleteModal').modal('show');
        }
    </script>
@endpush
