@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Purchase Report') }}</title>
@endsection


@section('content')
    <div class="card">
        <div class="card-body pb-0">
            <form class="search_form" action="" method="GET">
                <div class="row">
                    <div class="col-xxl-3 col-md-4">
                        <div class="form-group search-wrapper">
                            <input type="text" name="keyword" value="{{ request()->get('keyword') }}" class="form-control"
                                placeholder="Search">
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
                    <div class="col-xxl-3 col-md-4">
                        <div class="form-group">
                            <div class="input-group input-daterange" id="bs-datepicker-daterange">
                                <input type="text" id="dateRangePicker" placeholder="From Date"
                                    class="form-control datepicker" name="from_date"
                                    value="{{ request()->get('from_date') }}" autocomplete="off">
                                <span class="input-group-text">to</span>
                                <input type="text" placeholder="To Date" class="form-control datepicker" name="to_date"
                                    value="{{ request()->get('to_date') }}" autocomplete="off">
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-2 col-md-4">
                        <div class="form-group">
                            <button type="button" class="btn bg-danger form-reset">{{ __('Reset') }}</button>
                            <button type="submit" class="btn bg-label-primary">{{ __('Search') }}</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="card mt-5">
        <div class="card-header">
            <h4 class="section_title">{{ __('Purchase Report') }}</h4>
            <div class="btn-actions-pane-right actions-icon-btn">
                <button type="button" class="btn bg-label-success export"><i class="fa fa-file-excel"></i>
                    Excel</button>
                <button type="button" class="btn bg-label-warning export-pdf"><i class="fa fa-file-pdf"></i>
                    PDF</button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive table-invoice">
                <table class="table">
                    <thead>
                        <tr>
                            <th>{{ __('Sl') }}</th>
                            <th>{{ __('Date') }}</th>
                            <th>{{ __('Invoice') }}</th>
                            <th>{{ __('Supplier') }}</th>
                            <th>{{ __('Purchased By') }}</th>
                            {{-- <th>{{ __('Product (Qty)') }}</th> --}}
                            <th>{{ __('Invoice Qty') }}</th>
                            <th>{{ __('Total') }}</th>
                            <th>{{ __('Paid') }}</th>
                            <th>{{ __('Due') }}</th>
                            <th>{{ __('Payment Status') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $start = checkPaginate($purchases) ? $purchases->firstItem() : 1;
                        @endphp
                        @foreach ($purchases as $index => $purchase)
                            <tr>
                                <td>
                                    {{ $start + $index }}
                                </td>
                                <td>
                                    {{ formatDate($purchase->purchase_date) }}
                                </td>
                                <td>
                                    {{ $purchase->invoice_number }}
                                </td>
                                <td>
                                    {{ $purchase->supplier->name ?? 'Guest' }}
                                </td>
                                <td>
                                    {{ $purchase->createdBy->name }}
                                </td>
                                {{-- <td>
                                    <table>
                                        @foreach ($purchase->purchaseDetails as $key => $purchaseDetail)
                                            <tr>
                                                <td>{{ $purchaseDetail->product->name }}</td>
                                                <td>{{ $purchaseDetail->quantity }}</td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </td> --}}
                                <td>
                                    {{ $purchase->purchaseDetails->sum('quantity') }}
                                </td>
                                <td>
                                    {{ currency($purchase->total_amount) }}
                                </td>
                                <td>
                                    {{ currency($purchase->paid_amount) }}
                                </td>
                                <td>
                                    {{ currency($purchase->due_amount) }}
                                </td>
                                <td>
                                    <span class="badge {{ $purchase->due_amount == 0 ? 'bg-success' : 'bg-danger' }}">
                                        {{ $purchase->due_amount == 0 ? 'Paid' : 'Due' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="6" class="text-center">
                                <b> {{ __('Total') }}</b>
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
                    {{ $purchases->onEachSide(0)->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
