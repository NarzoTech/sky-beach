@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Sales Return List') }}</title>
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body pb-0">
                    <form class="search_form" action="" method="GET">
                        <div class="row">
                            <div class="col-xxl-3 col-md-6">
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
                            <div class="col-xxl-3 col-md-6">
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
                            <div class="col-xxl-2 col-md-6">
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
            <div class="card-header-title font-size-lg text-capitalize font-weight-normal">
                <h4 class="section_title"> {{ __('Sales Return List') }}</h4>
            </div>
            <div class="btn-actions-pane-right actions-icon-btn">
                @adminCan('sales.return.excel.download')
                    <button type="button" class="btn bg-label-success export"><i class="fa fa-file-excel"></i>
                        {{ __('Excel') }}</button>
                @endadminCan
                @adminCan('sales.return.pdf.download')
                    <button type="button" class="btn bg-label-warning export-pdf"><i class="fa fa-file-pdf"></i>
                        {{ __('PDF') }}</button>
                @endadminCan
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive list_table">
                <table style="width: 100%;" class="table common_table">
                    <thead>
                        <tr>
                            <th>{{ __('Sl') }}</th>
                            <th>{{ __('Date') }}</th>
                            <th>{{ __('Invoice No') }}</th>
                            <th>{{ __('Customer') }}</th>
                            <th>{{ __('Total Amount') }}</th>
                            <th>{{ __('Paying Amount') }}</th>
                            <th>{{ __('Payment Status') }}</th>
                            <th>{{ __('Due') }}</th>
                            <th>{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($lists as $key => $sale)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ formatDate($sale->return_date) }}</td>
                                <td>{{ $sale->invoice }}</td>
                                <td>{{ $sale?->customer?->name ?? 'Guest' }}</td>
                                <td>{{ $sale->return_amount }}</td>
                                <td>{{ $sale->return_amount - $sale->return_due }}</td>
                                <td>
                                    @if (!$sale->return_due)
                                        <span class="badge bg-success">{{ __('Paid') }}</span>
                                    @else
                                        <span class="badge bg-danger">{{ __('Due') }}</span>
                                    @endif
                                </td>
                                <td>{{ $sale->return_due }}</td>
                                <td>
                                    @adminCan('sales.return.delete')
                                        <div class="btn-group mb-2">
                                            <a class="btn bg-label-danger" href="javascript:void(0)"
                                                onclick="deleteData({{ $sale->id }})">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    @endadminCan
                                </td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="4" class="text-center fw-bold">
                                {{ __('Total') }}
                            </td>
                            <td class="fw-bold">
                                {{ currency($data['totalAmount']) }}
                            </td>
                            <td class="fw-bold">
                                {{ currency($data['paidAmount']) }}
                            </td>
                            <td class="fw-bold"></td>
                            <td colspan="1" class="fw-bold">
                                {{ currency($data['totalDue']) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            @if (request()->get('par-page') !== 'all')
                <div class="float-right">
                    {{ $lists->onEachSide(0)->links() }}
                </div>
            @endif
        </div>
    </div>
    @include('components.admin.preloader')

    <div class="modal fade bd-example-modal-xl" id="salemodal" tabindex="-1" role="dialog"
        aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="width: 100%">
            <div class="modal-content" id="modalcontent" style="width: 100%">

            </div>
        </div>
    </div>
@endsection
@push('js')
    <script>
        'use strict'

        $(document).ready(function() {
            $(document).on('click', '.view-sale', function() {
                var id = $(this).data('id');
                $.ajax({
                    type: "GET",
                    url: "{{ route('admin.sales.show', '') }}/" + id,
                    success: function(data) {
                        $('#modalcontent').html(data);
                        $('#salemodal').modal('show');
                    }
                });
            })
        })

        function deleteData(id) {
            const modal = $('#deleteModal');
            $('#deleteForm').attr('action', "{{ route('admin.sales.return.destroy', '') }}/" + id);
            modal.modal('show');
        }
    </script>
@endpush
