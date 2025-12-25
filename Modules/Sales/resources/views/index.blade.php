@extends('admin.layouts.master')
@section('title')
    <title>{{ $title }}</title>
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body pb-0">
                    <form class="search_form" action="" method="GET">
                        <div class="row">
                            <div class="col-xxl-3 col-md-4">
                                <div class="form-group search-wrapper">
                                    <input type="text" name="keyword" value="{{ request()->get('keyword') }}"
                                        class="form-control" placeholder="Search..." autocomplete="off">
                                    <button type="submit">
                                        <i class='bx bx-search'></i>
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
                <h4 class="section_title">{{ __('Sales List') }}</h4>
            </div>
            <div class="btn-actions-pane-right actions-icon-btn">
                @adminCan('sales.excel.download')
                    <button type="button" class="btn bg-label-success export"><i class="fa fa-file-excel"></i>
                        {{ __('Excel') }}</button>
                @endadminCan
                @adminCan('sales.pdf.download')
                    <button type="button" class="btn bg-label-warning export-pdf"><i class="fa fa-file-pdf"></i>
                        {{ __('PDF') }}</button>
                @endadminCan
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive list_table">
                <table style="width: 100%;" class="table mb-3">
                    <thead>
                        <tr>
                            <th>{{ __('SN') }}</th>
                            <th>{{ __('Date') }}</th>
                            <th>{{ __('Invoice No') }}</th>
                            <th>{{ __('Customer') }}</th>
                            <th>{{ __('Remark') }}</th>
                            <th>{{ __('Sale Amount') }}</th>
                            <th>{{ __('Total Amount') }}</th>
                            <th>{{ __('Paid Amount') }}</th>
                            <th>{{ __('Due') }}</th>
                            <th>{{ __('Payment Status') }}</th>
                            <th>{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sales as $key => $sale)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ formatDate($sale->order_date) }}</td>
                                <td>{{ $sale->invoice }}</td>
                                <td>{{ $sale?->customer?->name ?? 'Guest' }}</td>
                                <td>{{ $sale->sale_note }}</td>
                                <td>{{ $sale->total_price }}</td>
                                <td>{{ $sale->grand_total }}</td>
                                <td>{{ $sale->paid_amount }}</td>
                                <td>{{ $sale->due_amount }}</td>
                                <td>
                                    @if ((float)$sale->paid_amount >= (float)$sale->grand_total)
                                        <span class="badge bg-success">{{ __('Paid') }}</span>
                                    @elseif ((float)$sale->paid_amount == 0)
                                        <span class="badge bg-danger">{{ __('Due') }}</span>
                                    @else
                                        <span class="badge bg-warning">{{ __('Partial Due') }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if (checkAdminHasPermission('sales.delete') ||
                                            checkAdminHasPermission('sales.edit') ||
                                            checkAdminHasPermission('sales.view') ||
                                            checkAdminHasPermission('sales.invoice') ||
                                            checkAdminHasPermission('sales.return'))
                                        <div class="btn-group mb-2">
                                            <button class="btn btn-primary dropdown-toggle" type="button"
                                                data-bs-toggle="dropdown" aria-haspopup="true"
                                                aria-expanded="false">{{ __('Action') }}</button>
                                            <div class="dropdown-menu">
                                                @adminCan('sales.view')
                                                    <a class="dropdown-item view-sale" href="javascript:;"
                                                        data-id="{{ $sale->id }}">{{ __('View') }}</a>
                                                @endadminCan
                                                @adminCan('sales.invoice')
                                                    <a class="dropdown-item"
                                                        href="{{ route('admin.sales.invoice', $sale->id) }}">{{ __('Invoice') }}</a>
                                                @endadminCan
                                                @adminCan('sales.edit')
                                                    @if ($sale->saleReturns->count() == 0)
                                                        <a class="dropdown-item"
                                                            href="{{ route('admin.sales.edit', $sale->id) }}">{{ __('Edit') }}</a>
                                                    @endif
                                                @endadminCan
                                                @adminCan('sales.delete')
                                                    <a class="dropdown-item" href="javascript:void(0)"
                                                        onclick="deleteData({{ $sale->id }})">{{ __('Delete') }}</a>
                                                @endadminCan
                                                @adminCan('sales.return')
                                                    @if ($sale?->customer?->name)
                                                        <a class="dropdown-item"
                                                            href="{{ route('admin.sales.return.create', $sale->id) }}">{{ __('Sale Return') }}</a>
                                                    @endif
                                                @endadminCan
                                            </div>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="5" class="text-center">
                                <b> {{ __('Total') }}</b>
                            </td>
                            <td colspan="1">
                                <b>{{ currency($data['sale_amount']) }}</b>
                            </td>
                            <td colspan="1">
                                <b>{{ currency($data['total_amount']) }}</b>
                            </td>
                            <td colspan="1">
                                <b>{{ currency($data['paid_amount']) }}</b>
                            </td>
                            <td colspan="1">
                                <b>{{ currency($data['due_amount']) }}</b>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            @if (request()->get('par-page') !== 'all')
                <div class="float-right">
                    {{ $sales->onEachSide(0)->links() }}
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
            $('#deleteForm').attr('action', "{{ route('admin.sales.destroy', '') }}/" + id);
            modal.modal('show');
        }
    </script>
@endpush
