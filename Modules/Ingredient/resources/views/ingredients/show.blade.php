@extends('admin.layouts.master')
@section('title', __('Current Stock'))


@section('content')
    <div class="main-content">
        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header-tab card-header">
                                <div class="card-header-title font-size-lg text-capitalize font-weight-normal">
                                    <h4 class="section_title">{{ __('Product Details') }}</h4>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 col-lg-3 col-xxl-3">
                                        <img src="{{ asset($product->single_image) }}" class="product_det_img"
                                            alt="Product Picture" width="100">
                                    </div>
                                    <div class="col-md-8 col-lg-9 col-xxl-9">
                                        <div class="table-responsive">
                                            <table class="table">
                                                <tr>
                                                    <th style="width: 35%"><b>{{ __('Name') }}</b></th>
                                                    <td>{{ $product->name }}</td>
                                                </tr>
                                                <tr>
                                                    <th><b>{{ __('Category') }}</b></th>
                                                    <td>{{ $product->category->name }}</td>
                                                </tr>
                                                <tr>
                                                    <th><b>{{ __('Brand') }}</b></th>
                                                    <td>{{ $product->brand->name }}</td>
                                                </tr>
                                                <tr>
                                                    <th><b>{{ __('Unit') }}</b></th>
                                                    <td>{{ $product->unit->name }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="table-responsive mt-5">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th><b>{{ __('Barcode') }}</b></th>
                                                        <th><b>{{ __('Purchase Price') }}</b></th>
                                                        <th><b>{{ __('Selling Price') }}</b></th>
                                                        <th><b>{{ __('Qty') }}</b></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>{{ $product->barcode }}</td>
                                                        <td>{{ currency($product->cost) }}</td>
                                                        <td>{{ currency($product->selling_price) }}</td>
                                                        <td>{{ $product->stock }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </section>
    </div>
@endsection
