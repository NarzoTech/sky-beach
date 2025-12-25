@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Products Sales Report') }}</title>
@endsection


@section('content')
    <div class="main-content">
        <section class="section">


            <div class="section-body">
                <div class="row">
                    {{-- Search filter --}}
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <form action="" method="GET" class="card-body">
                                    <div class="row">
                                        <div class="col-md-4 form-group search-wrapper">
                                            <input type="text" name="keyword" value="{{ request()->get('keyword') }}"
                                                class="form-control" placeholder="Search">
                                            <button type="submit">
                                                <i class="far fa-arrow-alt-circle-right"></i>
                                            </button>
                                        </div>
                                        <div class="col-md-2 form-group">
                                            <select name="order_by" id="order_by" class="form-control">
                                                <option value="">{{ __('Order By') }}</option>
                                                <option value="asc" {{ request('order_by') == 'asc' ? 'selected' : '' }}>
                                                    {{ __('ASC') }}
                                                </option>
                                                <option value="desc"
                                                    {{ request('order_by') == 'desc' ? 'selected' : '' }}>
                                                    {{ __('DESC') }}
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col-md-2 form-group">
                                            <select name="par-page" id="par-page" class="form-control">
                                                <option value="">{{ __('Per Page') }}</option>
                                                <option value="10" {{ '10' == request('par-page') ? 'selected' : '' }}>
                                                    {{ __('10') }}
                                                </option>
                                                <option value="50" {{ '50' == request('par-page') ? 'selected' : '' }}>
                                                    {{ __('50') }}
                                                </option>
                                                <option value="100"
                                                    {{ '100' == request('par-page') ? 'selected' : '' }}>
                                                    {{ __('100') }}
                                                </option>
                                                <option value="all"
                                                    {{ 'all' == request('par-page') ? 'selected' : '' }}>
                                                    {{ __('All') }}
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col-md-2 form-group">
                                            <input type="text" placeholder="From Date" name="from_date"
                                                value="{{ request()->get('from_date') }}" class="form-control datepicker"
                                                autocomplete="off">
                                        </div>
                                        <div class="col-md-2 form-group">
                                            <input type="text" placeholder="To Date" name="to_date"
                                                value="{{ request()->get('to_date') }}" class="form-control datepicker"
                                                autocomplete="off">
                                        </div>
                                    </div>
                                    {{-- excel  buttons --}}
                                    <div class="row">
                                        <div class="col-md-4 form-group mx-auto">
                                            <div class="btn-group" role="group" aria-label="Basic example">
                                                <button type="button" class="btn btn-secondary export"><i
                                                        class="far fa-file-excel"></i>
                                                    Excel</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>

                                </h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive table-invoice">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Sl') }}</th>
                                                <th>{{ __('Product Name') }}</th>
                                                <th>{{ __('Sku') }}</th>
                                                <th>{{ __('Brand Name') }}</th>
                                                <th>{{ __('Stock Qty') }}</th>
                                                <th>{{ __('Selling Qty') }}</th>
                                                <th>{{ __('Selling Price') }}</th>
                                                <th>{{ __('Purchase Price') }}</th>
                                                <th>{{ __('Profit/Loss') }}</th>
                                            </tr>

                                        </thead>
                                        <tbody>
                                            @foreach ($products as $product)
                                                @php
                                                    $sellQty = $product->sales['qty'] - $product->sales_return['qty'];

                                                    $sellingPrice =
                                                        $sellQty > 0 ? $product->sales['price'] / $sellQty : 0;
                                                @endphp
                                                <tr>
                                                    <td>{{ $products->firstItem() + $loop->iteration - 1 }}</td>
                                                    <td>{{ $product->name }}</td>
                                                    <td>{{ $product->sku }}</td>
                                                    <td>{{ $product->brand->name ?? 'N/A' }}</td>
                                                    <td>{{ $product->stock_count }}</td>
                                                    <td>{{ $sellQty }}</td>

                                                    <td>{{ $sellingPrice }}
                                                    </td>
                                                    <td>{{ $product->purchase_price }}</td>
                                                    <td>
                                                        {{ $sellQty * $sellingPrice - $sellQty * $product->purchase_price }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                            <tr>
                                                <td colspan="5" class="text-right">
                                                    Total
                                                </td>
                                                <td>
                                                    {{ $totalStock }}
                                                </td>
                                                <td>
                                                    {{ $sellCount }}
                                                </td>
                                                <td>
                                                    {{ $sellPrice }}
                                                </td>
                                                <td>
                                                    {{ $totalPurchasePrice }}
                                                </td>
                                                <td>
                                                    {{ $sellPrice - $totalPurchasePrice }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                @if (request()->get('par-page') !== 'all')
                                    <div class="float-right">
                                        {{ $products->onEachSide(0)->links() }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
