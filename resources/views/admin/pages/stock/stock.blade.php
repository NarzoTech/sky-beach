@extends('admin.layouts.master')
@section('title', __('Stock List'))


@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body pb-0">
                    <form class="search_form" action="" method="GET">
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
                            <div class="col-lg-3 col-md-6">
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
                            <div class="col-lg-3 col-md-6">
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
                            <div class="col-lg-3 col-md-6">
                                <div class="form-group">
                                    <select name="brand_id" id="brand_id" class="form-control select2">
                                        <option value="" selected disabled>{{ __('Brand') }}</option>
                                        @foreach ($brands as $brand)
                                            <option value="{{ $brand->id }}"
                                                {{ request('brand_id') == $brand->id ? 'selected' : '' }}>
                                                {{ $brand->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <div class="form-group">
                                    <select name="category_id" id="categories" class="form-control select2">
                                        <option value="" selected disabled>{{ __('Categories') }}
                                        </option>
                                        @foreach ($categories as $cat)
                                            <option value="{{ $cat->id }}"
                                                {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                                {{ $cat->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <div class="form-group">
                                    <select name="stock_status" id="stock_status" class="form-control select2">
                                        <option value="">{{ __('All') }}</option>
                                        <option value="in_stock"
                                            {{ request('stock_status') == 'in_stock' ? 'selected' : '' }}>
                                            {{ __('In Stock') }}</option>
                                        <option value="out_of_stock"
                                            {{ request('stock_status') == 'out_of_stock' ? 'selected' : '' }}>
                                            {{ __('Stock Out') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <div class="form-group">
                                    <div class="input-group input-daterange" id="bs-datepicker-daterange">
                                        <input type="text" id="dateRangePicker" placeholder="From Date"
                                            class="form-control datepicker" name="from_date" value=""
                                            autocomplete="off">
                                        <span class="input-group-text">to</span>
                                        <input type="text" placeholder="To Date" class="form-control datepicker"
                                            name="to_date" value="" autocomplete="off">
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
            <div class="card-header-title font-size-lg text-capitalize font-weight-normal">
                <h4 class="section_title">{{ __('Stock List') }}</h4>
            </div>
            <div class="btn-actions-pane-right actions-icon-btn">
                @adminCan('stock.reset')
                    <a href="javascript:;" class="btn btn-danger reset-button">{{ __('Reset Stock') }}</a>
                @endadminCan
                @adminCan('stock.excel.download')
                    <button type="button" class="btn bg-label-success export"><i class="fa fa-file-excel"></i>
                        {{ __('Excel') }}</button>
                @endadminCan
                @adminCan('stock.pdf.download')
                    <button type="button" class="btn bg-label-warning export-pdf"><i class="fa fa-file-pdf"></i>
                        {{ __('PDF') }}</button>
                @endadminCan
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive list_table stock_table">
                <table style="width: 100%;" class="table">
                    <thead>
                        <tr>
                            <th>{{ __('Sl') }}</th>
                            <th>{{ __('Picture') }}</th>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Avg P.P') }}</th>
                            <th>{{ __('L. P.P') }}</th>
                            <th>{{ __('In Quantity') }}</th>
                            <th>{{ __('Out Quantity') }}</th>
                            <th>{{ __('Stock') }}</th>
                            <th>{{ __('Stock P.P') }}</th>
                            <th>{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($ingredients as $index => $ingredient)
                            @php
                                $stock = $ingredient->stock < 0 ? 0 : $ingredient->stock;
                            @endphp
                            <tr>
                                <td>{{ $ingredients->firstItem() + $index }}</td>
                                <td>
                                    <img src="{{ asset($ingredient->single_image) }}" alt="Product Picture" width="100">
                                </td>
                                <td>{{ $ingredient->name }}</td>
                                <td>{{ $ingredient->avg_purchase_price }}</td>
                                <td>{{ $ingredient->last_purchase_price }}</td>
                                <td>{{ round($ingredient->stockDetails->sum('base_in_quantity'), 4) }} {{ $ingredient->purchaseUnit->ShortName ?? '' }}</td>
                                <td>{{ round($ingredient->stockDetails->sum('base_out_quantity'), 4) }} {{ $ingredient->purchaseUnit->ShortName ?? '' }}</td>
                                <td>{{ $ingredient->stock }} {{ $ingredient->purchaseUnit->ShortName ?? '' }}</td>
                                <td>{{ remove_comma($stock) * remove_comma($ingredient->avg_purchase_price) }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button id="btnGroupDrop{{ $ingredient->id }}" type="button"
                                            class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                            {{ __('Action') }}
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="btnGroupDrop{{ $ingredient->id }}">

                                            @adminCan('ingredient.view')
                                                <a href="{{ route('admin.ingredient.show', $ingredient->id) }}"
                                                    class="dropdown-item"
                                                    title="Product Details">{{ __('Product Details') }}</a>
                                            @endadminCan
                                            @adminCan('stock.ledger')
                                                <a href="{{ route('admin.stock.ledger', $ingredient->id) }}"
                                                    class="dropdown-item" title="Stock Ledger">
                                                    {{ __('Stock Ledger') }}
                                                </a>
                                            @endadminCan
                                            @adminCan('stock.reset')
                                                <a href="javascript:;" class="dropdown-item" title="Reset Stock"
                                                    onclick="resetStock({{ $ingredient->id }})" data-bs-target="#stockModal"
                                                    data-bs-toggle="modal">
                                                    {{ __('Reset Stock') }}
                                                </a>
                                            @endadminCan
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach

                        <tr>
                            <td colspan="5" class="text-center">
                                <b>{{ __('Total') }}</b>
                            </td>
                            <td><b>{{ $totals['totalInQty'] }}</b></td>
                            <td><b>{{ $totals['totalOutQty'] }}</b></td>
                            <td><b>{{ $totals['totalStock'] }}</b></td>
                            <td><b>{{ $totals['totalStockPP'] }}</b></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            @if (request()->get('par-page') !== 'all')
                <div class="float-right">
                    {{ $ingredients->onEachSide(0)->links() }}
                </div>
            @endif
        </div>
    </div>

    <div class="modal fade" tabindex="-1" role="dialog" id="stockModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="section_title">{{ __('Stock Reset Confirmation') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body py-0">
                    <p>{{ __('Are You sure want to Reset Stock') }}?</p>
                </div>
                <div class="modal-footer bg-whitesmoke br">
                    <form id="resetForm" action="" method="POST">
                        @csrf
                        @method('PUT')
                        <button type="button" class="btn btn-danger"
                            data-bs-dismiss="modal">{{ __('Close') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Yes, Reset') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('js')
    <script>
        $(document).ready(function() {
            'use strict';
            $('.export').on('click', function() {
                // get full url including query string
                var fullUrl = window.location.href;
                if (fullUrl.includes('?')) {
                    fullUrl += '&export=true';
                } else {
                    fullUrl += '?export=true';
                }

                window.location.href = fullUrl;
            })

            $('.reset-button').on('click', function() {
                $('#resetForm').attr('action', "{{ route('admin.stock.reset.all') }}");

                $('#stockModal').modal('show');
            })
        });

        function resetStock(id) {
            $('#resetForm').attr('action', "{{ route('admin.stock.reset', ':id') }}".replace(':id', id));
        }
    </script>
@endpush
