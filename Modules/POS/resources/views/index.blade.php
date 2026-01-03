@extends('admin.layouts.master')
@section('title')
    <title>
        {{ __('POS') }}</title>
@endsection
@push('css')
    <link rel="stylesheet" href="{{ asset('backend/css/pos.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/css/jquery-ui.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/backend/css/invoice.css') }}">
    <style>
        .ui-autocomplete {
            z-index: 215000000 !important;
        }
    </style>
@endpush
@section('content')

    <!-- Main Content -->
    <div class="main-content">
        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-lg-5">
                        <div class="row">
                            <div class="col-md-12">
                                <ul class="nav nav-tabs pos_tabs" id="myTab" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="products-tab" data-bs-toggle="tab"
                                            data-bs-target="#products" type="button" role="tab"
                                            aria-controls="products" aria-selected="true">{{ __('Products') }}</button>
                                    </li>

                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="favoriteProducts-tab" data-bs-toggle="tab"
                                            data-bs-target="#favoriteProducts" type="button" role="tab"
                                            aria-controls="profile"
                                            aria-selected="false">{{ __('Favorite Products') }}</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="service-tab" data-bs-toggle="tab"
                                            data-bs-target="#service" type="button" role="tab" aria-controls="profile"
                                            aria-selected="false">{{ __('Service') }}</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="favoriteService-tab" data-bs-toggle="tab"
                                            data-bs-target="#favoriteService" type="button" role="tab"
                                            aria-controls="profile"
                                            aria-selected="false">{{ __('Favorite Service') }}</button>
                                    </li>

                                </ul>
                            </div>
                        </div>
                        <div class="tab-content p-0 mt-3" id="myTabContent">
                            <div class="tab-pane fade show active" id="products" role="tabpanel"
                                aria-labelledby="products-tab">
                                <div class="card">
                                    <div class="card-header">
                                        <form id="product_search_form" class="pos_pro_search_form w-100">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="form-group mb-2">
                                                        <input type="text" class="form-control" name="name"
                                                            id="name"
                                                            placeholder="{{ __('Enter Product name / SKU / Scan bar code') }}"
                                                            autocomplete="off" value="{{ request()->get('name') }}">
                                                        <ul class="dropdown-menu" id="itemList">
                                                        </ul>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="form-group mb-2">
                                                        <select name="category_id" id="category_id"
                                                            class="form-control select2">
                                                            <option value="">{{ __('Select Category') }}</option>
                                                            @foreach ($categories as $category)
                                                                <option value="{{ $category->id }}">
                                                                    {{ $category->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="form-group mb-2">
                                                        <select name="brand_id" id="brand_id"
                                                            class="form-control select2">
                                                            <option value="">{{ __('Select Brand') }}</option>
                                                            @foreach ($brands as $brand)
                                                                <option value="{{ $brand->id }}">
                                                                    {{ $brand->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="card-body product_body">

                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="favoriteProducts" role="tabpanel"
                                aria-labelledby="favoriteProducts-tab">
                                <div class="card">
                                    <div class="card-header">
                                        <form id="favorite_product_search_form" class="pos_pro_search_form w-100">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="form-group mb-2">
                                                        <input type="text" class="form-control" name="name"
                                                            id="favoriteName"
                                                            placeholder="{{ __('Enter Product name / SKU / Scan bar code') }}"
                                                            autocomplete="off" value="{{ request()->get('name') }}">
                                                        <ul class="dropdown-menu" id="favoriteItemList">

                                                        </ul>
                                                    </div>
                                                </div>
                                                {{-- <div class="col-md-4 col-lg-4 col-sm-6">
                                                    <div class="form-group mb-2">
                                                        <select name="category_id" id="category_id"
                                                            class="form-control select2">
                                                            <option value="">{{ __('Select Category') }}</option>
                                                            @if (request()->has('category_id'))
                                                                @foreach ($categories as $category)
                                                                    <option
                                                                        {{ request()->get('category_id') == $category->id ? 'selected' : '' }}
                                                                        value="{{ $category->id }}">{{ $category->name }}
                                                                    </option>
                                                                @endforeach
                                                            @else
                                                                @foreach ($categories as $category)
                                                                    <option value="{{ $category->id }}">
                                                                        {{ $category->name }}
                                                                    </option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                    </div>
                                                </div> --}}
                                                {{-- <div class="col-md-4 col-lg-4 col-sm-6">
                                                    <div class="form-group mb-2">
                                                        <select name="brand_id" id="brand_id"
                                                            class="form-control select2">
                                                            <option value="">{{ __('Select brand') }}</option>
                                                            @if (request()->has('brand_id'))
                                                                @foreach ($brands as $brand)
                                                                    <option
                                                                        {{ request()->get('brand_id') == $brand->id ? 'selected' : '' }}
                                                                        value="{{ $brand->id }}">{{ $brand->name }}
                                                                    </option>
                                                                @endforeach
                                                            @else
                                                                @foreach ($categories as $brand)
                                                                    <option value="{{ $brand->id }}">
                                                                        {{ $brand->name }}
                                                                    </option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                    </div>
                                                </div> --}}
                                            </div>
                                        </form>
                                    </div>
                                    <div class="card-body product_body" style="overflow: auto">

                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="service" role="tabpanel" aria-labelledby="service-tab">
                                <div class="card">
                                    <div class="card-header">
                                        <form id="service_search_form" class="pos_pro_search_form w-100">
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <div class="form-group mb-2">
                                                        <input type="text" class="form-control" name="name"
                                                            id="service_name"
                                                            placeholder="{{ __('Enter Service name') }}"
                                                            autocomplete="off" value="{{ request()->get('name') }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group mb-2">
                                                        <select name="service_category_id" id="service_category_id"
                                                            class="form-control select2">
                                                            <option value="">{{ __('Select Category') }}</option>
                                                            @if (request()->has('service_category_id'))
                                                                @foreach ($serviceCategories as $category)
                                                                    <option
                                                                        {{ request()->get('service_category_id') == $category->id ? 'selected' : '' }}
                                                                        value="{{ $category->id }}">{{ $category->name }}
                                                                    </option>
                                                                @endforeach
                                                            @else
                                                                @foreach ($serviceCategories as $category)
                                                                    <option value="{{ $category->id }}">
                                                                        {{ $category->name }}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="card-body service_body" style="overflow: auto">

                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="favoriteService" role="tabpanel"
                                aria-labelledby="favoriteService-tab">
                                <div class="card">
                                    <div class="card-header">
                                        <form id="favorite_service_search_form" class="pos_pro_search_form w-100">
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <div class="form-group mb-2">
                                                        <input type="text" class="form-control" name="name"
                                                            id="favorite_service_name"
                                                            placeholder="{{ __('Enter Service name') }}"
                                                            autocomplete="off" value="{{ request()->get('name') }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group mb-2">
                                                        <select name="favorite_service_category_id"
                                                            id="favorite_service_category_id"
                                                            class="form-control select2">
                                                            <option value="">{{ __('Select Category') }}</option>
                                                            @if (request()->has('favorite_service_category_id'))
                                                                @foreach ($serviceCategories as $category)
                                                                    <option
                                                                        {{ request()->get('service_category_id') == $category->id ? 'selected' : '' }}
                                                                        value="{{ $category->id }}">{{ $category->name }}
                                                                    </option>
                                                                @endforeach
                                                            @else
                                                                @foreach ($serviceCategories as $category)
                                                                    <option value="{{ $category->id }}">
                                                                        {{ $category->name }}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="card-body favorite_service_body" style="overflow: auto">

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-7">
                        <div class="card">
                            <div class="card-header pos_sidebar_button">
                                <div class="row w-100">
                                    <div class="col-md-9 col-lg-10">
                                        <div class="form-group mb-2">
                                            <select name="customer_id" id="customer_id" class="form-control select2">
                                                @include('pos::customer-drop-down')
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-lg-2 pe-0">
                                        <div class="form-group mb-0">
                                            <button type="button" class="btn btn-primary w-100 addCustomer">
                                                <i class="fa fa-plus" aria-hidden="true"></i>
                                                {{ __('New') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body pos_pro_table">
                                <div class="row">
                                    @php
                                        $cumalitive_sub_total = 0;
                                    @endphp
                                    <div class="col-md-12 product-table-container">
                                        @include('pos::ajax_cart')
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table id="totalTable" class="summary-table">
                                        <tbody>
                                            <tr>
                                                <td>Items</td>
                                                <td><span id="titems">{{ count($cart_contents) }}</span> </td>
                                                <td class="custom_width">Total</td>
                                                <td> <span id="total">{{ currency($cumalitive_sub_total) }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td> Extra <small class="text-info"></small> </td>
                                                <td> <span id="extra">{{ currency(0) }}</span> </td>
                                                <td class="custom_width">{{ __('Discount') }}
                                                    <i class="fa fa-edit dis-tgl" style="cursor: pointer;"></i>
                                                    <div class="dis-form">
                                                        <select name="discount_type" id="discount_type"
                                                            onchange="discountExist()">
                                                            <option value="1" selected>{{ __('Amount') }} (TK )
                                                            </option>
                                                            <option value="2">{{ __('Percentage') }} (%)</option>
                                                        </select>
                                                        <input type="number" onchange="discountExist()"
                                                            id="discount_total_amount" value="0" step="0.1"
                                                            name="discount_total_amount" autocomplete="off" autofocus>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span id="tds">0</span>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td colspan="3"> After Discount Price </td>
                                                <td>
                                                    <span id="gtotal">{{ currency($cumalitive_sub_total) }}</span>
                                                    <input type="hidden" value="0" id="business_vat">
                                                </td>
                                            </tr>

                                            <tr>
                                                <td colspan="3"> Total Vat </td>
                                                <td>
                                                    <span id="totalVat">0</span>
                                                    <input type="hidden" value="0" id="business_vat">
                                                </td>
                                            </tr>
                                            <tr class="pay-row">
                                                <td colspan="3">
                                                    Total Payable
                                                    <span id="payable_amount"></span>
                                                </td>
                                                <td id="totalAmountWithVat">
                                                    {{ currency($cumalitive_sub_total) }}
                                                </td>
                                            </tr>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <footer class="pos-footer" style="z-index: 9999">
        <div>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-block back-btn">
                <i class="fa fa-backward fa-lg mt-3"></i>
            </a>
        </div>
        <h3 class="final-text">
            Total : <span id="finalTotal"> {{ currency($cumalitive_sub_total) }} </span>
        </h3>
        <div class="btn-group lg-btns">
            <div class="btn-group">
                <button type="button" class="btn hold-list-btn" title="Hold Sale List">
                    <i class="fa fa-list" aria-hidden="true"></i>
                </button>
                <button type="button" class="btn hold-btn" title="Hold Current Sale">
                    Hold
                </button>
            </div>
            <button type="button" class="btn cancel-btn" onclick="resetCart()">
                Clear
            </button>
            <button type="button" class="btn payment-btn" onclick="openPaymentModal()">
                Payment
            </button>
        </div>
    </footer>
    </div>

    @include('components.admin.preloader')

    <!-- Product Modal -->
    <div class="modal fade" id="cartModal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
        aria-hidden="true">
        <div class="modal-dialog mw-100 w-75" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid load_product_modal_response">

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create new user modal -->
    @include('customer::customer-modal')

    {{-- item details modal --}}
    <div class="modal fade" id="itemDetailsModal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content load_item_details_modal_response">

            </div>
        </div>
    </div>

    <div class="modal fade bd-example-modal-lg" id="payment-modal" role="dialog" aria-labelledby="myLargeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <form method="POST" action="" id="checkoutForm" onSubmit="paymentSubmit(event)">
                    @csrf
                    <input type="hidden" name="order_customer_id" id="order_customer_id" value="">
                    <div class="row">
                        <!-- Left Column -->
                        <div class="col-md-4">
                            <div class="table-responsive">
                                <table class="table table-bordered table-condensed payment_det_table">
                                    <tbody>
                                        <tr>
                                            <td colspan="3" class="text-center">
                                                <h5 class="m-0">Payment Details</h5>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th class="text-right w-60" colspan="2">
                                                Subtotal
                                            </th>
                                            <input type="hidden" name="sub_total" value="" autocomplete="off">
                                            <td class="text-right w-40" id="sub_totalModal">0</td>
                                        </tr>

                                        <tr class="discount-row">
                                            <th class="text-right w-60" colspan="2">
                                                Discount
                                            </th>
                                            <input type="hidden" name="discount_amount" value="0"
                                                autocomplete="off">
                                            <td class="text-right w-40" id="discount_amountModal">0.00</td>
                                        </tr>
                                        <tr>
                                            <th class="text-right w-60" colspan="2">
                                                Total Amount <br><small class="ng-binding">(<span id="itemModal">0</span>
                                                    items)</small>
                                            </th>
                                            <input type="hidden" name="total_amount" value="0"
                                                id="total_amount_modal_input" autocomplete="off">
                                            <td class="text-right w-40" id="total_amountModal">0.00</td>
                                        </tr>
                                        <tr>
                                            <th class="text-right w-60" colspan="2">
                                                Paid Amount</th>
                                            <td class="text-right w-40" id="paid_amountModal">0</td>
                                        </tr>

                                        <tr class="due d-none">
                                            <th class="text-right w-60" colspan="2">
                                                <label>Previous Due</label>
                                            </th>
                                            <td class="text-right w-40" id="previous_due" data-amount="0">0</td>
                                        </tr>

                                        <tr class="due d-none">
                                            <th class="text-right w-60" colspan="2">
                                                Total Due
                                            </th>
                                            <td class="text-right w-40" id="due_amountModal">0</td>
                                        </tr>
                                        <tr>
                                            <th class="text-right w-60" colspan="2">
                                                Sale Date
                                            </th>
                                            <td class="text-right w-40">
                                                <div class="form-group mb-0">
                                                    <input type="text" class="form-control" name="sale_date"
                                                        value="{{ formatDate(now()) }}" autocomplete="off">
                                                </div>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-12">
                                    <button type="button" class="btn btn-block btn-secondary w-100">
                                        Total Amount:
                                        <span class="text-warning mx-2 fs-3 fw-bold" id="total_amountModal2">0</span>
                                        TK
                                    </button>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table payment payment_det_table_2 mt-2">
                                    <thead>
                                        <tr>
                                            <td style="vertical-align: middle; width: 30%; text-transform: capitalize">
                                                Payment Type
                                            </td>
                                            <td style="vertical-align: middle; width: 30%; text-transform: capitalize">
                                                Payment Option
                                            </td>
                                            <td style="vertical-align: middle; width: 30%; text-transform: capitalize">
                                                Amount Received
                                            </td>
                                            <td style="vertical-align: middle; width: 10%; text-transform: capitalize">
                                                Action
                                            </td>
                                        </tr>
                                    </thead>

                                    <tbody id="paymentRow">
                                        @include('pos::payment-row')
                                    </tbody>

                                    <tfoot id="normalPayment">
                                        <tr class="due d-none">
                                            <td class="pl-2" style="vertical-align: middle">
                                                <label>Due</label>
                                            </td>
                                            <td colspan="3">
                                                <input type="text" class="form-control" name="total_due" readonly>
                                            </td>
                                        </tr>
                                        <tr class="due-date d-none">
                                            <td class="pl-2" style="vertical-align: middle">
                                                <label>Due Date</label>
                                            </td>
                                            <td colspan="3">
                                                <input type="date" class="form-control" name="due_date"
                                                    id="flatpickr-date">
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="pl-2" style="vertical-align: middle">
                                                <label>Receive Cash</label>
                                            </td>
                                            <td colspan="3">
                                                <div class="form-group mb-0">
                                                    <input type="number" class="form-control receive_cash removeZero"
                                                        name="receive_amount" value="0" step="0.01">
                                                </div>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="pl-2" style="vertical-align: middle">
                                                <label>Change</label>
                                            </td>
                                            <td colspan="3">
                                                <div class="form-group mb-0">
                                                    <input type="text" class="form-control change_amount removeZero"
                                                        name="return_amount" value="0" readonly>
                                                </div>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="pl-2" style="vertical-align: middle">
                                                <label>Remark</label>
                                            </td>
                                            <td colspan="3">
                                                <div class="form-group mb-0">
                                                    <input type="text" class="form-control" name="remark"
                                                        value="" autocomplete="off" placeholder="Remark">
                                                </div>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <div class="row">
                                <div class="col-12 text-end">
                                    <button type="button" class="btn btn-danger"
                                        onclick="modalHide('#payment-modal')">Cancel [Esc]</button>
                                    <button type="submit" id="checkout" class="btn btn-primary"> Checkout </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- hold modal --}}
    <div class="modal fade" id="hold-modal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content hold-modal">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    <h4 class="section_title">Hold Sale</h4>
                </div>
                <div class="modal-body pt-0">
                    <form action="javascript:;" id="hold-sale-form" method="post">
                        @csrf
                        <div class="form-group">
                            <label for="">Note</label>
                            <input type="text" class="form-control hold-sale-note" name="note">
                            <input type="hidden" class="form-control" name="user_id">
                        </div>
                        <div class="text-end mt-1">
                            <button class="btn bg-label-primary" type="submit">Hold</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    {{-- hold sale list modal --}}
    <div class="modal fade" id="hold-list-modal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content hold-modal">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    <h4 class="section_title">Hold Sale List</h4>
                </div>
                <div class="modal-body pt-0">
                    <div class="table-responsive">
                        <table class="table table-bordered m-0">
                            <thead>
                                <tr>
                                    <th>Sl.</th>
                                    <th>Customer</th>
                                    <th>Time</th>
                                    <th>Note</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($cart_holds as $key => $hold_sale)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $hold_sale->user?->name }}</td>
                                        <td>{{ formatDate($hold_sale->created_at, 'd-m-Y h:i a') }}</td>
                                        <td>{{ $hold_sale->note }}</td>
                                        <td>
                                            <a href="javascript:;" class="btn btn-sm btn-danger"
                                                onclick="deleteFromHold({{ $hold_sale->id }},this)">
                                                <i class="fas fa-trash"></i>
                                            </a>

                                            <a href="javascript:;" class="btn btn-sm btn-primary"
                                                onclick="editFromHold({{ $hold_sale->id }},this)">
                                                <i class="fas fa-check"></i>
                                            </a>
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

    <div class="modal fade" id="stockUpdateModal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <form action="javascript:;" id="stockUpdateModalForm">
                        <input type="hidden" name="row_number">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="purchase_price">{{ __('Purchase Price') }}
                                        ({{ currency_icon() }})</label>
                                    <input type="number" name="purchase_price" class="form-control" id="purchase_price"
                                        value="{{ old('purchase_price') }}" step="0.01">
                                    @error('purchase_price')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="selling_price">{{ __('Selling Price') }}
                                        ({{ currency_icon() }})</label>
                                    <input type="number" name="selling_price" class="form-control" id="selling_price"
                                        value="{{ old('selling_price') }}" step="0.01">
                                    @error('selling_price')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-success stockModalSave"
                        form="stockUpdateModalForm">{{ __('Save') }}</button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="invoiceModal" tabindex="-1" role="dialog" aria-labelledby="invoiceModal"
        aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-body invoice_modal_body">

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <a type="submit" class="btn btn-primary print-redirect" href=""
                        target="_blank">{{ __('Print') }}</a>
                </div>
            </div>
        </div>
    </div>


@endsection

@push('js')
    <script src="{{ asset('backend/js/jquery-ui.min.js') }}"></script>
    <script>
        $("[name='due_date']").datepicker('destroy');
        // load products
        (function($) {
            "use strict";
            $(document).ready(function() {
                totalSummery();
                loadProudcts();
                $("#flatpickr-date,[name='sale_date']").flatpickr({
                    dateFormat: "d-m-Y",
                });

                // update pos quantity
                $(document).on("input", ".pos_input_qty", function(e) {
                    let quantity = $(this).val();
                    if (quantity < 1) {
                        return;
                    }
                    $('.preloader_area').removeClass('d-none');
                    let parernt_td = $(this).parents('td');
                    let rowid = parernt_td.data('rowid')
                    const pos = getCurrentPos();
                    $.ajax({
                        type: 'get',
                        data: {
                            rowid,
                            quantity
                        },
                        url: "{{ route('admin.cart-quantity-update') }}",
                        success: function(response) {
                            $(".product-table-container").html(response)
                            $('[name="source"]').niceSelect();
                            totalSummery();
                            console.log(pos);
                            scrollToCurrent(pos)
                            $('.preloader_area').addClass('d-none');
                        },
                        error: function(response) {
                            if (response.status == 500) {
                                toastr.error("{{ __('Server error occurred') }}")
                            }

                            if (response.status == 403) {
                                toastr.error("{{ __('Server error occurred') }}")
                            }
                            $('.preloader_area').addClass('d-none');
                        }
                    });

                });

                // load customer address
                $("#customer_id").on("change", function() {
                    let customer_id = $(this).val();
                    $("#order_customer_id").val(customer_id ? customer_id : 'walk-in-customer');

                    const discount = $('select#customer_id option:selected').data('discount');
                    if (discount) {
                        $('[name="discount_type"]').val(2).niceSelect('update');
                        $('#discount_total_amount').val(discount);
                        updateDiscountType(2);
                    }
                })

                // add new customer modal
                $("#add-customer-form").on("submit", function(e) {
                    e.preventDefault();
                    const from = $('#add-customer-form')
                    $.ajax({
                        type: 'POST',
                        data: $('#add-customer-form').serialize(),
                        url: $('#add-customer-form').attr('action'),
                        success: function(response) {
                            toastr.success(response.message)
                            $("#addCustomer").modal('hide');
                            $('#add-customer-form')[0].reset();
                            $("#customer_id").html(response.view)
                        },
                        error: function(response) {

                            if (response.status == 500) {
                                toastr.error("{{ __('Server error occurred') }}")
                            }
                        }
                    });
                })

                // product search modal
                $("#product_search_form,#favorite_product_search_form").on("submit", function(e) {
                    e.preventDefault();

                    $("#search_btn_text").html(`<div class="spinner-border" role="status">
                                            <span class="sr-only">Loading...</span></div>`)

                    const favorite = 0;
                    if ($(this).attr('id') == 'favorite_product_search_form') {
                        favorite = 1;
                    }

                    $.ajax({
                        type: 'get',
                        data: $('#product_search_form').serialize(),
                        url: "{{ route('admin.load-products') }}?favorite=" + favorite,
                        success: function(response) {
                            $("#search_btn_text").html(
                                `<i class="fas fa-search fa-2x fs-25"></i>`)
                            if (favorite == 1) {
                                $("#favoriteProducts .product_body").html(response)
                            } else {
                                $("#products .product_body").html(response)
                            }
                        },
                        error: function(response) {
                            $("#search_btn_text").html(
                                `<i class="fas fa-search fa-3x fs-25"></i>`)

                            if (response.status == 500) {
                                toastr.error("{{ __('Server error occurred') }}")
                            }

                            if (response.status == 403) {
                                toastr.error(response.responseJSON.message);
                            }

                        }
                    });
                })

                $('.modal-reset-button').on('click', function() {
                    const productId = $(this).data('product-id');
                    resetCart();
                    load_product_model(productId);
                })

                $('[name="discount_type"]').on('change', function() {
                    const type = $(this).val();
                    const symbol = type == 'percent' ? '%' : "{{ currency_icon() }}"
                    $('.discount_icon').html(symbol)
                })

                $(document).on('click', function() {
                    // without #name or #itemList remove show class

                    var searchInput = $("#name, #favoriteName");
                    var itemList = $("#itemList,#favoriteItemList");

                    // If click is outside the search input and dropdown, hide the dropdown
                    if (!searchInput.is(event.target) && !itemList.is(event.target) && itemList.has(
                            event.target).length === 0) {
                        itemList.removeClass("show");
                    }
                })
                let ProductAutoComplete = $('#name, #favoriteName').autocomplete({
                    html: true,
                    source: function(request, response) {

                        let favorite = 0;
                        if (this.element[0]?.id == 'favoriteName') {
                            favorite = 1;
                        }

                        $.ajax({
                            url: "{{ route('admin.load-products-list') }}?favorite=" +
                                favorite,
                            dataType: 'json',
                            data: {
                                name: request.term
                            },
                            success: function(response) {

                                if (response.total > 0) {

                                    if (favorite == 0) {
                                        $('#itemList').html(response.view).addClass(
                                            'show');
                                    } else {
                                        $('#favoriteItemList').html(response.view)
                                            .addClass('show');
                                    }
                                }
                            }
                        })
                    },
                    minLength: 2,
                    open: function() {
                        $(this).removeClass('ui-corner-all').addClass('ui-corner-top')
                    },
                    close: function() {
                        $(this).removeClass('ui-corner-top').addClass('ui-corner-all')
                    }
                })
                // search products

                $("#category_id,#brand_id").on('change', function() {
                    const category_id = $('#category_id').val();
                    const brand = $('#brand_id').val();
                    const name = $('#name').val();

                    loadProudcts({
                        category_id,
                        brand,
                        name
                    })
                })

                $("#service_category_id,#service_name,#favorite_service_name").on('input', function() {
                    const category_id = $('#service_category_id').val();
                    let name = null;
                    if (this.id != 'service_category_id') {
                        name = $(this).val();
                    }


                    loadProudcts({
                        service_category_id: category_id,
                        service_name: name
                    }, 'service')
                })

                // extra - discount edit toggle
                $(document).on('click', '.dis-tgl', function() {
                    $(".dis-form").slideToggle("fast")
                })


                // add payment row
                $('.add-payment').on('click', function() {
                    const row = `@include('pos::payment-row', ['add' => true])`;
                    $('#paymentRow').append(row)
                    $('[name="payment_type[]"]').niceSelect();

                })
                $(document).on('click', '.remove-payment', function() {
                    $(this).parents('tr').remove()
                })

                $(document).on('click', '.price', function() {
                    let child = $(this).children('input');

                    child.removeClass('d-none');
                    // remove child span
                    child.siblings('span').addClass('d-none');
                })
                $(document).on('focusout', '.price > input', function() {
                    const $this = $(this);
                    const rowId = $this.data('rowid');
                    const value = $this.val();

                    updatePrice(rowId, value)
                    calculateExtra()
                });

                $('.hold-btn').on('click', function() {
                    $('#hold-modal').modal('show')
                })
                $('#hold-sale-form').on('submit', function() {
                    let customer_id = $('#customer_id').val();
                    customer_id = customer_id == 'walk-in-customer' ? 0 : customer_id;
                    $('#hold-sale-form [name="user_id"]').val(customer_id)

                    $('#hold-sale-form').prop('action', "{{ route('admin.cart.hold') }}").submit()
                })
                $('.hold-list-btn').on('click', function() {
                    $('#hold-list-modal').modal('show')
                })

                $(document).on('change', '[name="source"]', function() {
                    let source = $(this).parents('tr').data('rowid');
                    $.ajax({
                        type: 'get',
                        data: {
                            rowid: source,
                            source: $(this).val()
                        },
                        url: "{{ route('admin.cart.source.update') }}",
                        success: function(response) {}
                    });
                    calculateExtra()
                    $(this).parents('tr').find('.edit-btn').toggleClass('d-none')
                })

                $(document).on('click', '.edit-btn', function() {
                    let source = $(this).parents('tr').data('rowid');
                    const purchasePrice = $(this).data('purchase')
                    const sellingPrice = $(this).data('selling')
                    $('#purchase_price').val(purchasePrice)
                    $('#selling_price').val(sellingPrice)
                    $('[name="row_number"]').val(source)
                    $('#stockUpdateModal').modal('show')
                })
                $('#stockUpdateModalForm').on('submit', function() {
                    const rowId = $('[name="row_number"]').val();
                    const purchasePrice = $('#purchase_price').val();
                    const sellingPrice = $('#selling_price').val();
                    const val = parseInt(sellingPrice || 0) - parseInt(purchasePrice || 0);
                    $('input[data-rowid="' + rowId + '"]').val(val);
                    $('#stockUpdateModal').modal('hide')
                    // reset the form
                    $('#stockUpdateModalForm').trigger('reset');

                    const row = $('tr[data-rowid="' + rowId + '"]');
                    var editBtn = row.find('.edit-btn');
                    const deleteBtn = editBtn.siblings('a');

                    editBtn.remove();

                    const newButton = `<a href="javascript:;" class="edit-btn"
                        data-purchase="${purchasePrice}" data-selling="${sellingPrice}">
                        <i class="fas fa-edit"></i>
                    </a>`;
                    deleteBtn.after(newButton);
                    $.ajax({
                        type: 'get',
                        data: {
                            rowid: rowId,
                            purchase_price: purchasePrice,
                            selling_price: sellingPrice,
                            price: sellingPrice,
                        },
                        url: "{{ route('admin.cart.price.update') }}",
                        success: function(response) {
                            $('#stockUpdateModal').modal('hide')
                            updatePrice(rowId, sellingPrice)
                            calculateExtra()
                            totalSummery();
                        }
                    });

                })


                $(".datepicker").datepicker("option", "dateFormat", "dd-mm-yy").val("{{ formatDate(now()) }}");

                $(document).on('keydown', function(event) {
                    const keycode = event.keyCode || event.which;
                    if (keycode === 13) {
                        event.preventDefault();

                        const $current = $(event.target);
                        const $next = $current.nextAll('input, select, textarea, button').first();

                        if ($next.length) {
                            $next.focus();
                        }

                        // else unfocus
                        else {
                            $current.blur();
                        }
                    }
                })
            });
        })(jQuery);

        function updateDiscountType(newType) {
            $('[name="discount_type"]').val(newType).niceSelect('update').trigger('change');
        }

        function calculateExtra() {
            let total = 0;
            $('[name="source"]').each(function() {
                if ($(this).val() == '2') {
                    let price = $(this).closest('td').siblings('td.row_total').text();
                    price = parseFloat(price.replace(/[^0-9\.]/g, ''));
                    total += isNaN(price) ? 0 : price;
                }
            });
            $('#extra').text(`{{ currency_icon() }}${total}`)
        }

        function deleteFromHold(id, parent) {
            $.ajax({
                url: "{{ route('admin.cart.hold.delete', '') }}/" + id,
                success: function(response) {
                    $(parent).parents('tr').remove()
                    totalSummery();
                    $('#hold-list-modal').modal('hide')
                }
            });
        }

        function editFromHold(id, parent) {
            $.ajax({
                url: "{{ route('admin.cart.hold.edit', '') }}/" + id,
                success: function(response) {
                    $(".product-table-container").html(response)

                    $('[name="source"]').niceSelect();
                    totalSummery();

                    $(parent).parents('tr').remove()
                    $('#hold-list-modal').modal('hide')

                }
            });
        }

        function updatePrice(rowId, price) {
            const pos = getCurrentPos();
            $.ajax({
                type: 'get',
                data: {
                    rowId,
                    price
                },
                url: "{{ route('admin.cart-price-update') }}",
                success: function(response) {
                    $(".product-table-container").html(response)
                    $('[name="source"]').niceSelect();
                    scrollToCurrent(pos)
                    totalSummery();
                }
            });
        }

        function load_product_model(product_id) {
            $('.preloader_area').removeClass('d-none');
            // check if cart has item from different restaurant using ajax request
            $.ajax({
                type: 'get',
                url: "{{ route('admin.check-cart-restaurant', '') }}" + "/" + product_id,
                success: function(response) {
                    if (response.status == true) {
                        // add product id to reset button of modal
                        $(".modal-reset-button").attr('data-product-id', product_id);
                        $("#resetCartModal").modal('show');
                        $('.preloader_area').addClass('d-none');
                    } else {
                        loadProductModal(product_id)
                    }
                },
                error: function(response) {
                    toastr.error("{{ __('Server error occurred') }}")
                    $('.preloader_area').addClass('d-none');
                }
            });
        }

        function loadProductModal(product_id) {
            $('.preloader_area').removeClass('d-none');
            $.ajax({
                type: 'get',
                url: "{{ url('admin/pos/load-product-modal') }}" + "/" + product_id,
                success: function(response) {
                    $(".load_product_modal_response").html(response)
                    $("#cartModal").modal('show');
                    $('.preloader_area').addClass('d-none');
                },
                error: function(response) {
                    toastr.error("{{ __('Server error occurred') }}")
                    $('.preloader_area').addClass('d-none');
                }
            });
        }

        function removeCartItem(rowId) {
            const pos = getCurrentPos();
            $.ajax({
                type: 'get',
                url: "{{ url('admin/pos/remove-cart-item') }}" + "/" + rowId,
                success: function(response) {
                    $(".product-table-container").html(response)
                    $('[name="source"]').niceSelect();
                    totalSummery();
                    scrollToCurrent(pos)
                    toastr.success("{{ __('Remove successfully') }}")
                },
                error: function(response) {
                    toastr.error("{{ __('Server error occurred') }}")
                }
            });
        }

        function calculateTotalFee() {

            let subTotal = $('#sub_total').val() || '0.00';

            // remove , if exists
            if (subTotal.includes(',')) {
                subTotal = subTotal.replace(/,/g, '');
            }
            subTotal = parseFloat(subTotal);
            let deliveryFee = parseFloat($('#delivery_fee').val()) || 0;

            let tax = parseFloat($('#tax_fee').val()) || 0;
            let discount = parseFloat($('#discount').val()) || 0;
            let total = parseFloat($('#total_fee').val()) || 0;

            let discountType = $('[name="discount_type"]').val();

            if (discountType === 'percent') {
                discount = subTotal * (discount / 100);
            }

            // Calculate the total
            total = subTotal + deliveryFee + tax - discount;

            // Update the total field with the calculated value
            $('#total_fee').val(total.toFixed(2));

            $('[name="order_sub_total"]').val(subTotal);
            $('[name="order_delivery_fee"]').val(deliveryFee);
            $('[name="order_tax"]').val(tax);
            $('[name="order_discount"]').val(discount.toFixed(2));
            $('[name="order_total_fee"]').val(total.toFixed(2));
        }


        function loadProudcts(data = null, type = 'product') {
            $('.preloader_area').removeClass('d-none');
            $.ajax({
                type: 'get',
                url: "{{ route('admin.load-products') }}",
                data: data,
                success: function(response) {

                    $("#products .product_body").html(response.productView)
                    $("#favoriteProducts .product_body").html(response.favProductView)
                    $(".service_body").html(response.serviceView)
                    $(".favorite_service_body").html(response.favoriteServiceView)
                    $('.preloader_area').addClass('d-none');
                },
                error: function(response) {

                    toastr.error("{{ __('Server error occurred') }}")
                    location.reload();
                }
            });
        }

        function loadPagination(url) {
            $('.preloader_area').removeClass('d-none');
            $.ajax({
                type: 'get',
                url: url,
                success: function(response) {
                    $("#products .product_body").html(response.productView)
                    $("#favoriteProducts .product_body").html(response.favProductView)
                    $(".service_body").html(response.serviceView)
                    $('.preloader_area').addClass('d-none');
                },
                error: function(response) {
                    toastr.error("{{ __('Server error occurred') }}")
                }
            });
        }

        function openPaymentModal() {
            $('.pos-footer').css('z-index', 0);
            const finalTotal = $('#finalTotal').text().replace(/[^0-9.]/g, '');
            const discountAmount = $('#tds').text();
            const subTotal = $('#total').text().replace(/[^0-9.]/g, '');
            const item = $('#titems').text();


            $('[name="sub_total"]').val(subTotal);
            $('#sub_totalModal').text(subTotal);

            $('#discount_amountModal').text(discountAmount);
            $('[name="discount_amount"]').val(discountAmount);

            let grandTotal = parseFloat(finalTotal);
            $('#total_amountModal').text(grandTotal);
            $('#total_amount_modal_input').val(grandTotal);
            $('#total_amountModal2').text(grandTotal);


            // load customer info
            let customer_id = $('#customer_id').val();
            $("#order_customer_id").val(customer_id ? customer_id : 'walk-in-customer');
            loadCustomer(customer_id);

            // total items

            $('#itemModal').text(item);



            $('.paying_amount').val(grandTotal);
            $('#paid_amountModal').text(grandTotal);


            // hide rows
            if (!discountAmount) {
                $('.discount-row').addClass('d-none');
            } else {
                $('.discount-row').removeClass('d-none');
            }

            calDue()
            $('#payment-modal').modal('show')
        }

        function resetCart() {
            $.ajax({
                type: 'get',
                url: "{{ route('admin.modal-cart-clear') }}",
                success: function(response) {
                    $(".product-table tbody").html('')
                    totalSummery();
                    // hide discount form and reset values
                    $('.dis-form').hide();
                    $('#discount_total_amount').val(0);
                    $('#discount_type').val(1).trigger('change');
                    toastr.success("{{ __('Cart reset successfully') }}")
                },
                error: function(response) {
                    toastr.error("{{ __('Server error occurred') }}")
                }
            });
        }

        function singleAddToCart(id, serviceType = 'product') {
            $('.preloader_area').removeClass('d-none');
            $.ajax({
                type: 'get',
                data: {
                    product_id: id,
                    type: 'single',
                    serviceType: serviceType
                },
                url: "{{ url('/admin/pos/add-to-cart') }}",
                success: function(response) {
                    $(".product-table-container").html(response)

                    $('[name="source"]').niceSelect();
                    toastr.success("{{ __('Item added successfully') }}")
                    totalSummery();
                    $('.preloader_area').addClass('d-none');
                    scrollToCurrent();
                },
                error: function(response) {
                    if (response.status == 500) {
                        toastr.error("{{ __('Server error occurred') }}")
                    }

                    if (response.status == 403) {
                        toastr.error(response.responseJSON.message)
                    }
                    $('.preloader_area').addClass('d-none');
                }
            });
        }

        function numberFormat(n) {
            return Number(n).toFixed(2)
        }

        function showDeliveryInfo(show = false) {
            if (show) {
                $('.add_delivery_info').removeClass('d-none');
            } else {
                $('.add_delivery_info').addClass('d-none');
            }
        }

        function discountExist() {
            let discount_total_amount = $('#discount_total_amount').val()
            let discount_type = $('#discount_type').val()
            let total_amount_get_text = Number($('#total').text().replace(/[^0-9.]/g, ''))
            let vat_amount = Number($('#ttax2').text())
            let totalAmount = 0
            let percentage = null

            if (discount_type == 1) {
                if (discount_total_amount > total_amount_get_text) {
                    discount_total_amount = total_amount_get_text
                }
                totalAmount = numberFormat(
                    Number(total_amount_get_text - discount_total_amount).toFixed(6)
                )
            } else {
                if (discount_total_amount > 100) {
                    discount_total_amount = 100
                }
                percentage = (discount_total_amount * total_amount_get_text) / 100
                totalAmount = total_amount_get_text - percentage
            }


            $('#tds').text(percentage ? percentage : discount_total_amount)
            $('input[name=discount_amount]').val(
                percentage ? percentage : discount_total_amount
            )
            $('#discount_amountModal').text(
                percentage ? percentage : discount_total_amount
            )
            vat_amount = 0
            let grand_total = numberFormat(
                // Number(exchange_total)
                Number(totalAmount) + Number(vat_amount) - 0
            )
            $('#ttax2').text(vat_amount)
            $('#gtotal').text(totalAmount)
            $('#finalTotal').text(grand_total)
            $('#discount_total_amount').val(discount_total_amount)
            $('#discountModal').modal('hide')
            $('input[name=total_amount]').val(grand_total)
            $('#total_amountModal').text(grand_total)
            $('input[name=paying_amount]').val(grand_total)
            $('#paing_amountModal').text(grand_total)
            $('#total_amountModal2').text(grand_total)
            totalSummery()
        }

        const accountsList = @json($accounts);

        $(document).on('change', 'select[name="payment_type[]"]', function() {
            const accounts = accountsList.filter(account => account.account_type == $(this).val());

            if (accounts) {
                let html = '<select name="account_id[]" id="" class="form-control">';
                accounts.forEach(account => {
                    switch ($(this).val()) {
                        case 'bank':
                            html +=
                                `<option value="${account.id}">${account.bank_account_number} (${account.bank?.name})</option>`;
                            break;
                        case "mobile_banking":
                            html +=
                                `<option value="${account.id}">${account.mobile_number}(${account.mobile_bank_name})</option>`;
                            break;
                        case 'card':
                            html +=
                                `<option value="${account.id}">${account.card_number} (${account.bank?.name})</option>`;
                            break;
                        default:
                            break;
                    }

                });
                html += '</select>';

                $(this).parents('td').siblings('.account_info').html(html);
            }

            if ($(this).val() == 'cash' || $(this).val() == 'advance') {
                $(this).parents('td').siblings('.account_info').html('');
                const cash =
                    `<input type="text" name="account_id[]" class="form-control" value="${$(this).val()}" readonly>`;

                $(this).parents('td').siblings('.account_info').html(cash);
            }

            $('select[name="account_id[]"]').niceSelect();
        });

        $('.receive_cash').on('input', function() {
            const cash = $(this).val();
            const total = $('#finalTotal').text();
            let change_amount = 0;

            if (numberOnly(total) < numberOnly(cash)) {
                change_amount = numberOnly(cash) - numberOnly(total);
            }

            if (change_amount < 0 || !change_amount) {
                $('.change_amount').val(0)
            } else {
                $('.change_amount').val(change_amount)
            }
        })


        $(document).on('input', '[name="paying_amount[]"]', function() {
            const amount = [];
            const allAmount = $('[name="paying_amount[]"]').each(function() {
                amount.push($(this).val());
            })
            const amountVal = amount.reduce((a, b) => Number(a) + Number(b), 0);
            $('#paid_amountModal').text(amountVal);

            let totalAmount = $('#total_amountModal').text();
            totalAmount = parseFloat(totalAmount);
            if (totalAmount > amountVal) {
                $('#normalPayment [name="total_due"]').val(totalAmount - amountVal);
                $(".due-date").removeClass('d-none');
            } else {
                $(".due-date").addClass('d-none');
                $('#normalPayment [name="total_due"]').val(totalAmount - amountVal);
            }
            calDue();
        })

        $('.addCustomer').on('click', function(e) {
            e.preventDefault();
            $('#addCustomer').modal('show');
            $('.pos-footer').css('z-index', 0)
        })
        $('#addCustomer .close').on('click', function() {
            modalHide('#addCustomer')
        })
    </script>

    <script>
        function modalHide(id) {
            $(id).modal('hide')
            $('.pos-footer').css('z-index', 9000)
            $('#checkoutForm').trigger('reset');
            calDue()
        }

        $(document).on('keydown', function(event) {
            if (event.key === 'Escape' || event.keyCode === 27) {
                modalHide('#payment-modal')
            }
        });

        function paymentSubmit(e) {
            e.preventDefault();

            // check cart is empty or not

            if ($('.product-table tbody > tr').length == 0) {
                toastr.error("{{ __('Cart is empty') }}")
                return
            }

            // if customer is walk-in customer

            if ($('#customer_id').val() == 'walk-in-customer') {

                let totalAmount = $('#total_amountModal').text();
                totalAmount = parseFloat(totalAmount);
                let paidAmount = $('#paid_amountModal').text();
                paidAmount = parseFloat(paidAmount);

                if (totalAmount != paidAmount) {
                    toastr.error("{{ __('Can\'t Make Due Sale for Guest Customer') }}")
                    return
                }
            }

            const formData = $('#checkoutForm').serialize();
            $.ajax({
                type: 'POST',
                data: formData,
                url: "{{ route('admin.place-order') }}",
                success: function(response) {

                    console.log(response);
                    $(".product-table tbody").html('')
                    if (response['alert-type'] == 'success') {

                        toastr.success(response.message)
                        $("#payment-modal").modal('hide');
                        $("#checkoutForm")[0].reset();
                        $('#titems').text(0);
                        $('#discount_total_amount').val(0);
                        $('#tds').text(0);
                        totalSummery();

                        $('.pos-footer').css('z-index', 9000);

                        // reset customer select
                        $("#customer_id").val('').trigger('change')

                        // reset discount type
                        $('#discount_type').val(0).trigger('change')

                        // hide discount form
                        $('.dis-form').hide();

                        // reset payment type
                        $('#paymentRow').html(`@include('pos::payment-row')`);
                        $('[name="payment_type[]"]').niceSelect();

                        $('.invoice_modal_body').html(response.invoice);
                        $('.print-redirect').attr('href', response.invoiceRoute);

                        // reset form
                        $('#checkoutForm').trigger('reset');
                        $('#invoiceModal').modal('show');

                    } else {
                        toastr.error(response.message)
                    }
                },
                error: function(response) {
                    if (response.status == 500) {
                        toastr.error("{{ __('Server error occurred') }}")
                    }
                    console.log(response);
                }
            });
        }


        function totalSummery() {
            const products = $('.product-table tbody > tr > .row_total');

            let total = 0;

            products.each(function() {
                total += numberOnly($(this).text())
            })


            $('#total').text(`{{ currency_icon() }}${total}`)


            // discount
            const discount = $('#discount_total_amount').val() ? $('#discount_total_amount').val() : 0;
            const discountType = $('#discount_type').val();
            let discountAmount = 0;

            if (discountType == 2) {
                discountAmount = total * parseFloat(discount) / 100
            } else {
                discountAmount = parseFloat(discount)
            }

            // total after discount = total - discount

            $('#gtotal').text(`{{ currency_icon() }}${total - discountAmount}`)

            // vat

            const vat = $('#ttax2').text();

            let vatAmount = 0;

            if (vat) {
                vatAmount = total * parseFloat(vat) / 100
            }

            $('#totalVat').text(`{{ currency_icon() }}${vatAmount}`)

            // totalAmountWithVat
            const grandTotal = total - discountAmount + vatAmount
            $('#totalAmountWithVat').text(`{{ currency_icon() }}${grandTotal}`)
            $('#finalTotal').text(`{{ currency_icon() }}${grandTotal}`)
            calculateExtra()

            // products.length
            $('#titems').text(products.length)
        }

        // load customer
        function loadCustomer(id) {
            if (id != 'walk-in-customer') {
                $.ajax({
                    type: 'GET',
                    url: "{{ route('admin.customer.single', '') }}/" + id,
                    success: function(response) {
                        $('#previous_due').text(response.total_due);
                        $('.due').removeClass('d-none')
                        calDue()
                    }
                })
            } else {
                $('.due').addClass('d-none')
            }
        }

        function calDue() {
            let previous_due = $('#previous_due').text();
            previous_due = parseFloat(previous_due);
            // let due_amountModal = $('#due_amountModal').text();
            // due_amountModal = parseFloat(due_amountModal);

            let currentDue = $('#normalPayment [name="total_due"]').val();

            currentDue = parseFloat(currentDue ? currentDue : 0);
            const totalDue = currentDue + previous_due;
            $('#due_amountModal').text(`{{ currency_icon() }}${totalDue}`)
        }

        function wishlist(event, id, type) {
            event.stopPropagation();
            let url = "{{ route('admin.ingredient.wishlist', ':id') }}";

            url = url.replace(':id', id);

            // remove d-none from preloader
            $('.preloader_area').removeClass('d-none');

            $.ajax({
                type: 'POST',
                data: {
                    type: type
                },
                url: url,
                success: function(response) {
                    if (response['alert-type'] == 'success') {
                        toastr.success(response.message)
                        loadProudcts();
                    } else {
                        toastr.error(response.message)
                    }
                },
                error: function(response) {
                    if (response.status == 500) {
                        toastr.error("{{ __('Server error occurred') }}")
                    }
                }
            });
        }

        function serviceWishlist(event, id, type) {
            event.stopPropagation();
            let url = "{{ route('admin.service.wishlist', ':id') }}";

            url = url.replace(':id', id);

            // remove d-none from preloader
            $('.preloader_area').removeClass('d-none');

            $.ajax({
                type: 'POST',
                data: {
                    type: type
                },
                url: url,
                success: function(response) {
                    if (response['alert-type'] == 'success') {
                        toastr.success(response.message)
                        loadProudcts();
                    } else {
                        toastr.error(response.message)
                    }
                },
                error: function(response) {
                    if (response.status == 500) {
                        toastr.error("{{ __('Server error occurred') }}")
                    }
                }
            });
        }


        function scrollToCurrent(pos = 'scroll') {
            const $current = $('.pos_pro_list_table tbody tr:last-child');
            const sidebar = $('.product-table .table-responsive');
            if (pos !== 'scroll') {
                sidebar.animate({
                    scrollTop: pos
                }, 300);
            } else if (pos === 'scroll' && $current.length) {

                const sidebarOffset = sidebar.offset().top;
                const currentOffset = $current.offset().top;

                sidebar.animate({
                    scrollTop: sidebar.scrollTop() + (currentOffset - sidebarOffset -
                        50)
                }, 300);
            }
        }

        function getCurrentPos() {
            const sidebar = $('.product-table .table-responsive');
            return sidebar.scrollTop();
        }
    </script>
@endpush
