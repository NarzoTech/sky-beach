@extends('admin.layouts.master')
@section('title', __('POS'))
@push('css')
    <link rel="stylesheet" href="{{ asset('backend/css/pos.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/css/jquery-ui.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/backend/css/invoice.css') }}">
    <style>
        .ui-autocomplete {
            z-index: 215000000 !important;
        }

        /* Modal title color when parent has bg-primary */
        .bg-primary .modal-title,
        .modal-header.bg-primary .modal-title {
            color: #fff !important;
        }

        /* Table Selection Modal Styles */
        #tableSelectionModal .modal-body {
            background: #f8f9fa;
        }

        .table-status-legend {
            background: #fff;
            padding: 12px 20px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            display: inline-flex;
            flex-wrap: wrap;
            gap: 15px 25px;
            justify-content: center;
        }

        .table-status-dot {
            width: 14px;
            height: 14px;
            border-radius: 50%;
            display: inline-block;
            box-shadow: 0 2px 4px rgba(0,0,0,0.15);
        }
        .table-status-dot.available { background: linear-gradient(135deg, #28a745, #20c997); }
        .table-status-dot.partial { background: linear-gradient(135deg, #fd7e14, #ffc107); }
        .table-status-dot.occupied { background: linear-gradient(135deg, #dc3545, #e74c3c); }
        .table-status-dot.reserved { background: linear-gradient(135deg, #6c757d, #495057); }

        .tables-grid {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 18px;
            padding: 15px;
            max-height: 60vh;
            overflow-y: auto;
        }

        .table-card {
            background: #fff;
            border-radius: 12px;
            padding: 15px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid #f0f0f0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }
        .table-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.12);
            border-color: #e0e0e0;
        }
        .table-card.selected {
            border-color: #007bff;
            background: linear-gradient(135deg, #e3f2fd, #bbdefb);
            box-shadow: 0 0 0 3px rgba(0,123,255,0.25);
        }
        .table-card.available {
            border-left: 4px solid #28a745;
            background: linear-gradient(135deg, #f8fff8, #fff);
        }
        .table-card.partial {
            border-left: 4px solid #fd7e14;
            background: linear-gradient(135deg, #fff9e6, #fff3cd);
        }
        .table-card.occupied {
            border-left: 4px solid #dc3545;
            opacity: 0.6;
            cursor: not-allowed;
            background: linear-gradient(135deg, #fff5f5, #fff);
        }
        .table-card.reserved {
            border-left: 4px solid #6c757d;
            opacity: 0.7;
            cursor: not-allowed;
            background: linear-gradient(135deg, #f8f9fa, #fff);
        }
        .table-card.disabled {
            opacity: 0.5;
            cursor: not-allowed;
            pointer-events: none;
        }
        .table-card.disabled:hover {
            transform: none;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }

        .table-shape {
            position: relative;
            width: 80px;
            height: 80px;
            margin: 20px auto 15px;
        }

        .table-surface {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: linear-gradient(145deg, #8B4513, #A0522D);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 8px rgba(0,0,0,0.3), inset 0 2px 4px rgba(255,255,255,0.1);
            z-index: 1;
        }

        .table-shape.square .table-surface {
            width: 50px;
            height: 50px;
            border-radius: 8px;
        }
        .table-shape.round .table-surface {
            width: 55px;
            height: 55px;
            border-radius: 50%;
        }
        .table-shape.rectangle .table-surface {
            width: 65px;
            height: 40px;
            border-radius: 8px;
        }

        .table-number {
            color: #fff;
            font-weight: bold;
            font-size: 11px;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
        }

        /* Chairs */
        .chair {
            position: absolute;
            width: 14px;
            height: 14px;
            background: linear-gradient(145deg, #666, #888);
            border-radius: 3px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        /* Chair positions for different seat counts */
        .seats-1 .chair-1 { top: -18px; left: 50%; transform: translateX(-50%); }

        .seats-2 .chair-1 { top: -18px; left: 50%; transform: translateX(-50%); }
        .seats-2 .chair-2 { bottom: -18px; left: 50%; transform: translateX(-50%); }

        .seats-3 .chair-1 { top: -18px; left: 50%; transform: translateX(-50%); }
        .seats-3 .chair-2 { bottom: -18px; left: 25%; transform: translateX(-50%); }
        .seats-3 .chair-3 { bottom: -18px; left: 75%; transform: translateX(-50%); }

        .seats-4 .chair-1 { top: -18px; left: 50%; transform: translateX(-50%); }
        .seats-4 .chair-2 { bottom: -18px; left: 50%; transform: translateX(-50%); }
        .seats-4 .chair-3 { left: -18px; top: 50%; transform: translateY(-50%); }
        .seats-4 .chair-4 { right: -18px; top: 50%; transform: translateY(-50%); }

        .seats-5 .chair-1 { top: -18px; left: 30%; transform: translateX(-50%); }
        .seats-5 .chair-2 { top: -18px; left: 70%; transform: translateX(-50%); }
        .seats-5 .chair-3 { bottom: -18px; left: 50%; transform: translateX(-50%); }
        .seats-5 .chair-4 { left: -18px; top: 50%; transform: translateY(-50%); }
        .seats-5 .chair-5 { right: -18px; top: 50%; transform: translateY(-50%); }

        .seats-6 .chair-1 { top: -18px; left: 30%; transform: translateX(-50%); }
        .seats-6 .chair-2 { top: -18px; left: 70%; transform: translateX(-50%); }
        .seats-6 .chair-3 { bottom: -18px; left: 30%; transform: translateX(-50%); }
        .seats-6 .chair-4 { bottom: -18px; left: 70%; transform: translateX(-50%); }
        .seats-6 .chair-5 { left: -18px; top: 50%; transform: translateY(-50%); }
        .seats-6 .chair-6 { right: -18px; top: 50%; transform: translateY(-50%); }

        .seats-7 .chair-1 { top: -18px; left: 25%; transform: translateX(-50%); }
        .seats-7 .chair-2 { top: -18px; left: 50%; transform: translateX(-50%); }
        .seats-7 .chair-3 { top: -18px; left: 75%; transform: translateX(-50%); }
        .seats-7 .chair-4 { bottom: -18px; left: 30%; transform: translateX(-50%); }
        .seats-7 .chair-5 { bottom: -18px; left: 70%; transform: translateX(-50%); }
        .seats-7 .chair-6 { left: -18px; top: 50%; transform: translateY(-50%); }
        .seats-7 .chair-7 { right: -18px; top: 50%; transform: translateY(-50%); }

        .seats-8 .chair-1 { top: -18px; left: 25%; transform: translateX(-50%); }
        .seats-8 .chair-2 { top: -18px; left: 50%; transform: translateX(-50%); }
        .seats-8 .chair-3 { top: -18px; left: 75%; transform: translateX(-50%); }
        .seats-8 .chair-4 { bottom: -18px; left: 25%; transform: translateX(-50%); }
        .seats-8 .chair-5 { bottom: -18px; left: 50%; transform: translateX(-50%); }
        .seats-8 .chair-6 { bottom: -18px; left: 75%; transform: translateX(-50%); }
        .seats-8 .chair-7 { left: -18px; top: 50%; transform: translateY(-50%); }
        .seats-8 .chair-8 { right: -18px; top: 50%; transform: translateY(-50%); }

        .table-card.available .chair { background: linear-gradient(145deg, #28a745, #20c997); }
        .table-card.partial .chair { background: linear-gradient(145deg, #28a745, #20c997); }
        .table-card.partial .chair.chair-occupied { background: linear-gradient(145deg, #dc3545, #c0392b); }
        .table-card.occupied .chair { background: linear-gradient(145deg, #dc3545, #c0392b); }
        .table-card.reserved .chair { background: linear-gradient(145deg, #6c757d, #495057); }
        .table-card.selected .chair { background: linear-gradient(145deg, #007bff, #0056b3); }
        .chair.chair-occupied {
            background: linear-gradient(145deg, #dc3545, #c0392b) !important;
            box-shadow: 0 0 6px rgba(220, 53, 69, 0.5);
        }

        .table-info {
            margin-top: 10px;
            padding: 10px 8px 5px;
            border-top: 1px solid #eee;
            background: rgba(0,0,0,0.02);
            border-radius: 0 0 10px 10px;
            margin: 10px -15px -15px;
        }
        .table-info strong {
            font-size: 13px;
            color: #333;
            display: block;
            margin-bottom: 4px;
            font-weight: 600;
        }
        .table-info small {
            font-size: 11px;
            display: block;
            margin-top: 2px;
            line-height: 1.4;
        }
        .table-info small i,
        .table-info small svg {
            margin-right: 4px;
            width: 12px;
        }

        /* Take Away button checked state */
        #orderTypeTakeAway:checked + label {
            background: #47c363 !important;
            border-color: #47c363 !important;
            color: #fff !important;
        }

        /* Product Search Dropdown Styles */
        #itemList, #favoriteItemList {
            width: 100% !important;
            max-height: 400px;
            overflow-y: auto;
            padding: 8px 0;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            border: 1px solid #e0e0e0;
        }

        .product_list_item {
            padding: 10px 15px;
            border-bottom: 1px solid #f0f0f0;
            transition: background 0.2s ease;
        }

        .product_list_item:last-child {
            border-bottom: none;
        }

        .product_list_item:hover {
            background: #f8f9fa;
        }

        .product_list_img {
            width: 50px;
            height: 50px;
            border-radius: 8px;
            overflow: hidden;
            flex-shrink: 0;
            margin-right: 12px;
        }

        .product_list_img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .product_list_info {
            flex: 1;
            min-width: 0;
        }

        .product_list_info h6 {
            margin: 0 0 4px 0;
            font-size: 14px;
            font-weight: 600;
            color: #333;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .product_list_info p {
            font-size: 12px;
            color: #666;
        }

        .product_list_info .text-primary {
            font-weight: 600;
            color: #696cff !important;
        }

        /* POS Item Tabs Styles */
        #posItemTabs {
            border: none;
            margin-bottom: 0;
            display: flex;
            width: 100%;
        }

        #posItemTabs .nav-item {
            flex: 1;
            width: 50%;
        }

        #posItemTabs .nav-link {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 10px 20px;
            font-weight: 600;
            color: #6c757d;
            background: #f8f9fa;
            transition: all 0.2s ease;
            width: 100%;
            text-align: center;
            box-shadow: none;
        }

        #posItemTabs .nav-link:hover {
            color: #47c363;
            background: #e9ecef;
        }

        #posItemTabs .nav-link.active {
            color: #fff;
            background: #47c363;
            border: 1px solid #47c363;
            border-radius: 4px;
            box-shadow: none;
        }

        #posItemTabsContent .tab-pane {
            min-height: 300px;
        }

        #posItemTabsContent .card-body {
            padding: 10px;
        }

        /* Equal height cards */
        .section-body > .row {
            display: flex;
            flex-wrap: wrap;
        }

        .section-body > .row > .col-lg-5,
        .section-body > .row > .col-lg-7 {
            display: flex;
            flex-direction: column;
        }

        .section-body > .row > .col-lg-5 > .card,
        .section-body > .row > .col-lg-7 > .card {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        #products {
            height: 100%;
        }

        .col-lg-7 > .card {
            height: 100%;
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
                        <div class="card" id="products">
                                    <div class="card-header">
                                        <form id="product_search_form" class="pos_pro_search_form w-100">
                                            <input type="hidden" name="search_type" id="search_type" value="regular">
                                            <div class="row">
                                                @if($posSettings->show_barcode)
                                                <div class="col-12">
                                                    <div class="form-group mb-2">
                                                        <div class="input-group">
                                                            <span class="input-group-text bg-primary text-white">
                                                                <i class="fas fa-barcode"></i>
                                                            </span>
                                                            <input type="text" class="form-control" id="barcode_input"
                                                                placeholder="{{ __('Scan Barcode') }}"
                                                                autocomplete="off" autofocus>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif
                                                <div class="col-5" id="categoryFilterContainer">
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
                                                <div class="col-7" id="searchFilterContainer">
                                                    <div class="form-group mb-2">
                                                        <input type="text" class="form-control" name="name"
                                                            id="name"
                                                            placeholder="{{ __('Search menu items...') }}"
                                                            autocomplete="off" value="{{ request()->get('name') }}">
                                                        <ul class="dropdown-menu" id="itemList">
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>

                                        <!-- Tabs for Regular and Combo -->
                                        <ul class="nav nav-tabs nav-tabs-solid mt-2" id="posItemTabs" role="tablist">
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link active" id="regular-tab" data-bs-toggle="tab"
                                                    data-bs-target="#regularItems" type="button" role="tab"
                                                    aria-controls="regularItems" aria-selected="true">
                                                    <i class="fas fa-utensils me-1"></i>{{ __('Regular') }}
                                                </button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="combo-tab" data-bs-toggle="tab"
                                                    data-bs-target="#comboItems" type="button" role="tab"
                                                    aria-controls="comboItems" aria-selected="false">
                                                    <i class="fas fa-box me-1"></i>{{ __('Combo') }}
                                                </button>
                                            </li>
                                        </ul>
                                    </div>

                                    <!-- Tab Content -->
                                    <div class="tab-content pt-0" id="posItemTabsContent">
                                        <div class="tab-pane fade show active" id="regularItems" role="tabpanel" aria-labelledby="regular-tab">
                                            <div class="card-body product_body">
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="comboItems" role="tabpanel" aria-labelledby="combo-tab">
                                            <div class="card-body combo_body">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                    </div>

                    <div class="col-lg-7">
                        <div class="card">
                            <div class="card-header pos_sidebar_button">
                                @if($posSettings->show_customer)
                                <div class="row w-100 d-none" id="customerSelectionRow">
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
                                @endif
                                <!-- Customer Loyalty Points Display -->
                                <div class="row w-100 mt-2 d-none" id="customerLoyaltyRow">
                                    <div class="col-12">
                                        <div class="alert alert-info py-2 mb-0 d-flex justify-content-between align-items-center">
                                            <span>
                                                <i class="fas fa-star text-warning me-2"></i>
                                                <strong>{{ __('Loyalty Points:') }}</strong>
                                                <span id="customerPointsBalance">0</span> {{ __('pts') }}
                                                <small class="text-muted ms-2">
                                                    ({{ __('Worth') }} <span id="customerPointsValue">{{ currency_icon() }}0</span>)
                                                </small>
                                            </span>
                                            <span class="badge bg-success" id="pointsToEarn">
                                                +<span id="earnablePoints">0</span> {{ __('pts on this order') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <!-- Order Type Selection -->
                                <div class="row w-100 mt-2">
                                    <div class="col-12">
                                        <div class="d-flex gap-2 w-100" role="group" id="orderTypeGroup">
                                            <input type="radio" class="btn-check" name="order_type_radio" id="orderTypeDineIn" value="dine_in" checked>
                                            <label class="btn btn-outline-primary flex-fill" for="orderTypeDineIn">
                                                <i class="fas fa-utensils me-2"></i>{{ __('Dine In') }}
                                            </label>

                                            <input type="radio" class="btn-check" name="order_type_radio" id="orderTypeTakeAway" value="take_away">
                                            <label class="btn flex-fill" style="border: 1px solid #47c363; color: #47c363;" for="orderTypeTakeAway">
                                                <i class="fas fa-shopping-bag me-2"></i>{{ __('Take Away') }}
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <!-- Hidden Table ID Input -->
                                <input type="hidden" name="table_id" id="table_id" value="">
                                <!-- Edit Mode Banner -->
                                @if(isset($editingOrderId) && $editingOrderId)
                                <div class="row w-100 mt-2" id="editModeRow">
                                    <div class="col-12">
                                        <div class="alert alert-warning mb-0 py-2 d-flex justify-content-between align-items-center">
                                            <span>
                                                <i class="fas fa-edit me-2"></i>
                                                <strong>{{ __('Adding items to Order') }} #{{ $editingOrderId }}</strong>
                                                @if($editingTableName)
                                                <span class="badge bg-primary ms-2">{{ $editingTableName }}</span>
                                                @endif
                                            </span>
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="cancelEditMode()">
                                                <i class="fas fa-times me-1"></i>{{ __('Cancel') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                @endif
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
                                                <td>{{ __('Items') }}</td>
                                                <td class="text-end"><span id="titems">{{ count($cart_contents) }}</span></td>
                                            </tr>
                                            <tr>
                                                <td>{{ __('Subtotal') }}</td>
                                                <td class="text-end" id="subtotalDisplay">{{ currency($cumalitive_sub_total) }}</td>
                                            </tr>
                                            <tr id="discountRow" style="display: none;">
                                                <td>{{ __('Discount') }}</td>
                                                <td class="text-end text-success" id="discountDisplay">- {{ currency_icon() }}0.00</td>
                                            </tr>
                                            <tr id="taxRow">
                                                <td>{{ __('Tax') }} (<span id="taxRateDisplay">{{ optional($posSettings)->pos_tax_rate ?? 0 }}</span>%)</td>
                                                <td class="text-end" id="taxDisplay">{{ currency_icon() }}0.00</td>
                                            </tr>
                                            <tr class="pay-row">
                                                <td>{{ __('Total Payable') }}</td>
                                                <td class="text-end" id="totalAmountWithVat">{{ currency($cumalitive_sub_total) }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <!-- Hidden fields for calculations -->
                                    <input type="hidden" id="total" value="{{ $cumalitive_sub_total }}">
                                    <input type="hidden" id="subtotal" value="{{ $cumalitive_sub_total }}">
                                    <input type="hidden" id="extra" value="0">
                                    <input type="hidden" id="tds" value="0">
                                    <input type="hidden" id="gtotal" value="{{ $cumalitive_sub_total }}">
                                    <input type="hidden" id="totalVat" value="0">
                                    <input type="hidden" id="taxRate" value="{{ optional($posSettings)->pos_tax_rate ?? 0 }}">
                                    <input type="hidden" id="taxAmount" value="0">
                                    <input type="hidden" id="business_vat" value="0">
                                    <input type="hidden" id="discount_total_amount" value="0">
                                    <input type="hidden" id="discount_type" value="1">
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
                <button type="button" class="btn hold-list-btn" style="background: #03b0d4 !important; color: #fff !important; border-color: #03b0d4 !important;" title="Hold Sale List">
                    <i class="fa fa-list" aria-hidden="true"></i>
                </button>
                <button type="button" class="btn hold-btn" style="background: #47c363 !important; color: #fff !important; border-color: #47c363 !important;" title="Hold Current Sale">
                    {{ __('Hold') }}
                </button>
            </div>
            <button type="button" class="btn btn-info running-orders-btn" onclick="openRunningOrders()" title="Running Orders">
                <i class="fa fa-utensils me-1" aria-hidden="true"></i>
                {{ __('Running') }}
                <span class="running-orders-count badge bg-danger ms-1 d-none">0</span>
            </button>
            <button type="button" class="btn cancel-btn" onclick="resetCart()">
                Clear
            </button>
            @if(isset($editingOrderId) && $editingOrderId)
            <button type="button" class="btn btn-warning" onclick="addItemsToExistingOrder({{ $editingOrderId }})" id="addToOrderButton">
                <i class="fas fa-plus me-1"></i>
                <span id="addToOrderBtnText">{{ __('Add to Order') }} #{{ $editingOrderId }}</span>
            </button>
            @else
            <button type="button" class="btn payment-btn" onclick="openPaymentModal()" id="paymentButton">
                <span id="paymentBtnText">Payment</span>
            </button>
            @endif
        </div>
    </footer>
    </div>

    @include('components.admin.preloader')

    <!-- Product Modal -->
    <div class="modal fade" id="cartModal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document" style="max-width: 900px;">
            <div class="modal-content" style="border-radius: 12px; overflow: hidden; border: none;">
                <div class="modal-body p-0">
                    <div class="load_product_modal_response">
                        {{-- Content loaded via AJAX --}}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cart Item Addon Modal -->
    <div class="modal fade" id="cartAddonModal" tabindex="-1" role="dialog" aria-labelledby="cartAddonModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="cartAddonModalLabel">
                        <i class="bx bx-plus-circle me-2"></i>{{ __('Select Add-ons') }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="addon_cart_rowid" value="">
                    <input type="hidden" id="addon_menu_item_id" value="">
                    <div id="addon-list-container">
                        <div class="text-center py-3">
                            <i class="fas fa-spinner fa-spin fa-2x"></i>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="button" class="btn btn-info" onclick="saveCartAddons()">
                        <i class="fas fa-check me-1"></i>{{ __('Apply Add-ons') }}
                    </button>
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

    <div class="modal fade" id="payment-modal" role="dialog" aria-labelledby="paymentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" action="" id="checkoutForm" onSubmit="paymentSubmit(event)">
                    @csrf
                    <input type="hidden" name="order_customer_id" id="order_customer_id" value="">
                    <input type="hidden" name="order_type" id="order_type" value="dine_in">
                    <input type="hidden" name="table_id" id="order_table_id" value="">
                    <input type="hidden" name="guest_count" id="guest_count" value="1">
                    <input type="hidden" name="waiter_id" id="waiter_id" value="">
                    <input type="hidden" name="sale_note" id="sale_note" value="">
                    <input type="hidden" name="sub_total" value="" autocomplete="off">
                    <input type="hidden" name="total_amount" value="0" id="total_amount_modal_input" autocomplete="off">
                    <input type="hidden" name="discount_amount" value="0" autocomplete="off">
                    <input type="hidden" name="points_discount" id="pointsDiscountInput" value="0">
                    <input type="hidden" name="loyalty_customer_id" id="loyaltyCustomerIdInput" value="">
                    <input type="hidden" name="sale_date" value="{{ formatDate(now()) }}" autocomplete="off">

                    <!-- Modal Header -->
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">
                            <i class="bx bx-credit-card me-2"></i>{{ __('Checkout') }}
                            <span class="badge bg-white bg-opacity-25 ms-2 fw-normal" id="orderTypeBadge">{{ __('Dine-in') }}</span>
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <!-- Total Amount Display -->
                        <div class="text-center mb-4 py-3 bg-dark rounded">
                            <small class="text-muted d-block">{{ __('Total Amount') }}</small>
                            <h1 class="text-white mb-0">
                                <span id="total_amountModal2">0</span>
                                <small class="fs-4">{{ currency_icon() }}</small>
                            </h1>
                            <small class="text-muted"><span id="itemModal">0</span> {{ __('items') }}</small>
                        </div>

                        <div class="row">
                            <!-- Left: Order Summary -->
                            <div class="col-md-5">
                                <div class="card mb-3">
                                    <div class="card-header py-2">
                                        <strong>{{ __('Order Summary') }}</strong>
                                    </div>
                                    <div class="card-body p-0">
                                        <table class="table table-sm mb-0">
                                            <tr>
                                                <td>{{ __('Subtotal') }}</td>
                                                <td class="text-end" id="sub_totalModal">0</td>
                                            </tr>
                                            <tr class="discount-row">
                                                <td>{{ __('Discount') }}</td>
                                                <td class="text-end text-danger" id="discount_amountModal">0</td>
                                            </tr>
                                            <tr class="points-redemption-row d-none">
                                                <td>
                                                    <i class="fas fa-star text-warning"></i> {{ __('Points') }}
                                                    <small class="text-muted d-block" id="availablePointsText">0 pts</small>
                                                </td>
                                                <td class="text-end">
                                                    <div class="input-group input-group-sm" style="width: 100px; margin-left: auto;">
                                                        <input type="number" class="form-control form-control-sm text-center"
                                                            name="points_to_redeem" id="pointsToRedeem"
                                                            value="0" min="0" max="0" step="1">
                                                        <button type="button" class="btn btn-sm btn-outline-primary"
                                                            onclick="applyMaxPoints()">Max</button>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr class="points-discount-row d-none">
                                                <td><i class="fas fa-tag text-success"></i> {{ __('Points Discount') }}</td>
                                                <td class="text-end text-success" id="pointsDiscountModal">-0</td>
                                            </tr>
                                            <tr class="due d-none">
                                                <td>{{ __('Previous Due') }}</td>
                                                <td class="text-end" id="previous_due" data-amount="0">0</td>
                                            </tr>
                                            <tr class="table-dark">
                                                <td><strong>{{ __('Grand Total') }}</strong></td>
                                                <td class="text-end"><strong id="total_amountModal">0</strong></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>

                                <!-- Cash Summary -->
                                <div class="card">
                                    <div class="card-body p-2">
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <label class="form-label small mb-1">{{ __('Paid') }}</label>
                                                <div class="fw-bold text-primary" id="paid_amountModal">0</div>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label small mb-1">{{ __('Change') }}</label>
                                                <div class="fw-bold text-success">
                                                    <input type="text" class="form-control form-control-sm change_amount text-success fw-bold"
                                                        name="return_amount" value="0" readonly style="background: transparent; border: none;">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="due d-none mt-2 pt-2 border-top">
                                            <div class="d-flex justify-content-between">
                                                <span class="text-danger">{{ __('Due Amount') }}</span>
                                                <strong class="text-danger" id="due_amountModal">0</strong>
                                            </div>
                                            <input type="text" class="form-control form-control-sm mt-1 d-none" name="total_due" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Right: Payment Section -->
                            <div class="col-md-7">
                                <!-- Payment Method -->
                                <div class="card mb-3">
                                    <div class="card-header py-2 d-flex justify-content-between align-items-center">
                                        <strong>{{ __('Payment Method') }}</strong>
                                        <button type="button" class="btn btn-sm btn-outline-primary add-payment">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                    <div class="card-body p-2" id="paymentRow">
                                        @include('pos::payment-row')
                                    </div>
                                </div>

                                <!-- Cash Received -->
                                <div class="card mb-3">
                                    <div class="card-header py-2">
                                        <strong>{{ __('Cash Received') }}</strong>
                                    </div>
                                    <div class="card-body p-2">
                                        <input type="number" class="form-control form-control-lg text-center receive_cash removeZero"
                                            name="receive_amount" value="0" step="0.01" style="font-size: 1.5rem; font-weight: bold;">

                                        <!-- Quick Amount Buttons -->
                                        <div class="d-flex flex-wrap gap-1 mt-2" id="quickAmountBtns">
                                            <button type="button" class="btn btn-outline-secondary btn-sm flex-fill quick-amount-btn" data-amount="50">50</button>
                                            <button type="button" class="btn btn-outline-secondary btn-sm flex-fill quick-amount-btn" data-amount="100">100</button>
                                            <button type="button" class="btn btn-outline-secondary btn-sm flex-fill quick-amount-btn" data-amount="200">200</button>
                                            <button type="button" class="btn btn-outline-secondary btn-sm flex-fill quick-amount-btn" data-amount="500">500</button>
                                            <button type="button" class="btn btn-outline-secondary btn-sm flex-fill quick-amount-btn" data-amount="1000">1000</button>
                                            <button type="button" class="btn btn-outline-primary btn-sm flex-fill" onclick="setExactAmount()">{{ __('Exact') }}</button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Due Date (hidden by default) -->
                                <div class="due-date d-none mb-3">
                                    <div class="card">
                                        <div class="card-body p-2">
                                            <label class="form-label small mb-1">{{ __('Due Date') }}</label>
                                            <input type="date" class="form-control form-control-sm" name="due_date" id="flatpickr-date">
                                        </div>
                                    </div>
                                </div>

                                <!-- Remark -->
                                <div class="mb-2">
                                    <input type="text" class="form-control form-control-sm" name="remark"
                                        value="" autocomplete="off" placeholder="{{ __('Remark (optional)') }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="modal-footer py-2">
                        <button type="button" class="btn btn-secondary" onclick="modalHide('#payment-modal')">
                            <i class="fas fa-times me-1"></i>{{ __('Cancel') }} <small class="text-muted">[Esc]</small>
                        </button>
                        <button type="button" class="btn btn-warning" id="startOrderBtn" onclick="startRunningOrder()">
                            <i class="fas fa-clock me-1"></i>{{ __('Start Order') }}
                        </button>
                        <button type="submit" id="checkout" class="btn btn-success btn-lg px-4">
                            <i class="fas fa-check me-1"></i>{{ __('Complete Payment') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- hold modal --}}
    <div class="modal fade" id="hold-modal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="bx bx-pause-circle me-2"></i>{{ __('Hold Sale') }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="javascript:;" id="hold-sale-form" method="post">
                        @csrf
                        <div class="form-group">
                            <label for="">{{ __('Note') }}</label>
                            <input type="text" class="form-control hold-sale-note" name="note">
                            <input type="hidden" class="form-control" name="user_id">
                        </div>
                        <div class="text-end mt-3">
                            <button class="btn btn-primary" type="submit">{{ __('Hold') }}</button>
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
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="bx bx-list-ul me-2"></i>{{ __('Hold Sale List') }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
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

    {{-- Running Orders Modal --}}
    <div class="modal fade payment-modal" id="running-orders-modal" tabindex="-1" role="dialog" aria-labelledby="runningOrdersModal"
        aria-hidden="true">
        <div class="modal-dialog modal-xl running-orders-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="bx bx-food-menu me-2"></i>{{ __('Running Orders') }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="ro-search-strip">
                    <div class="ro-search-box">
                        <i class="bx bx-search ro-search-icon"></i>
                        <input type="text" id="running-orders-search" placeholder="{{ __('Search by invoice, table, customer or waiter...') }}" autocomplete="off">
                        <span class="ro-search-clear d-none" id="ro-search-clear"><i class="bx bx-x"></i></span>
                        <span class="ro-search-spinner d-none" id="ro-search-spinner"><i class="bx bx-loader-alt bx-spin"></i></span>
                        <kbd class="ro-search-kbd">{{ __('Ctrl+F') }}</kbd>
                    </div>
                </div>
                <div class="modal-body" id="running-orders-content">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="mt-3 text-muted">{{ __('Loading running orders...') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
    /* Running Orders Search Strip */
    .ro-search-strip {
        padding: 12px 20px;
        background: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
    }
    .ro-search-box {
        position: relative;
        display: flex;
        align-items: center;
        background: #fff;
        border: 2px solid #e9ecef;
        border-radius: 10px;
        padding: 0 14px;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .ro-search-box:focus-within {
        border-color: #696cff;
        box-shadow: 0 0 0 3px rgba(105,108,255,0.1);
    }
    .ro-search-icon {
        font-size: 18px;
        color: #a1acb8;
        flex-shrink: 0;
        transition: color 0.2s;
    }
    .ro-search-box:focus-within .ro-search-icon { color: #696cff; }
    .ro-search-box input {
        flex: 1;
        border: none;
        outline: none;
        padding: 10px 12px;
        font-size: 14px;
        background: transparent;
        color: #566a7f;
    }
    .ro-search-box input::placeholder { color: #b4bdc6; }
    .ro-search-clear {
        cursor: pointer;
        font-size: 20px;
        color: #a1acb8;
        padding: 2px;
        line-height: 1;
        border-radius: 50%;
        transition: all 0.15s;
    }
    .ro-search-clear:hover { color: #dc3545; background: #fde8ea; }
    .ro-search-spinner { font-size: 18px; color: #696cff; }
    .ro-search-kbd {
        font-size: 11px;
        padding: 2px 6px;
        background: #f0f1ff;
        color: #696cff;
        border-radius: 4px;
        border: 1px solid #e0e1ff;
        font-family: inherit;
        white-space: nowrap;
        margin-left: 6px;
    }
    /* Hide cards during client-side filter */
    .running-order-col-hidden { display: none !important; }
    @media (max-width: 575px) {
        .ro-search-strip { padding: 8px 12px; }
        .ro-search-kbd { display: none; }
        .ro-search-box input { font-size: 13px; padding: 8px 10px; }
    }

    /* Running Order Card Styles */
    .running-order-card {
        border-radius: var(--pm-radius) !important;
        transition: all 0.3s ease;
        border: 2px solid transparent !important;
        cursor: pointer;
    }

    .running-order-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(105,108,255,0.15) !important;
        border-color: var(--pm-primary) !important;
    }

    .running-order-card .card-body {
        padding: 16px !important;
    }

    .running-order-card .order-badge {
        color: var(--pm-white);
        font-size: 12px;
        padding: 6px 12px;
        border-radius: 6px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .running-order-card .order-badge.dine-in {
        background: var(--pm-primary);
    }

    .running-order-card .order-badge.take-away {
        background: var(--pm-success);
    }

    .running-order-card .order-invoice {
        font-size: 12px;
        font-weight: 700;
        color: var(--pm-gray);
    }

    .running-order-card .order-time {
        font-size: 12px;
        color: #a1acb8;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .running-order-card .order-meta-badge {
        background: var(--pm-gray-light);
        color: var(--pm-gray);
        font-size: 11px;
        padding: 4px 8px;
        border-radius: 4px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .running-order-card .order-status {
        font-size: 11px;
        padding: 6px 10px;
        border-radius: 6px;
        font-weight: 600;
    }

    .running-order-card .order-status.preparing {
        background: var(--pm-warning);
        color: var(--pm-white);
    }

    .running-order-card .order-status.ready {
        background: var(--pm-success);
        color: var(--pm-white);
    }

    .running-order-card .order-items-preview {
        background: var(--pm-gray-light);
        border-radius: 8px;
        padding: 10px 12px !important;
        max-height: 72px;
        overflow: hidden;
    }

    .running-order-card .order-items-preview .item-row {
        display: flex;
        justify-content: space-between;
        font-size: 12px;
        margin-bottom: 4px;
    }

    .running-order-card .order-items-preview .item-row:last-child {
        margin-bottom: 0;
    }

    .running-order-card .order-items-preview .item-name {
        color: var(--pm-dark);
        max-width: 150px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .running-order-card .order-items-preview .item-price {
        font-weight: 600;
        color: var(--pm-dark);
    }

    .running-order-card .card-footer {
        background: var(--pm-white) !important;
        border-top: 1px solid var(--pm-border) !important;
        padding: 12px 16px !important;
    }

    .running-order-card .order-total {
        font-size: 18px;
        font-weight: 700;
        color: var(--pm-primary);
    }

    .running-order-card .order-customer {
        font-size: 12px;
        color: #a1acb8;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    /* Empty State */
    .running-orders-empty {
        padding: 60px 20px;
        text-align: center;
    }

    .running-orders-empty .empty-icon {
        width: 80px;
        height: 80px;
        background: var(--pm-gray-light);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
    }

    .running-orders-empty .empty-icon i {
        font-size: 32px;
        color: #a1acb8;
    }

    .running-orders-empty h5 {
        color: var(--pm-dark);
        font-weight: 600;
        margin-bottom: 8px;
    }

    .running-orders-empty p {
        color: var(--pm-gray);
        font-size: 14px;
    }

    /* Responsive - Tablet (768px - 1199px) */
    @media (min-width: 768px) and (max-width: 1199px) {
        .running-orders-dialog {
            max-width: 95%;
            margin: 1rem auto;
        }

        .running-order-card .card-body {
            padding: 14px !important;
        }

        .running-order-card .order-items-preview {
            max-height: 65px;
        }

        .running-order-card .order-items-preview .item-name {
            max-width: 120px;
        }
    }

    /* Mobile */
    @media (max-width: 767px) {
        #running-orders-content {
            padding: 16px !important;
        }

        .running-order-card .order-total {
            font-size: 16px;
        }
    }
    </style>

    {{-- Order Details Modal --}}
    <div class="modal fade" id="order-details-modal" tabindex="-1" role="dialog" aria-labelledby="orderDetailsModal"
        aria-hidden="true">
        <div class="modal-dialog modal-xl order-details-dialog" role="document">
            <div class="modal-content order-details-modal">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="bx bx-receipt me-2"></i>{{ __('Order Details') }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4" id="order-details-content">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="mt-3 text-muted">{{ __('Loading order details...') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
    /* Order Details Modal Styles */
    #order-details-modal.show .modal-dialog {
        transform: none !important;
        transition: none !important;
    }

    .order-details-dialog {
        margin-top: 2rem;
        margin-bottom: calc(var(--pos-footer-height, 70px) + 1rem);
    }

    .order-details-modal {
        border-radius: 16px;
        border: none;
    }

    #order-details-content {
        max-height: calc(100vh - 150px - var(--pos-footer-height, 70px));
        overflow-x: hidden;
        overflow-y: auto;
    }

    /* Order Details Content Styles */
    .order-details-content .info-card {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 16px;
        border: none;
        height: 100%;
    }

    .order-details-content .info-card.primary {
        background: #f0f1ff;
        border-left: 4px solid #696cff;
    }

    .order-details-content .info-card .info-icon {
        width: 48px;
        height: 48px;
        background: #fff;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }

    .order-details-content .info-card .info-icon i {
        font-size: 20px;
        color: #696cff;
    }

    .order-details-content .info-card .info-label {
        font-size: 12px;
        color: #697a8d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 4px;
    }

    .order-details-content .info-card .info-value {
        font-size: 16px;
        font-weight: 600;
        color: #566a7f;
    }

    .order-details-content .info-card .info-value.primary {
        color: #696cff;
    }

    .order-details-content .duration-card {
        background: #fff;
        border: 2px solid #696cff;
        border-radius: 12px;
        padding: 16px 20px;
    }

    .order-details-content .duration-display {
        font-family: 'Courier New', monospace;
        font-size: 28px;
        font-weight: 700;
        letter-spacing: 2px;
        color: #696cff;
        min-width: 160px;
    }

    .order-details-content .duration-display span {
        display: inline-block;
        width: 2ch;
        text-align: center;
    }

    .order-details-content .status-badge {
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
    }

    .order-details-content .progress-card {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 16px;
    }

    .order-details-content .progress-icon {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .order-details-content .progress-icon.ready {
        background: #d4edda;
        color: #28a745;
    }

    .order-details-content .progress-icon.cooking {
        background: #fff3cd;
        color: #ffc107;
    }

    .order-details-content .staff-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #f8f9fa;
        padding: 10px 16px;
        border-radius: 8px;
        font-size: 13px;
    }

    .order-details-content .staff-badge i {
        color: #696cff;
    }

    .order-details-content .items-card {
        border: 1px solid #e9ecef;
        border-radius: 12px;
        overflow: hidden;
    }

    .order-details-content .items-card .card-header {
        background: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
        padding: 12px 16px;
    }

    .order-details-content .items-table {
        margin-bottom: 0;
    }

    .order-details-content .items-table thead th {
        background: #f8f9fa;
        border-bottom: 2px solid #e9ecef;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        color: #697a8d;
        padding: 12px 16px;
    }

    .order-details-content .items-table tbody td {
        padding: 12px 16px;
        vertical-align: middle;
    }

    .order-details-content .items-table tfoot td {
        padding: 12px 16px;
        background: #f8f9fa;
    }

    .order-details-content .qty-control {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: #f8f9fa;
        border-radius: 8px;
        padding: 4px;
    }

    .order-details-content .qty-control .qty-btn {
        width: 28px;
        height: 28px;
        border: none;
        background: #fff;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
        color: #697a8d;
    }

    .order-details-content .qty-control .qty-btn:hover {
        background: #696cff;
        color: #fff;
    }

    .order-details-content .qty-control .qty-input {
        width: 40px;
        height: 28px;
        border: none;
        background: transparent;
        text-align: center;
        font-weight: 600;
        font-size: 14px;
    }

    .order-details-content .remove-btn {
        width: 28px;
        height: 28px;
        border: none;
        background: #fff5f5;
        border-radius: 6px;
        color: #dc3545;
        cursor: pointer;
        transition: all 0.2s;
        opacity: 0.6;
    }

    .order-details-content .order-item-row:hover .remove-btn {
        opacity: 1;
    }

    .order-details-content .remove-btn:hover {
        background: #dc3545;
        color: #fff;
    }

    .order-details-content .total-row {
        font-size: 18px;
    }

    .order-details-content .total-amount {
        color: #696cff;
        font-weight: 700;
    }

    .order-details-content .action-buttons {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        padding: 16px;
        margin: 16px -24px -24px;
        border-top: 1px solid #e9ecef;
        position: sticky;
        bottom: -24px;
        background: #fff;
        z-index: 10;
        box-shadow: 0 -4px 12px rgba(0,0,0,0.08);
    }

    .order-details-content .action-buttons .btn {
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 600;
    }

    .order-details-content .action-buttons .btn-back {
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        color: #697a8d;
    }

    .order-details-content .action-buttons .btn-back:hover {
        background: #e9ecef;
    }

    .order-details-content .action-buttons .btn-cancel {
        border: 1px solid #dc3545;
        color: #dc3545;
        background: transparent;
    }

    .order-details-content .action-buttons .btn-cancel:hover {
        background: #dc3545;
        color: #fff;
    }

    .order-details-content .action-buttons .btn-add {
        background: #ffab00;
        border: none;
        color: #fff;
    }

    .order-details-content .action-buttons .btn-add:hover {
        background: #ff9500;
    }

    .order-details-content .action-buttons .btn-pay {
        background: #696cff;
        border: none;
        color: #fff;
    }

    .order-details-content .action-buttons .btn-pay:hover {
        background: #5a5ee0;
    }

    /* Responsive - Tablet (768px - 1199px) */
    @media (min-width: 768px) and (max-width: 1199px) {
        .order-details-dialog {
            max-width: 95%;
            margin: 1rem auto;
        }

        .order-details-content .info-card {
            padding: 12px;
        }

        .order-details-content .duration-display {
            font-size: 24px;
        }

        .order-details-content .items-table thead th,
        .order-details-content .items-table tbody td {
            padding: 10px 12px;
            font-size: 13px;
        }

        .order-details-content .action-buttons {
            flex-wrap: wrap;
        }

        .order-details-content .action-buttons .btn {
            padding: 8px 16px;
            font-size: 13px;
        }
    }

    /* Mobile */
    @media (max-width: 767px) {
        #order-details-content {
            padding: 16px !important;
        }

        .order-details-content .duration-display {
            font-size: 20px;
        }

        .order-details-content .action-buttons {
            flex-direction: column;
            margin: 16px -16px -16px;
            bottom: -16px;
            padding: 12px 16px;
        }

        .order-details-content .action-buttons > div {
            display: flex;
            gap: 8px;
            width: 100%;
        }

        .order-details-content .action-buttons .btn {
            flex: 1;
        }
    }
    </style>

    {{-- Running Order Payment Modal --}}
    <div class="modal fade" id="running-order-payment-modal" tabindex="-1" role="dialog" aria-labelledby="runningOrderPaymentModal"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="bx bx-credit-card me-2"></i>{{ __('Complete Payment') }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="payment-order-id" value="">

                    <!-- Order Summary -->
                    <div class="card bg-light mb-3">
                        <div class="card-body py-2">
                            <div class="row">
                                <div class="col-6">
                                    <small class="text-muted">{{ __('Order') }}</small>
                                    <h5 class="mb-0" id="payment-order-invoice">#--</h5>
                                </div>
                                <div class="col-6 text-end">
                                    <small class="text-muted">{{ __('Table') }}</small>
                                    <h5 class="mb-0" id="payment-order-table">--</h5>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Discount Section -->
                    <div class="card mb-3">
                        <div class="card-body py-2">
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <label class="form-label mb-0 fw-bold">{{ __('Discount') }}</label>
                                </div>
                                <div class="col-md-4">
                                    <div class="input-group input-group-sm">
                                        <input type="number" class="form-control" id="payment-discount-amount" value="0" min="0" step="0.01" onchange="applyPaymentDiscount()">
                                        <span class="input-group-text">{{ currency_icon() }}</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="input-group input-group-sm">
                                        <input type="number" class="form-control" id="payment-discount-percent" value="0" min="0" max="100" step="0.1" onchange="applyPaymentDiscountPercent()">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Amount Summary -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="card border-secondary">
                                <div class="card-body text-center py-3">
                                    <small class="text-muted d-block">{{ __('Subtotal') }}</small>
                                    <h4 class="text-secondary mb-0" id="payment-subtotal-amount">{{ currency_icon() }}0</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-primary">
                                <div class="card-body text-center py-3">
                                    <small class="text-muted d-block">{{ __('Grand Total') }}</small>
                                    <h3 class="text-primary mb-0" id="payment-total-amount">{{ currency_icon() }}0</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-success">
                                <div class="card-body text-center py-3">
                                    <small class="text-muted d-block">{{ __('Change Due') }}</small>
                                    <h3 class="text-success mb-0" id="payment-change-due">{{ currency_icon() }}0</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Methods -->
                    <div class="card mb-3">
                        <div class="card-header bg-dark text-white py-2">
                            <i class="fas fa-wallet me-2"></i>{{ __('Payment Methods') }}
                        </div>
                        <div class="card-body" id="payment-methods-container">
                            <!-- Payment method rows will be added here -->
                        </div>
                        <div class="card-footer">
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="addPaymentMethod()">
                                <i class="fas fa-plus me-1"></i>{{ __('Add Payment Method') }}
                            </button>
                        </div>
                    </div>

                    <!-- Amount Received -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label fw-bold">{{ __('Amount Received') }}</label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text">{{ currency_icon() }}</span>
                                    <input type="number" class="form-control" id="payment-amount-received" value="0" step="0.01" onchange="calculateChange()">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label fw-bold">{{ __('Total Paying') }}</label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text">{{ currency_icon() }}</span>
                                    <input type="text" class="form-control" id="payment-total-paying" value="0" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="button" class="btn btn-success btn-lg" onclick="processRunningOrderPayment()">
                        <i class="fas fa-check me-1"></i>{{ __('Complete Payment') }}
                    </button>
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


    {{-- Table Selection Modal --}}
    <div class="modal fade" id="tableSelectionModal" tabindex="-1" role="dialog" aria-labelledby="tableSelectionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="tableSelectionModalLabel">
                        <i class="fas fa-chair me-2"></i>{{ __('Select a Table') }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <!-- Table Status Legend -->
                    <div class="d-flex justify-content-center mb-4">
                        <div class="table-status-legend">
                            <span class="d-flex align-items-center">
                                <span class="table-status-dot available me-2"></span>
                                <small class="text-muted">{{ __('Available') }}</small>
                            </span>
                            <span class="d-flex align-items-center">
                                <span class="table-status-dot partial me-2"></span>
                                <small class="text-muted">{{ __('Partially Occupied') }}</small>
                            </span>
                            <span class="d-flex align-items-center">
                                <span class="table-status-dot occupied me-2"></span>
                                <small class="text-muted">{{ __('Fully Occupied') }}</small>
                            </span>
                            <span class="d-flex align-items-center">
                                <span class="table-status-dot reserved me-2"></span>
                                <small class="text-muted">{{ __('Reserved') }}</small>
                            </span>
                        </div>
                    </div>

                    <!-- Tables Grid -->
                    <div class="tables-grid" id="tablesGrid">
                        @foreach ($availableTables ?? [] as $table)
                            @php
                                $availableSeats = $table->capacity - ($table->occupied_seats ?? 0);
                                $isPartial = $table->occupied_seats > 0 && $availableSeats > 0;
                                $isFullyOccupied = $availableSeats <= 0;
                                $tableClass = $isFullyOccupied ? 'occupied' : ($isPartial ? 'partial' : $table->status);
                                $canSelect = $availableSeats > 0 && $table->status !== 'reserved' && $table->status !== 'maintenance';
                            @endphp
                            <div class="table-card {{ $tableClass }} {{ !$canSelect ? 'disabled' : '' }}"
                                 data-table-id="{{ $table->id }}"
                                 data-table-name="{{ $table->name }}"
                                 data-table-capacity="{{ $table->capacity }}"
                                 data-table-available-seats="{{ $availableSeats }}"
                                 data-table-occupied-seats="{{ $table->occupied_seats ?? 0 }}"
                                 data-table-status="{{ $table->status }}"
                                 onclick="{{ $canSelect ? 'selectTable(this)' : '' }}">
                                <div class="table-shape {{ $table->shape ?? 'square' }} seats-{{ min($table->capacity, 8) }}">
                                    <div class="table-surface">
                                        <span class="table-number">{{ $table->table_number ?? $table->name }}</span>
                                    </div>
                                    <!-- Chairs based on capacity - occupied chairs marked differently -->
                                    @for ($i = 0; $i < min($table->capacity, 8); $i++)
                                        <div class="chair chair-{{ $i + 1 }} {{ $i < ($table->occupied_seats ?? 0) ? 'chair-occupied' : '' }}"></div>
                                    @endfor
                                </div>
                                <div class="table-info">
                                    <strong>{{ $table->name }}</strong>
                                    @if($isPartial)
                                        <small class="d-block text-warning">
                                            <i class="fas fa-chair"></i> {{ $availableSeats }}/{{ $table->capacity }} {{ __('seats free') }}
                                        </small>
                                    @elseif($isFullyOccupied)
                                        <small class="d-block text-danger">
                                            <i class="fas fa-ban"></i> {{ __('Fully occupied') }}
                                        </small>
                                    @else
                                        <small class="d-block text-success">
                                            <i class="fas fa-users"></i> {{ $table->capacity }} {{ __('seats') }}
                                        </small>
                                    @endif
                                    @if($table->activeOrders && $table->activeOrders->count() > 0)
                                        <small class="d-block text-info">
                                            <i class="fas fa-receipt"></i> {{ $table->activeOrders->count() }} {{ __('active order(s)') }}
                                        </small>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if(count($availableTables ?? []) === 0)
                        <div class="text-center py-5">
                            <i class="fas fa-chair fa-3x text-muted mb-3"></i>
                            <p class="text-muted">{{ __('No tables available. Please add tables first.') }}</p>
                            <a href="{{ route('admin.tables.index') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>{{ __('Add Tables') }}
                            </a>
                        </div>
                    @endif
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createTableModal">
                        <i class="fas fa-plus me-2"></i>{{ __('New Table') }}
                    </button>
                    <div>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="button" class="btn btn-primary" id="confirmTableSelection" disabled>
                            <i class="fas fa-check me-2"></i>{{ __('Confirm Selection') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Table Modal -->
    <div class="modal fade" id="createTableModal" tabindex="-1" role="dialog" aria-labelledby="createTableModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="createTableModalLabel">
                        <i class="bx bx-plus-circle me-2"></i>{{ __('Create New Table') }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="quickCreateTableForm">
                        <div class="mb-3">
                            <label for="newTableName" class="form-label fw-bold">
                                {{ __('Table Name') }} <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="newTableName" placeholder="{{ __('e.g., Table 10, VIP 1') }}" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="newTableNumber" class="form-label fw-bold">
                                    {{ __('Table Number') }}
                                </label>
                                <input type="text" class="form-control" id="newTableNumber" placeholder="{{ __('e.g., T10, V1') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="newTableCapacity" class="form-label fw-bold">
                                    {{ __('Capacity') }} <span class="text-danger">*</span>
                                </label>
                                <input type="number" class="form-control" id="newTableCapacity" value="4" min="1" max="20" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="newTableShape" class="form-label fw-bold">
                                    {{ __('Shape') }}
                                </label>
                                <select class="form-select" id="newTableShape">
                                    <option value="square">{{ __('Square') }}</option>
                                    <option value="round">{{ __('Round') }}</option>
                                    <option value="rectangle">{{ __('Rectangle') }}</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="newTableLocation" class="form-label fw-bold">
                                    {{ __('Location') }}
                                </label>
                                <input type="text" class="form-control" id="newTableLocation" placeholder="{{ __('e.g., Main Hall, Patio') }}">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="button" class="btn btn-success" id="saveNewTable">
                        <i class="fas fa-save me-2"></i>{{ __('Create & Select') }}
                    </button>
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

    <!-- Start Dine-In Order Modal -->
    <div class="modal fade" id="startDineInModal" tabindex="-1" role="dialog" aria-labelledby="startDineInModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow-lg dine-in-modal">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="bx bx-restaurant me-2"></i>{{ __('Start Dine-In Order') }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-0">
                    <!-- Selected Table Card -->
                    <div class="dine-in-table-card">
                        <div class="table-visual-mini" id="dineInTableVisual">
                            <span id="dineInTableNumber">T1</span>
                        </div>
                        <div class="table-details">
                            <h6 id="dineInTableName">Table Name</h6>
                            <div class="table-meta">
                                <span class="meta-item">
                                    <i class="fas fa-chair"></i>
                                    <span id="dineInTableCapacity">0</span> {{ __('seats') }}
                                </span>
                                <span class="meta-item" id="dineInAvailableSeats">
                                    <i class="fas fa-check-circle text-success"></i>
                                    {{ __('Available') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Form Content -->
                    <div class="dine-in-form-content">
                        <!-- Guest Count -->
                        <div class="form-section">
                            <label class="section-label">
                                <i class="fas fa-users"></i>
                                {{ __('Number of Guests') }}
                                <span class="text-danger">*</span>
                            </label>
                            <div class="guest-count-selector">
                                <button type="button" class="guest-btn minus" onclick="adjustGuestCount(-1)">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <div class="guest-count-display">
                                    <input type="number" id="dineInGuestCount" value="1" min="1" max="20" readonly>
                                    <small>{{ __('guests') }}</small>
                                </div>
                                <button type="button" class="guest-btn plus" onclick="adjustGuestCount(1)">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                            <small class="text-muted d-block text-center mt-2" id="guestCountHint"></small>
                        </div>

                        <!-- Waiter Selection -->
                        <div class="form-section">
                            <label class="section-label">
                                <i class="fas fa-user-tie"></i>
                                {{ __('Assign Waiter') }}
                                <span class="text-muted fw-normal">({{ __('Optional') }})</span>
                            </label>
                            <select class="form-control select2" id="dineInWaiter">
                                <option value="">{{ __('-- Select Waiter --') }}</option>
                                @foreach($waiters as $waiter)
                                    <option value="{{ $waiter->id }}" data-image="{{ $waiter->image ? asset($waiter->image) : '' }}">
                                        {{ $waiter->name }} @if($waiter->designation) ({{ $waiter->designation }}) @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Order Note -->
                        <div class="form-section">
                            <label class="section-label">
                                <i class="fas fa-comment-alt"></i>
                                {{ __('Order Note') }}
                                <span class="text-muted fw-normal">({{ __('Optional') }})</span>
                            </label>
                            <textarea class="form-control" id="dineInNote" rows="2" placeholder="{{ __('Any special instructions or requests...') }}"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="dine-in-modal-footer">
                    <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">
                        {{ __('Cancel') }}
                    </button>
                    <button type="button" class="btn btn-start-order" id="confirmStartDineIn">
                        <i class="fas fa-play-circle me-2"></i>
                        {{ __('Start Order') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
    /* Dine-In Modal Styles */
    .dine-in-modal {
        border-radius: 16px;
        overflow: hidden;
    }

    /* Table Card */
    .dine-in-table-card {
        background: linear-gradient(135deg, #f8f9ff 0%, #f0f1ff 100%);
        padding: 20px 24px;
        display: flex;
        align-items: center;
        gap: 16px;
        border-bottom: 1px solid #e8e8e8;
    }

    .table-visual-mini {
        width: 60px;
        height: 60px;
        background: linear-gradient(145deg, #8B4513, #A0522D);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 12px rgba(139, 69, 19, 0.3);
    }

    .table-visual-mini span {
        color: #fff;
        font-weight: 700;
        font-size: 16px;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
    }

    .table-details h6 {
        margin: 0 0 6px 0;
        font-weight: 700;
        font-size: 16px;
        color: #333;
    }

    .table-meta {
        display: flex;
        gap: 16px;
    }

    .table-meta .meta-item {
        font-size: 13px;
        color: #666;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .table-meta .meta-item i {
        font-size: 12px;
    }

    /* Form Content */
    .dine-in-form-content {
        padding: 24px;
    }

    .form-section {
        margin-bottom: 24px;
    }

    .form-section:last-child {
        margin-bottom: 0;
    }

    .section-label {
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 600;
        font-size: 14px;
        color: #333;
        margin-bottom: 12px;
    }

    .section-label i {
        color: #696cff;
        font-size: 14px;
    }

    /* Guest Count Selector */
    .guest-count-selector {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 20px;
        background: #f8f9fa;
        padding: 16px;
        border-radius: 12px;
    }

    .guest-btn {
        width: 48px;
        height: 48px;
        border: 2px solid #e0e0e0;
        background: #fff;
        border-radius: 12px;
        font-size: 18px;
        color: #666;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .guest-btn:hover {
        border-color: #696cff;
        color: #696cff;
        background: #f8f9ff;
    }

    .guest-btn:active {
        transform: scale(0.95);
    }

    .guest-btn.minus:hover {
        border-color: #dc3545;
        color: #dc3545;
        background: #fff5f5;
    }

    .guest-btn.plus:hover {
        border-color: #28a745;
        color: #28a745;
        background: #f5fff5;
    }

    .guest-count-display {
        text-align: center;
        min-width: 80px;
    }

    .guest-count-display input {
        width: 60px;
        height: 50px;
        border: none;
        background: transparent;
        text-align: center;
        font-size: 32px;
        font-weight: 700;
        color: #333;
        padding: 0;
    }

    .guest-count-display input:focus {
        outline: none;
    }

    .guest-count-display small {
        display: block;
        color: #888;
        font-size: 12px;
        margin-top: -5px;
    }

    /* Select2 styling for this modal */
    #startDineInModal .select2-container--default .select2-selection--single {
        height: 46px;
        border: 2px solid #e0e0e0;
        border-radius: 10px;
        padding: 8px 12px;
    }

    #startDineInModal .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 28px;
        padding-left: 0;
    }

    #startDineInModal .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 44px;
    }

    #startDineInModal textarea.form-control {
        border: 2px solid #e0e0e0;
        border-radius: 10px;
        padding: 12px;
        resize: none;
    }

    #startDineInModal textarea.form-control:focus {
        border-color: #696cff;
        box-shadow: 0 0 0 3px rgba(105,108,255,0.1);
    }

    /* Footer */
    .dine-in-modal-footer {
        padding: 16px 24px;
        background: #f8f9fa;
        display: flex;
        gap: 12px;
        justify-content: flex-end;
    }

    .dine-in-modal-footer .btn-cancel {
        padding: 12px 24px;
        border: 2px solid #e0e0e0;
        background: #fff;
        color: #666;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.2s;
    }

    .dine-in-modal-footer .btn-cancel:hover {
        border-color: #ccc;
        background: #f5f5f5;
    }

    .dine-in-modal-footer .btn-start-order {
        padding: 12px 28px;
        background: linear-gradient(135deg, #696cff 0%, #5a5ee0 100%);
        border: none;
        color: #fff;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.2s;
    }

    .dine-in-modal-footer .btn-start-order:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(105,108,255,0.4);
    }

    /* Responsive */
    @media (max-width: 576px) {
        .dine-in-table-card {
            padding: 16px 20px;
        }

        .table-visual-mini {
            width: 50px;
            height: 50px;
        }

        .dine-in-form-content {
            padding: 20px;
        }

        .guest-count-selector {
            gap: 15px;
            padding: 12px;
        }

        .guest-btn {
            width: 42px;
            height: 42px;
        }

        .guest-count-display input {
            font-size: 28px;
        }

        .dine-in-modal-footer {
            padding: 16px 20px;
        }

        .dine-in-modal-footer .btn {
            flex: 1;
        }
    }
    </style>

    <!-- POS Receipt Modal -->
    <div class="modal fade" id="posReceiptModal" tabindex="-1" role="dialog" aria-labelledby="posReceiptModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 400px;">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white py-2">
                    <h5 class="modal-title" id="posReceiptModalLabel">
                        <i class="bx bx-check-circle me-2"></i>{{ __('Payment Successful') }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0" id="pos-receipt-body">
                    <!-- Receipt will be loaded here -->
                </div>
                <div class="modal-footer justify-content-between py-2">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>{{ __('Close') }}
                    </button>
                    <div>
                        <button type="button" class="btn btn-info me-2" onclick="printPosReceipt()">
                            <i class="fas fa-print me-1"></i>{{ __('Print') }}
                        </button>
                        <a type="button" class="btn btn-primary receipt-full-invoice" href="" target="_blank">
                            <i class="fas fa-file-invoice me-1"></i>{{ __('Full Invoice') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- New Payment System Modals --}}
    @include('pos::modals.dine-in-setup')
    @include('pos::modals.takeaway-setup')
    @include('pos::modals.payment', ['accounts' => $accounts ?? collect(), 'setting' => $setting ?? null])
    @include('pos::modals.running-order-payment', ['accounts' => $accounts ?? collect(), 'setting' => $setting ?? null])

@endsection

@push('js')
    <script src="{{ asset('backend/js/jquery-ui.min.js') }}"></script>
    <script>
        // Currency icon for JS usage
        var currencyIcon = '{{ currency_icon() }}';

        // POS Settings
        var posSettings = {
            show_customer: {{ $posSettings->show_customer ? 'true' : 'false' }},
            show_discount: {{ $posSettings->show_discount ? 'true' : 'false' }},
            show_barcode: {{ $posSettings->show_barcode ? 'true' : 'false' }},
            is_printable: {{ $posSettings->is_printable ? 'true' : 'false' }},
            merge_cart_items: {{ $posSettings->merge_cart_items ? 'true' : 'false' }}
        };

        $("[name='due_date']").datepicker('destroy');
        // load products
        (function($) {
            "use strict";
            $(document).ready(function() {
                totalSummery();
                loadProudcts();

                // POS Item Tab Switching Handler
                $('#posItemTabs button[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
                    var targetTab = $(e.target).attr('id');
                    var searchInput = $('#name');
                    var searchContainer = $('#searchFilterContainer');
                    var categoryContainer = $('#categoryFilterContainer');

                    if (targetTab === 'combo-tab') {
                        // Switched to Combo tab
                        $('#search_type').val('combo');
                        searchInput.attr('placeholder', "{{ __('Search combos...') }}");
                        categoryContainer.hide();
                        // Make search bar full width
                        searchContainer.removeClass('col-7').addClass('col-12');
                    } else {
                        // Switched to Regular tab
                        $('#search_type').val('regular');
                        searchInput.attr('placeholder', "{{ __('Search menu items...') }}");
                        categoryContainer.show();
                        // Reset search bar width
                        searchContainer.removeClass('col-12').addClass('col-7');
                    }

                    // Clear search and reload
                    searchInput.val('');
                    $('#category_id').val('').trigger('change');
                });

                // Handle search input changes with debounce
                var searchTimeout;
                $('#name').on('input', function() {
                    var searchValue = $(this).val();
                    var searchType = $('#search_type').val();

                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(function() {
                        if (searchValue.length >= 2 || searchValue.length === 0) {
                            performTabSearch();
                        }
                    }, 300);
                });

                // Handle category change
                $('#category_id').on('change', function() {
                    performTabSearch();
                });

                // Tab-aware search function
                function performTabSearch() {
                    var searchType = $('#search_type').val();
                    var searchName = $('#name').val();
                    var categoryId = $('#category_id').val();

                    $('.preloader_area').removeClass('d-none');

                    $.ajax({
                        type: 'get',
                        url: "{{ route('admin.load-products') }}",
                        data: {
                            name: searchName,
                            category_id: categoryId,
                            search_type: searchType
                        },
                        success: function(response) {
                            if (searchType === 'combo') {
                                $(".combo_body").html(response.comboView);
                            } else {
                                $("#products .product_body").html(response.productView);
                                $("#favoriteProducts .product_body").html(response.favProductView);
                            }
                            $('.preloader_area').addClass('d-none');
                        },
                        error: function() {
                            toastr.error("{{ __('Search error occurred') }}");
                            $('.preloader_area').addClass('d-none');
                        }
                    });
                }

                // Barcode Scanner Handler
                @if($posSettings->show_barcode)
                var barcodeBuffer = '';
                var barcodeTimeout;

                $('#barcode_input').on('keypress', function(e) {
                    if (e.which === 13) { // Enter key
                        e.preventDefault();
                        var barcode = $(this).val().trim();
                        if (barcode) {
                            searchAndAddByBarcode(barcode);
                            $(this).val('');
                        }
                    }
                });

                function searchAndAddByBarcode(barcode) {
                    $.ajax({
                        type: 'get',
                        url: "{{ route('admin.load-products') }}",
                        data: { name: barcode },
                        success: function(response) {
                            // Parse the response to find if there's an exact SKU match
                            var $html = $(response);
                            var $item = $html.find('.product-item[data-sku="' + barcode + '"]').first();

                            if ($item.length > 0) {
                                // Found exact match, add to cart
                                var itemId = $item.data('id');
                                addToCart(itemId);
                            } else {
                                // No exact match, try first item or show message
                                var $firstItem = $html.find('.product-item').first();
                                if ($firstItem.length > 0) {
                                    var itemId = $firstItem.data('id');
                                    addToCart(itemId);
                                } else {
                                    toastr.warning("{{ __('No item found with this barcode') }}");
                                }
                            }
                            // Refocus the barcode input
                            $('#barcode_input').focus();
                        },
                        error: function() {
                            toastr.error("{{ __('Error searching for item') }}");
                            $('#barcode_input').focus();
                        }
                    });
                }

                function addToCart(itemId) {
                    $.ajax({
                        type: 'get',
                        url: "{{ route('admin.add-to-cart') }}",
                        data: { menu_item_id: itemId, qty: 1, serviceType: 'menu_item' },
                        success: function(response) {
                            $(".product-table-container").html(response);
                            totalSummery();
                            toastr.success("{{ __('Item added to cart') }}");
                        },
                        error: function() {
                            toastr.error("{{ __('Error adding item to cart') }}");
                        }
                    });
                }
                @endif
                $("#flatpickr-date,[name='sale_date']").flatpickr({
                    dateFormat: "d-m-Y",
                });

                // Order Type Switching
                $('input[name="order_type_radio"]').on('change', function() {
                    var orderType = $(this).val();
                    if (orderType === 'dine_in') {
                        $('#tableSelectionRow').removeClass('d-none');
                        $('#customerSelectionRow').addClass('d-none');
                        $('#customerLoyaltyRow').addClass('d-none');
                        // Reset customer to walk-in for dine-in
                        $('#customer_id').val('walk-in-customer').trigger('change');
                    } else { // take_away
                        $('#tableSelectionRow').addClass('d-none');
                        $('#customerSelectionRow').removeClass('d-none');
                        clearTableSelection();
                    }
                    updatePaymentButtonState();
                });

                // Update payment button based on table selection
                $('#table_id').on('change', function() {
                    updatePaymentButtonState();
                });

                // Make updatePaymentButtonState globally accessible
                window.updatePaymentButtonState = function() {
                    var orderType = $('input[name="order_type_radio"]:checked').val();
                    var tableId = $('#table_id').val();

                    if (orderType === 'dine_in' && tableId) {
                        $('#paymentBtnText').html('<i class="fas fa-play-circle me-1"></i>{{ __("Start Order") }}');
                        $('.payment-btn').removeClass('btn-warning').addClass('btn-success');
                    } else if (orderType === 'dine_in') {
                        $('#paymentBtnText').html('<i class="fas fa-chair me-1"></i>{{ __("Select Table First") }}');
                        $('.payment-btn').removeClass('btn-success').addClass('btn-warning');
                    } else {
                        $('#paymentBtnText').html('<i class="fas fa-credit-card me-1"></i>{{ __("Payment") }}');
                        $('.payment-btn').removeClass('btn-warning btn-success');
                    }
                }

                // Initialize payment button state on page load
                updatePaymentButtonState();

                // Initialize customer selection visibility based on order type
                $('input[name="order_type_radio"]:checked').trigger('change');

                // Table Selection Modal
                $('#openTableModal').on('click', function() {
                    $('#tableSelectionModal').modal('show');
                });

                // Confirm table selection - show Start Dine-In modal
                $('#confirmTableSelection').on('click', function() {
                    const selectedCard = $('.table-card.selected');
                    if (selectedCard.length && selectedTableData) {
                        // Populate Start Dine-In modal
                        $('#dineInTableName').text(selectedTableData.name);
                        $('#dineInTableCapacity').text(selectedTableData.capacity);

                        // Update table number visual
                        const tableNumber = selectedCard.find('.table-number').text() || 'T' + selectedTableData.id;
                        $('#dineInTableNumber').text(tableNumber);

                        // Show available seats if partially occupied
                        if (selectedTableData.occupiedSeats > 0) {
                            $('#dineInAvailableSeats').html('<i class="fas fa-exclamation-circle text-warning"></i> ' + selectedTableData.availableSeats + ' {{ __("available") }}');
                        } else {
                            $('#dineInAvailableSeats').html('<i class="fas fa-check-circle text-success"></i> {{ __("All available") }}');
                        }

                        // Set max guest count based on available seats
                        const maxGuests = selectedTableData.availableSeats;
                        $('#dineInGuestCount').attr('max', maxGuests).val(1);
                        $('#guestCountHint').text("{{ __('Maximum') }}: " + maxGuests + " {{ __('guests') }}");

                        // Reset other fields
                        $('#dineInWaiter').val('');
                        $('#dineInNote').val('');

                        // Hide table selection modal and show Start Dine-In modal
                        $('#tableSelectionModal').modal('hide');
                        setTimeout(function() {
                            $('#startDineInModal').modal('show');
                        }, 300);
                    }
                });

                // Confirm Start Dine-In Order
                $('#confirmStartDineIn').on('click', function() {
                    const tableId = selectedTableData ? selectedTableData.id : $('#table_id').val();
                    const tableName = selectedTableData ? selectedTableData.name : $('#dineInTableName').text();

                    if (!tableId) {
                        toastr.error("{{ __('Please select a table first') }}");
                        return;
                    }

                    const guestCount = parseInt($('#dineInGuestCount').val()) || 1;
                    const maxGuests = selectedTableData ? selectedTableData.availableSeats : parseInt($('#dineInGuestCount').attr('max')) || 20;

                    if (guestCount > maxGuests) {
                        toastr.error("{{ __('Guest count exceeds available seats') }}");
                        return;
                    }

                    // Set hidden input values
                    $('#table_id').val(tableId);
                    $('#guest_count').val(guestCount);
                    $('#waiter_id').val($('#dineInWaiter').val());
                    $('#sale_note').val($('#dineInNote').val());

                    // Update button display
                    $('#selectedTableText').html('<i class="fas fa-check-circle me-1"></i>' + tableName);
                    $('#selectedTableSeats').text(guestCount + ' {{ __("guests") }}');
                    $('#selectedTableBadge').show();
                    $('#openTableModal').addClass('table-selected');

                    // Close modal
                    $('#startDineInModal').modal('hide');

                    // Check if cart has items - if yes, submit order immediately
                    if ($('.product-table tbody > tr').length > 0) {
                        submitDineInOrder(guestCount);
                    } else {
                        // Just save the selection, user will add items then click Payment
                        updatePaymentButtonState();
                        toastr.success("{{ __('Table') }} " + tableName + " {{ __('selected with') }} " + guestCount + " {{ __('guests') }}");
                    }
                });

                // Initialize Select2 for waiter dropdown when modal opens
                var waiterSelect2Initialized = false;
                $('#startDineInModal').on('shown.bs.modal', function() {
                    if (!waiterSelect2Initialized) {
                        // Destroy if already exists
                        if ($('#dineInWaiter').hasClass('select2-hidden-accessible')) {
                            $('#dineInWaiter').select2('destroy');
                        }
                        $('#dineInWaiter').select2({
                            dropdownParent: $('#startDineInModal'),
                            placeholder: "{{ __('-- Select Waiter --') }}",
                            allowClear: true,
                            width: '100%',
                            templateResult: formatWaiterOption,
                            templateSelection: formatWaiterSelection
                        });
                        waiterSelect2Initialized = true;
                    }
                });

                // Format waiter option with image
                function formatWaiterOption(waiter) {
                    if (!waiter.id) return waiter.text;
                    var $option = $(waiter.element);
                    var imageUrl = $option.data('image');
                    if (imageUrl) {
                        return $('<span><img src="' + imageUrl + '" class="rounded-circle me-2" style="width:30px;height:30px;object-fit:cover;" /> ' + waiter.text + '</span>');
                    }
                    return $('<span><i class="fas fa-user-circle me-2 text-muted" style="font-size:24px;"></i> ' + waiter.text + '</span>');
                }

                function formatWaiterSelection(waiter) {
                    if (!waiter.id) return waiter.text;
                    var $option = $(waiter.element);
                    var imageUrl = $option.data('image');
                    if (imageUrl) {
                        return $('<span><img src="' + imageUrl + '" class="rounded-circle me-1" style="width:20px;height:20px;object-fit:cover;" /> ' + waiter.text + '</span>');
                    }
                    return waiter.text;
                }

                // Create New Table
                $('#saveNewTable').on('click', function() {
                    var tableName = $('#newTableName').val().trim();
                    var tableNumber = $('#newTableNumber').val().trim() || tableName;
                    var capacity = parseInt($('#newTableCapacity').val()) || 4;
                    var shape = $('#newTableShape').val();
                    var location = $('#newTableLocation').val().trim();

                    if (!tableName) {
                        toastr.error("{{ __('Please enter a table name') }}");
                        return;
                    }

                    $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>{{ __("Creating...") }}');

                    $.ajax({
                        type: 'POST',
                        url: "{{ route('admin.tables.store') }}",
                        data: {
                            _token: "{{ csrf_token() }}",
                            name: tableName,
                            table_number: tableNumber,
                            capacity: capacity,
                            shape: shape,
                            location: location,
                            status: 'available',
                            is_active: 1
                        },
                        success: function(response) {
                            toastr.success("{{ __('Table created successfully') }}");

                            // Create new table card and add to grid
                            var newTableHtml = `
                                <div class="table-card available"
                                     data-table-id="${response.id || response.table?.id}"
                                     data-table-name="${tableName}"
                                     data-table-capacity="${capacity}"
                                     data-table-available-seats="${capacity}"
                                     data-table-occupied-seats="0"
                                     data-table-status="available"
                                     onclick="selectTable(this)">
                                    <div class="table-shape ${shape} seats-${Math.min(capacity, 8)}">
                                        <div class="table-surface">
                                            <span class="table-number">${tableNumber}</span>
                                        </div>
                                        ${generateChairs(capacity)}
                                    </div>
                                    <div class="table-info">
                                        <strong>${tableName}</strong>
                                        <small class="d-block text-success">
                                            <i class="fas fa-users"></i> ${capacity} {{ __('seats') }}
                                        </small>
                                    </div>
                                </div>
                            `;

                            $('#tablesGrid').append(newTableHtml);

                            // Auto-select the new table
                            var newCard = $('#tablesGrid .table-card').last();
                            selectTable(newCard[0]);

                            // Close create modal
                            $('#createTableModal').modal('hide');
                            $('#quickCreateTableForm')[0].reset();
                            $('#saveNewTable').prop('disabled', false).html('<i class="fas fa-save me-2"></i>{{ __("Create & Select") }}');
                        },
                        error: function(xhr) {
                            toastr.error(xhr.responseJSON?.message || "{{ __('Error creating table') }}");
                            $('#saveNewTable').prop('disabled', false).html('<i class="fas fa-save me-2"></i>{{ __("Create & Select") }}');
                        }
                    });
                });

                // Helper function to generate chair HTML
                function generateChairs(capacity) {
                    var chairs = '';
                    for (var i = 0; i < Math.min(capacity, 8); i++) {
                        chairs += '<div class="chair chair-' + (i + 1) + '"></div>';
                    }
                    return chairs;
                }

                // Adjust guest count with buttons
                window.adjustGuestCount = function(delta) {
                    const input = $('#dineInGuestCount');
                    let value = parseInt(input.val()) || 1;
                    const min = parseInt(input.attr('min')) || 1;
                    const max = parseInt(input.attr('max')) || 20;

                    value += delta;
                    if (value < min) value = min;
                    if (value > max) value = max;

                    input.val(value);
                };

                // Reset selection when modal is closed without confirming
                $('#tableSelectionModal').on('hidden.bs.modal', function() {
                    // Keep selection if already confirmed
                });

                // update pos quantity - real-time without full cart reload
                let qtyUpdateTimeout = null;
                $(document).on("input", ".pos_input_qty", function(e) {
                    let $input = $(this);
                    let quantity = parseInt($input.val());
                    if (quantity < 1 || isNaN(quantity)) {
                        return;
                    }

                    let $row = $input.closest('tr');
                    let $parentTd = $input.parents('td');
                    let rowid = $parentTd.data('rowid');

                    // Get current price from the row
                    let priceText = $row.find('.price span').text();
                    let price = parseFloat(priceText.replace(/[^0-9.]/g, '')) || 0;

                    // Immediately update the row total in UI
                    let newSubTotal = price * quantity;
                    $row.find('.row_total').text(formatCurrency(newSubTotal));

                    // Update totals immediately
                    totalSummery();

                    // Debounce the AJAX call to sync with server
                    clearTimeout(qtyUpdateTimeout);
                    qtyUpdateTimeout = setTimeout(function() {
                        $.ajax({
                            type: 'get',
                            data: {
                                rowid: rowid,
                                quantity: quantity
                            },
                            url: "{{ route('admin.cart-quantity-update-quick') }}",
                            success: function(response) {
                                if (response.success) {
                                    // Update with server-formatted value
                                    $row.find('.row_total').text(response.sub_total_formatted);
                                    totalSummery();
                                }
                            },
                            error: function(response) {
                                // On error, reload cart to restore correct state
                                if (response.status == 500 || response.status == 403) {
                                    toastr.error("{{ __('Error updating quantity') }}");
                                }
                            }
                        });
                    }, 300); // 300ms debounce
                });

                // Format currency helper (client-side)
                function formatCurrency(amount) {
                    return '{{ config("app.currency_symbol", "$") }}' + parseFloat(amount).toFixed(2);
                }

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

                    var searchType = $('#search_type').val();

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

                            // Handle tab-aware search response
                            if (searchType === 'combo') {
                                $(".combo_body").html(response.comboView);
                            } else {
                                if (favorite == 1) {
                                    $("#favoriteProducts .product_body").html(response.favProductView || response)
                                } else {
                                    $("#products .product_body").html(response.productView || response)
                                }
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

                        let searchType = $('#search_type').val() || 'regular';

                        $.ajax({
                            url: "{{ route('admin.load-products-list') }}?favorite=" +
                                favorite + "&search_type=" + searchType,
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
                                } else {
                                    $('#itemList').removeClass('show');
                                    $('#favoriteItemList').removeClass('show');
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
                // search products (handled by performTabSearch for category_id)

                $("#brand_id").on('change', function() {
                    const category_id = $('#category_id').val();
                    const brand = $('#brand_id').val();
                    const name = $('#name').val();
                    const searchType = $('#search_type').val();

                    loadProudcts({
                        category_id,
                        brand,
                        name,
                        search_type: searchType
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
                $(document).on('click', '.add-payment', function() {
                    const row = `@include('pos::payment-row', ['add' => true])`;
                    $('#paymentRow').append(row);
                    $('[name="payment_type[]"]').niceSelect();
                });

                $(document).on('click', '.remove-payment', function() {
                    $(this).closest('.payment-row').remove();
                    calDue();
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

        // Open addon modal for cart item
        function openAddonModal(rowId, menuItemId) {
            $('#addon_cart_rowid').val(rowId);
            $('#addon_menu_item_id').val(menuItemId);
            $('#addon-list-container').html('<div class="text-center py-3"><i class="fas fa-spinner fa-spin fa-2x"></i></div>');
            $('#cartAddonModal').modal('show');

            // Fetch available addons for this menu item
            $.ajax({
                type: 'get',
                url: "{{ url('admin/pos/get-item-addons') }}" + "/" + menuItemId + "/" + rowId,
                success: function(response) {
                    $('#addon-list-container').html(response.html);
                },
                error: function(response) {
                    $('#addon-list-container').html('<p class="text-danger text-center">{{ __("Error loading add-ons") }}</p>');
                    toastr.error("{{ __('Error loading add-ons') }}");
                }
            });
        }

        // Save selected addons to cart item
        function saveCartAddons() {
            const rowId = $('#addon_cart_rowid').val();
            const addonIds = [];
            const addonQtys = {};
            $('.cart-addon-checkbox:checked').each(function() {
                const addonId = $(this).val();
                const qty = $(this).closest('.form-check').find('.addon-qty-input').val() || 1;
                addonIds.push(addonId);
                addonQtys[addonId] = parseInt(qty);
            });

            $.ajax({
                type: 'post',
                url: "{{ url('admin/pos/update-cart-addons') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    rowid: rowId,
                    addons: addonIds,
                    addon_qtys: addonQtys
                },
                success: function(response) {
                    $(".product-table-container").html(response);
                    totalSummery();
                    $('#cartAddonModal').modal('hide');
                    toastr.success("{{ __('Add-ons updated successfully') }}");
                },
                error: function(response) {
                    toastr.error("{{ __('Error updating add-ons') }}");
                }
            });
        }

        // Update addon quantity in cart
        function updateAddonQty(rowId, addonId, qty) {
            if (qty < 1) qty = 1;
            $.ajax({
                type: 'post',
                url: "{{ url('admin/pos/update-addon-qty') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    rowid: rowId,
                    addon_id: addonId,
                    qty: qty
                },
                success: function(response) {
                    $(".product-table-container").html(response);
                    totalSummery();
                },
                error: function(response) {
                    toastr.error("{{ __('Error updating add-on quantity') }}");
                }
            });
        }

        // Remove addon from cart item
        function removeAddon(rowId, addonId) {
            $.ajax({
                type: 'post',
                url: "{{ url('admin/pos/remove-addon') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    rowid: rowId,
                    addon_id: addonId
                },
                success: function(response) {
                    $(".product-table-container").html(response);
                    totalSummery();
                    toastr.success("{{ __('Add-on removed') }}");
                },
                error: function(response) {
                    toastr.error("{{ __('Error removing add-on') }}");
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

            let tax = parseFloat($('#tax_fee').val()) || 0;
            let discount = parseFloat($('#discount').val()) || 0;
            let total = parseFloat($('#total_fee').val()) || 0;

            let discountType = $('[name="discount_type"]').val();

            if (discountType === 'percent') {
                discount = subTotal * (discount / 100);
            }

            // Calculate the total
            total = subTotal + tax - discount;

            // Update the total field with the calculated value
            $('#total_fee').val(total.toFixed(2));

            $('[name="order_sub_total"]').val(subTotal);
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
                    $(".combo_body").html(response.comboView)
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
                    $(".combo_body").html(response.comboView)
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

            let grandTotal = parseFloat(finalTotal) || 0;
            let itemCount = parseInt(item) || 0;

            // Check if cart has items
            if (itemCount <= 0) {
                toastr.warning("{{ __('Please add items to cart first') }}");
                return;
            }

            // Show loader while preparing payment modal
            $('.preloader_area').removeClass('d-none');

            // Get selected order type
            let orderType = $('input[name="order_type_radio"]:checked').val() || 'dine_in';

            // Route to appropriate setup modal based on order type
            switch(orderType) {
                case 'dine_in':
                    initDineInSetupModal(grandTotal, itemCount);
                    break;
                case 'take_away':
                    initTakeawaySetupModal(grandTotal, itemCount);
                    break;
                default:
                    // Fallback to direct payment modal for unknown types
                    openDirectPaymentModal(grandTotal, itemCount, subTotal, discountAmount);
            }
        }

        // Direct payment modal (bypasses setup modals)
        function openDirectPaymentModal(grandTotal, itemCount, subTotal, discountAmount) {
            $('[name="sub_total"]').val(subTotal);
            $('#sub_totalModal').text(subTotal);

            $('#discount_amountModal').text(discountAmount);
            $('[name="discount_amount"]').val(discountAmount);

            $('#total_amountModal').text(grandTotal);
            $('#total_amount_modal_input').val(grandTotal);
            $('#total_amountModal2').text(grandTotal);

            // load customer info
            let customer_id = $('#customer_id').val();
            $("#order_customer_id").val(customer_id ? customer_id : 'walk-in-customer');
            loadCustomer(customer_id);

            // Set order type and table info
            let orderType = $('input[name="order_type_radio"]:checked').val();
            $('#order_type').val(orderType);
            $('#order_table_id').val($('#table_id').val());

            // Update order type badge
            updateOrderTypeBadge();

            // total items
            $('#itemModal').text(itemCount);

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

        function singleAddToCart(id, serviceType = 'service') {
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

        function addMenuItemToCart(id) {
            $('.preloader_area').removeClass('d-none');
            $.ajax({
                type: 'get',
                data: {
                    menu_item_id: id,
                    type: 'single',
                    serviceType: 'menu_item'
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

        function addComboToCart(id) {
            $('.preloader_area').removeClass('d-none');
            $.ajax({
                type: 'get',
                data: {
                    combo_id: id,
                    type: 'single',
                    serviceType: 'combo'
                },
                url: "{{ url('/admin/pos/add-to-cart') }}",
                success: function(response) {
                    $(".product-table-container").html(response)

                    $('[name="source"]').niceSelect();
                    toastr.success("{{ __('Combo added successfully') }}")
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
        // Table Selection Functions
        let selectedTableData = null;

        function selectTable(element) {
            const $card = $(element);
            const status = $card.data('table-status');
            const availableSeats = parseInt($card.data('table-available-seats')) || 0;

            // Don't allow selecting tables with no available seats
            if (availableSeats <= 0) {
                toastr.warning("{{ __('This table has no available seats') }}");
                return;
            }

            // Don't allow reserved or maintenance tables
            if (status === 'reserved' || status === 'maintenance') {
                toastr.warning("{{ __('This table is not available') }}");
                return;
            }

            // Remove previous selection
            $('.table-card').removeClass('selected');

            // Add selection to clicked card
            $card.addClass('selected');

            // Enable confirm button
            $('#confirmTableSelection').prop('disabled', false);

            // Store selected data
            selectedTableData = {
                id: $card.data('table-id'),
                name: $card.data('table-name'),
                capacity: $card.data('table-capacity'),
                availableSeats: availableSeats,
                occupiedSeats: parseInt($card.data('table-occupied-seats')) || 0,
                status: status
            };
        }

        // Double-click to quickly select table
        $(document).on('dblclick', '.table-card', function() {
            const availableSeats = parseInt($(this).data('table-available-seats')) || 0;
            if (availableSeats <= 0) return;

            selectTable(this);
            $('#confirmTableSelection').click();
        });

        function clearTableSelection() {
            $('#table_id').val('');
            $('#selectedTableText').html("{{ __('Click to Select Table') }}");
            $('#selectedTableBadge').hide();
            $('#openTableModal').removeClass('table-selected');
            $('.table-card').removeClass('selected');
            $('#confirmTableSelection').prop('disabled', true);
            selectedTableData = null;
            updatePaymentButtonState();
        }

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

        // Quick amount buttons
        $(document).on('click', '.quick-amount-btn', function() {
            const amount = parseFloat($(this).data('amount'));
            const currentAmount = parseFloat($('.receive_cash').val()) || 0;
            $('.receive_cash').val(currentAmount + amount).trigger('input');
        });

        // Set exact amount
        function setExactAmount() {
            const totalAmount = parseFloat($('#total_amount_modal_input').val()) || 0;
            $('.receive_cash').val(totalAmount).trigger('input');
            // Also set the paying amount
            $('.paying_amount').first().val(totalAmount).trigger('keyup');
        }

        // Update order type badge in modal
        function updateOrderTypeBadge() {
            const orderType = $('#order_type').val();
            const badge = $('#orderTypeBadge');
            const startBtn = $('#startOrderBtn');

            switch(orderType) {
                case 'dine_in':
                    badge.text("{{ __('Dine-in') }}").removeClass('bg-success bg-info').addClass('bg-primary');
                    startBtn.show();
                    break;
                case 'take_away':
                    badge.text("{{ __('Take Away') }}").removeClass('bg-primary bg-info').addClass('bg-success');
                    startBtn.show();
                    break;
                default:
                    badge.text("{{ __('Order') }}").removeClass('bg-primary bg-success bg-info').addClass('bg-secondary');
                    startBtn.hide();
            }
        }

        // Start running order (deferred payment)
        function startRunningOrder() {
            if ($('.product-table tbody > tr').length == 0) {
                toastr.error("{{ __('Cart is empty') }}");
                return;
            }

            // Set defer_payment flag
            const formData = $('#checkoutForm').serialize() + '&defer_payment=1';

            $.ajax({
                type: 'POST',
                data: formData,
                url: "{{ route('admin.place-order') }}",
                success: function(response) {
                    console.log(response);
                    if (response.is_deferred) {
                        toastr.success(response.message);
                        modalHide('#payment-modal');

                        // Clear the cart (same as successful order)
                        $(".product-table tbody").html('');
                        $('#titems').text(0);
                        $('#discount_total_amount').val(0);
                        $('#tds').text(0);
                        totalSummery();

                        // Reset customer select
                        $("#customer_id").val('').trigger('change');

                        // Reset discount
                        $('#discount_type').val(0).trigger('change');
                        $('.dis-form').hide();

                        // Reset payment row
                        $('#paymentRow').html(`@include('pos::payment-row')`);
                        $('[name="payment_type[]"]').niceSelect();

                        // Reset form
                        $('#checkoutForm').trigger('reset');

                        // Refresh running orders
                        loadRunningOrdersCount();
                        loadRunningOrders();

                        // Reset table selection if dine-in
                        if (selectedTableData) {
                            selectedTableData = null;
                            $('#selected_table_display').addClass('d-none');
                            $('#table_id').val('');
                            refreshAvailableTables();
                        }
                    } else {
                        toastr.error(response.message || "{{ __('Error starting order') }}");
                    }
                },
                error: function(xhr) {
                    console.log(xhr);
                    toastr.error(xhr.responseJSON?.message || "{{ __('Error starting order') }}");
                }
            });
        }

        // Update badge when payment modal opens
        $('#payment-modal').on('show.bs.modal', function() {
            updateOrderTypeBadge();
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

            // vat/tax - calculate on amount after discount
            const taxRate = parseFloat($('#taxRate').val()) || parseFloat($('#ttax2').text()) || 0;
            const taxableAmount = total - discountAmount;
            let taxAmount = 0;

            if (taxRate > 0) {
                taxAmount = taxableAmount * taxRate / 100;
            }

            $('#totalVat').text(`{{ currency_icon() }}${taxAmount}`)
            $('#taxAmount').val(taxAmount);

            // totalAmountWithVat
            const grandTotal = total - discountAmount + taxAmount
            $('#totalAmountWithVat').text(`{{ currency_icon() }}${grandTotal.toFixed(2)}`)
            $('#finalTotal').text(`{{ currency_icon() }}${grandTotal.toFixed(2)}`)

            // Update summary table displays
            $('#subtotalDisplay').text(`{{ currency_icon() }}${total.toFixed(2)}`);
            $('#subtotal').val(total);

            // Show/hide and update discount row
            if (discountAmount > 0) {
                $('#discountRow').show();
                $('#discountDisplay').text(`- {{ currency_icon() }}${discountAmount.toFixed(2)}`);
            } else {
                $('#discountRow').hide();
            }

            // Always show tax row and update values
            $('#taxRateDisplay').text(taxRate);
            $('#taxDisplay').text(`{{ currency_icon() }}${taxAmount.toFixed(2)}`);

            calculateExtra()

            // products.length
            $('#titems').text(products.length)

            // Update checkout/start order button states
            updateCheckoutButtonState(products.length);
        }

        // Update checkout button state based on cart items
        function updateCheckoutButtonState(itemCount) {
            const hasItems = itemCount > 0;
            $('#checkout, #startOrderBtn').prop('disabled', !hasItems);
            if (hasItems) {
                $('#checkout, #startOrderBtn').removeClass('disabled');
            } else {
                $('#checkout, #startOrderBtn').addClass('disabled');
            }
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

        // Running Orders Functions
        let currentEditingOrderId = null;

        function openRunningOrders() {
            $('#running-orders-modal').modal('show');
            $('#running-orders-search').val('');
            $('#ro-search-clear').addClass('d-none');
            $('.ro-search-kbd').removeClass('d-none');
            loadRunningOrders();
        }

        var currentRunningOrdersPage = 1;
        var roSearchTimer = null;
        var roSearchXhr = null;

        // Keyboard shortcut: Ctrl+F focuses search when modal is open
        $(document).on('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'f' && $('#running-orders-modal').hasClass('show')) {
                e.preventDefault();
                $('#running-orders-search').focus();
            }
        });

        // Clear button
        $(document).on('click', '#ro-search-clear', function() {
            $('#running-orders-search').val('').focus();
            $(this).addClass('d-none');
            $('.ro-search-kbd').removeClass('d-none');
            // Show all cards instantly
            $('.running-order-col').removeClass('running-order-col-hidden');
            loadRunningOrders(1);
        });

        // Search input handler — instant client-side + debounced server
        $(document).on('input', '#running-orders-search', function() {
            var val = $(this).val().trim();

            // Toggle clear button / kbd hint
            if (val.length > 0) {
                $('#ro-search-clear').removeClass('d-none');
                $('.ro-search-kbd').addClass('d-none');
            } else {
                $('#ro-search-clear').addClass('d-none');
                $('.ro-search-kbd').removeClass('d-none');
            }

            // Instant client-side filter on visible cards
            var term = val.toLowerCase();
            if (term.length > 0) {
                $('.running-order-col').each(function() {
                    var data = $(this).data('search') || '';
                    $(this).toggleClass('running-order-col-hidden', data.indexOf(term) === -1);
                });
            } else {
                $('.running-order-col').removeClass('running-order-col-hidden');
            }

            // Debounced server search for full results
            clearTimeout(roSearchTimer);
            if (roSearchXhr) roSearchXhr.abort();
            roSearchTimer = setTimeout(function() {
                loadRunningOrders(1);
            }, 400);
        });

        function loadRunningOrders(page = 1) {
            currentRunningOrdersPage = page;
            var search = ($('#running-orders-search').val() || '').trim();

            // Show spinner only when no cards are visible
            if ($('.running-order-col:not(.running-order-col-hidden)').length === 0) {
                $('#running-orders-content').html(`
                    <div class="text-center py-5">
                        <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                        <p class="mt-2 text-muted mb-0" style="font-size:13px">{{ __('Searching...') }}</p>
                    </div>
                `);
            }

            // Show inline spinner
            $('#ro-search-spinner').removeClass('d-none');

            if (roSearchXhr) roSearchXhr.abort();
            roSearchXhr = $.ajax({
                type: 'GET',
                url: "{{ route('admin.pos.running-orders') }}",
                data: { page: page, search: search },
                success: function(response) {
                    if (response.success) {
                        $('#running-orders-content').html(response.html);
                        if (!search) {
                            updateRunningOrdersCount(response.count);
                        }
                    } else {
                        $('#running-orders-content').html(`
                            <div class="alert alert-danger">
                                {{ __('Error loading running orders') }}
                            </div>
                        `);
                    }
                },
                error: function(xhr) {
                    if (xhr.statusText === 'abort') return;
                    $('#running-orders-content').html(`
                        <div class="alert alert-danger">
                            {{ __('Server error occurred') }}
                        </div>
                    `);
                },
                complete: function() {
                    $('#ro-search-spinner').addClass('d-none');
                }
            });
        }

        function updateRunningOrdersCount(count) {
            const badge = $('.running-orders-count');
            if (count > 0) {
                badge.text(count).removeClass('d-none');
            } else {
                badge.addClass('d-none');
            }
        }

        function loadRunningOrdersCount() {
            $.ajax({
                type: 'GET',
                url: "{{ route('admin.pos.running-orders.count') }}",
                success: function(response) {
                    if (response.success) {
                        updateRunningOrdersCount(response.count);
                    }
                }
            });
        }

        // Refresh available tables (for real-time seat updates)
        function refreshAvailableTables() {
            $.ajax({
                type: 'GET',
                url: "{{ route('admin.pos.available-tables') }}",
                success: function(response) {
                    if (response.success) {
                        $('#tablesGrid').html(response.html);
                    }
                },
                error: function() {
                    console.log('Error refreshing tables');
                }
            });
        }

        // Load specific page of running orders
        function loadRunningOrdersPage(page) {
            if (page < 1) return;
            loadRunningOrders(page);
        }

        function viewOrderDetails(orderId) {
            $('#running-orders-modal').modal('hide');
            $('#order-details-modal').modal('show');
            currentEditingOrderId = orderId;

            $('#order-details-content').html(`
                <div class="text-center py-5">
                    <i class="fas fa-spinner fa-spin fa-3x text-primary"></i>
                </div>
            `);

            $.ajax({
                type: 'GET',
                url: "{{ url('admin/pos/running-orders') }}/" + orderId + "/details",
                success: function(response) {
                    if (response.success) {
                        $('#order-details-content').html(response.html);
                    } else {
                        $('#order-details-content').html(`
                            <div class="alert alert-danger">
                                {{ __('Order not found') }}
                            </div>
                        `);
                    }
                },
                error: function() {
                    $('#order-details-content').html(`
                        <div class="alert alert-danger">
                            {{ __('Server error occurred') }}
                        </div>
                    `);
                }
            });
        }

        // Back to running orders list from order details
        function backToRunningOrders() {
            $('#order-details-modal').modal('hide');
            $('#running-orders-modal').modal('show');
            loadRunningOrders(currentRunningOrdersPage);
        }

        // Update item quantity in running order
        function updateItemQty(orderId, detailId, change) {
            const qtyInput = $(`input.item-qty[data-detail-id="${detailId}"]`);
            let newQty = parseInt(qtyInput.val()) + change;
            if (newQty < 1) newQty = 1;

            updateItemQtyDirect(orderId, detailId, newQty);
        }

        function updateItemQtyDirect(orderId, detailId, quantity) {
            quantity = Math.max(1, parseInt(quantity));
            const qtyInput = $(`input.item-qty[data-detail-id="${detailId}"]`);
            qtyInput.val(quantity);

            $.ajax({
                type: 'POST',
                url: "{{ url('admin/pos/running-orders') }}/" + orderId + "/update-item-qty",
                data: {
                    _token: "{{ csrf_token() }}",
                    detail_id: detailId,
                    quantity: quantity
                },
                success: function(response) {
                    if (response.success) {
                        // Update totals in UI
                        const row = $(`tr[data-detail-id="${detailId}"]`);
                        row.find('.item-total').text(response.new_subtotal);
                        $('#orderSubtotal').text(response.order_subtotal);
                        $('#orderGrandTotal').text(response.order_total);
                        $('#current-order-total').val(response.order_total.replace(/[^0-9.]/g, ''));

                        toastr.success(response.message);
                    } else {
                        toastr.error(response.message);
                        // Reload to get correct state
                        loadOrderDetails(orderId);
                    }
                },
                error: function() {
                    toastr.error("{{ __('Error updating quantity') }}");
                    loadOrderDetails(orderId);
                }
            });
        }

        // Remove item from running order
        function removeOrderItem(orderId, detailId) {
            Swal.fire({
                title: "{{ __('Remove Item?') }}",
                text: "{{ __('Are you sure you want to remove this item from the order?') }}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: "{{ __('Yes, Remove') }}",
                cancelButtonText: "{{ __('Cancel') }}"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'POST',
                        url: "{{ url('admin/pos/running-orders') }}/" + orderId + "/remove-item",
                        data: {
                            _token: "{{ csrf_token() }}",
                            detail_id: detailId
                        },
                        success: function(response) {
                            if (response.success) {
                                if (response.items_remaining === 0) {
                                    // If no items left, offer to cancel the order
                                    Swal.fire({
                                        title: "{{ __('No Items Left') }}",
                                        text: "{{ __('The order has no items. Would you like to cancel it?') }}",
                                        icon: 'question',
                                        showCancelButton: true,
                                        confirmButtonText: "{{ __('Yes, Cancel Order') }}",
                                        cancelButtonText: "{{ __('Keep Order') }}"
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            cancelRunningOrder(orderId);
                                        } else {
                                            loadOrderDetails(orderId);
                                        }
                                    });
                                } else {
                                    // Remove row from table
                                    $(`tr[data-detail-id="${detailId}"]`).fadeOut(300, function() {
                                        $(this).remove();
                                        // Update totals
                                        $('#orderSubtotal').text(response.order_subtotal);
                                        $('#orderGrandTotal').text(response.order_total);
                                    });
                                    toastr.success(response.message);
                                }
                            } else {
                                toastr.error(response.message);
                            }
                        },
                        error: function() {
                            toastr.error("{{ __('Error removing item') }}");
                        }
                    });
                }
            });
        }

        // Reload order details
        function loadOrderDetails(orderId) {
            $.ajax({
                type: 'GET',
                url: "{{ url('admin/pos/running-orders') }}/" + orderId + "/details",
                success: function(response) {
                    if (response.success) {
                        $('#order-details-content').html(response.html);
                    } else {
                        $('#order-details-content').html(response);
                    }
                },
                error: function() {
                    toastr.error("{{ __('Error loading order details') }}");
                }
            });
        }

        function addItemsToOrder(orderId) {
            // Close the details modal first
            $('#order-details-modal').modal('hide');
            $('#running-orders-modal').modal('hide');

            // Store the order ID for later
            currentEditingOrderId = orderId;

            // Show a quick menu selection modal
            Swal.fire({
                title: "{{ __('Add Items to Order') }}",
                html: `
                    <div class="text-start">
                        <p class="text-muted mb-3">{{ __('Select items from the POS to add them to this order.') }}</p>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            {{ __('The page will reload. Add items to cart and click "Update Order" to add them to order #') }}${orderId}
                        </div>
                    </div>
                `,
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: "{{ __('Continue') }}",
                cancelButtonText: "{{ __('Cancel') }}"
            }).then((result) => {
                if (result.isConfirmed) {
                    $('.preloader_area').removeClass('d-none');

                    $.ajax({
                        type: 'POST',
                        url: "{{ url('admin/pos/running-orders') }}/" + orderId + "/load-to-cart",
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            if (response.success) {
                                location.reload();
                            } else {
                                toastr.error(response.message || "{{ __('Error loading order') }}");
                                $('.preloader_area').addClass('d-none');
                            }
                        },
                        error: function() {
                            toastr.error("{{ __('Server error occurred') }}");
                            $('.preloader_area').addClass('d-none');
                        }
                    });
                }
            });
        }

        // Payment modal data
        let paymentOrderTotal = 0;
        let paymentMethodCounter = 0;

        // Available accounts from database
        const availableAccounts = @json($accounts->groupBy('account_type'));

        var paymentSubtotal = 0; // Store original subtotal before discount

        function showPaymentModal(orderId, total, invoice = '', tableName = '') {
            // Close any open modals first
            $('#order-details-modal').modal('hide');

            // Use the new redesigned running order payment modal
            if (typeof openRunningOrderPayment === 'function') {
                openRunningOrderPayment(orderId);
            } else {
                // Fallback to old modal if new one not loaded
                currentEditingOrderId = orderId;
                paymentSubtotal = parseFloat(total);
                paymentOrderTotal = paymentSubtotal;
                paymentMethodCounter = 0;

                // Set order info
                $('#payment-order-id').val(orderId);
                $('#payment-order-invoice').text('#' + (invoice || orderId));
                $('#payment-order-table').text(tableName || '--');
                $('#payment-subtotal-amount').text('{{ currency_icon() }}' + paymentSubtotal.toFixed(2));
                $('#payment-total-amount').text('{{ currency_icon() }}' + paymentOrderTotal.toFixed(2));

                // Reset discount fields
                $('#payment-discount-amount').val(0);
                $('#payment-discount-percent').val(0);

                // Clear and add default payment method
                $('#payment-methods-container').empty();
                addPaymentMethod(paymentOrderTotal);

                // Set received amount
                $('#payment-amount-received').val(paymentOrderTotal);
                calculateChange();

                $('#running-order-payment-modal').modal('show');
            }
        }

        function applyPaymentDiscount() {
            const discountAmount = parseFloat($('#payment-discount-amount').val()) || 0;
            const discountPercent = paymentSubtotal > 0 ? (discountAmount / paymentSubtotal) * 100 : 0;
            $('#payment-discount-percent').val(discountPercent.toFixed(1));
            updatePaymentTotalAfterDiscount(discountAmount);
        }

        function applyPaymentDiscountPercent() {
            const discountPercent = parseFloat($('#payment-discount-percent').val()) || 0;
            const discountAmount = (paymentSubtotal * discountPercent) / 100;
            $('#payment-discount-amount').val(discountAmount.toFixed(2));
            updatePaymentTotalAfterDiscount(discountAmount);
        }

        function updatePaymentTotalAfterDiscount(discountAmount) {
            paymentOrderTotal = Math.max(0, paymentSubtotal - discountAmount);
            $('#payment-total-amount').text('{{ currency_icon() }}' + paymentOrderTotal.toFixed(2));

            // Update first payment method amount
            $('.payment-method-row:first .payment-amount').val(paymentOrderTotal.toFixed(2));
            $('#payment-amount-received').val(paymentOrderTotal.toFixed(2));

            calculateTotalPaying();
            calculateChange();
        }

        function addPaymentMethod(amount = 0) {
            const index = paymentMethodCounter++;
            const accountOptions = buildAccountOptions();

            const html = `
                <div class="payment-method-row mb-3 p-3 border rounded" data-index="${index}">
                    <div class="row align-items-end">
                        <div class="col-md-4">
                            <label class="form-label">{{ __('Payment Type') }}</label>
                            <select class="form-select payment-type-select" name="payment_type[]" data-index="${index}" onchange="onPaymentTypeChange(${index})">
                                <option value="cash">{{ __('Cash') }}</option>
                                <option value="bank">{{ __('Bank Transfer') }}</option>
                                <option value="card">{{ __('Card') }}</option>
                                <option value="mobile_banking">{{ __('Mobile Banking') }}</option>
                            </select>
                        </div>
                        <div class="col-md-3 account-select-col" style="display: none;">
                            <label class="form-label">{{ __('Account') }}</label>
                            <select class="form-select account-select" name="account_id[]" data-index="${index}">
                                ${accountOptions}
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ __('Amount') }}</label>
                            <input type="number" class="form-control payment-amount" name="paying_amount[]" value="${amount}" step="0.01" onchange="calculateTotalPaying()">
                        </div>
                        <div class="col-md-2">
                            ${index > 0 ? `<button type="button" class="btn btn-outline-danger" onclick="removePaymentMethod(${index})"><i class="fas fa-trash"></i></button>` : ''}
                        </div>
                    </div>
                </div>
            `;

            $('#payment-methods-container').append(html);
            onPaymentTypeChange(index);
            calculateTotalPaying();
        }

        function buildAccountOptions() {
            let options = '<option value="">{{ __("Select Account") }}</option>';

            // Bank accounts
            if (availableAccounts.bank) {
                availableAccounts.bank.forEach(acc => {
                    const bankName = acc.bank ? acc.bank.name : '';
                    options += `<option value="${acc.id}" data-type="bank">${bankName} - ${acc.bank_account_number || ''}</option>`;
                });
            }

            // Card accounts
            if (availableAccounts.card) {
                availableAccounts.card.forEach(acc => {
                    options += `<option value="${acc.id}" data-type="card">${acc.card_holder_name || ''} - ${acc.card_type || ''}</option>`;
                });
            }

            // Mobile banking accounts
            if (availableAccounts.mobile_banking) {
                availableAccounts.mobile_banking.forEach(acc => {
                    options += `<option value="${acc.id}" data-type="mobile_banking">${acc.mobile_bank_name || ''} - ${acc.mobile_number || ''}</option>`;
                });
            }

            return options;
        }

        function onPaymentTypeChange(index) {
            const row = $(`.payment-method-row[data-index="${index}"]`);
            const paymentType = row.find('.payment-type-select').val();
            const accountCol = row.find('.account-select-col');
            const accountSelect = row.find('.account-select');

            if (paymentType === 'cash') {
                accountCol.hide();
                accountSelect.val('');
            } else {
                accountCol.show();
                // Filter accounts by type
                accountSelect.find('option').each(function() {
                    const optType = $(this).data('type');
                    if (!optType || optType === paymentType) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
                // Select first visible option
                accountSelect.find('option:visible').first().prop('selected', true);
            }
        }

        function removePaymentMethod(index) {
            $(`.payment-method-row[data-index="${index}"]`).remove();
            calculateTotalPaying();
        }

        function calculateTotalPaying() {
            let total = 0;
            $('.payment-amount').each(function() {
                total += parseFloat($(this).val()) || 0;
            });
            $('#payment-total-paying').val(total.toFixed(2));
            calculateChange();
        }

        function calculateChange() {
            const received = parseFloat($('#payment-amount-received').val()) || 0;
            const totalPaying = parseFloat($('#payment-total-paying').val()) || 0;
            const change = received - paymentOrderTotal;

            if (change >= 0) {
                $('#payment-change-due').text('{{ currency_icon() }}' + change.toFixed(2)).removeClass('text-danger').addClass('text-success');
            } else {
                $('#payment-change-due').text('-{{ currency_icon() }}' + Math.abs(change).toFixed(2)).removeClass('text-success').addClass('text-danger');
            }
        }

        function processRunningOrderPayment() {
            const orderId = $('#payment-order-id').val();
            const totalPaying = parseFloat($('#payment-total-paying').val()) || 0;
            const amountReceived = parseFloat($('#payment-amount-received').val()) || 0;
            const discountAmount = parseFloat($('#payment-discount-amount').val()) || 0;

            if (totalPaying < paymentOrderTotal) {
                toastr.warning("{{ __('Payment amount is less than total. Remaining will be recorded as due.') }}");
            }

            // Collect payment methods
            const paymentTypes = [];
            const accountIds = [];
            const payingAmounts = [];

            $('.payment-method-row').each(function() {
                const type = $(this).find('.payment-type-select').val();
                const accountId = $(this).find('.account-select').val() || null;
                const amount = parseFloat($(this).find('.payment-amount').val()) || 0;

                if (amount > 0) {
                    paymentTypes.push(type);
                    accountIds.push(accountId);
                    payingAmounts.push(amount);
                }
            });

            if (paymentTypes.length === 0) {
                toastr.error("{{ __('Please add at least one payment method') }}");
                return;
            }

            $('.preloader_area').removeClass('d-none');
            $('#running-order-payment-modal').modal('hide');

            console.log('Completing order:', orderId);
            console.log('Payment data:', { paymentTypes, accountIds, payingAmounts, totalPaying, amountReceived, discountAmount });

            $.ajax({
                type: 'POST',
                url: "{{ url('admin/pos/running-orders') }}/" + orderId + "/complete",
                data: {
                    _token: "{{ csrf_token() }}",
                    payment_type: paymentTypes,
                    account_id: accountIds,
                    paying_amount: payingAmounts,
                    paid_amount: totalPaying,
                    receive_amount: amountReceived,
                    return_amount: Math.max(0, amountReceived - paymentOrderTotal),
                    discount: discountAmount
                },
                success: function(response) {
                    console.log('Complete order response:', response);
                    if (response.success) {
                        toastr.success(response.message);
                        $('#order-details-modal').modal('hide');
                        loadRunningOrdersCount();
                        loadRunningOrders(); // Refresh running orders list
                        refreshAvailableTables(); // Refresh table availability

                        // Show POS receipt modal
                        $('#pos-receipt-body').html(response.receipt);
                        $('.receipt-full-invoice').attr('href', response.invoiceRoute);
                        $('#posReceiptModal').modal('show');

                        // Auto-print if setting is enabled
                        if (posSettings.is_printable) {
                            setTimeout(function() {
                                printPosReceipt();
                            }, 500);
                        }
                    } else {
                        toastr.error(response.message || "{{ __('Error completing order') }}");
                    }
                    $('.preloader_area').addClass('d-none');
                },
                error: function(xhr) {
                    console.error('Complete order error:', xhr.responseText);
                    toastr.error(xhr.responseJSON?.message || "{{ __('Server error occurred') }}");
                    $('.preloader_area').addClass('d-none');
                }
            });
        }

        // Print POS receipt
        function printPosReceipt() {
            const receiptContent = document.getElementById('pos-receipt-content');
            if (!receiptContent) {
                toastr.error("{{ __('Receipt not found') }}");
                return;
            }

            // Use a hidden iframe to print without leaving the page
            let printFrame = document.getElementById('pos-print-frame');
            if (!printFrame) {
                printFrame = document.createElement('iframe');
                printFrame.id = 'pos-print-frame';
                printFrame.style.cssText = 'position:fixed;top:-9999px;left:-9999px;width:80mm;height:0;border:none;';
                document.body.appendChild(printFrame);
            }

            const doc = printFrame.contentDocument || printFrame.contentWindow.document;
            doc.open();
            doc.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>POS Receipt</title>
                    <style>
                        * { margin: 0; padding: 0; box-sizing: border-box; }
                        body { font-family: 'Courier New', monospace; width: 80mm; padding: 3mm; }
                        @media print {
                            body { width: 80mm; padding: 0 3mm; }
                            @page { margin: 0; size: 80mm auto; }
                        }
                    </style>
                </head>
                <body>
                    ${receiptContent.outerHTML}
                </body>
                </html>
            `);
            doc.close();

            // Wait for content to render then print
            printFrame.onload = function() {
                printFrame.contentWindow.focus();
                printFrame.contentWindow.print();
            };
        }

        // Print receipt for dine-in/running order
        function printDineInReceipt(orderId) {
            if (!orderId) {
                toastr.warning("{{ __('No order to print') }}");
                return;
            }

            // Fetch receipt HTML via AJAX and print using hidden iframe
            const receiptUrl = '{{ route("admin.pos.running-orders.receipt", ["id" => "__ID__"]) }}'.replace('__ID__', orderId);

            $.get(receiptUrl, function(html) {
                let printFrame = document.getElementById('pos-print-frame');
                if (!printFrame) {
                    printFrame = document.createElement('iframe');
                    printFrame.id = 'pos-print-frame';
                    printFrame.style.cssText = 'position:fixed;top:-9999px;left:-9999px;width:80mm;height:0;border:none;';
                    document.body.appendChild(printFrame);
                }

                const doc = printFrame.contentDocument || printFrame.contentWindow.document;
                doc.open();
                doc.write(html);
                doc.close();

                printFrame.onload = function() {
                    printFrame.contentWindow.focus();
                    printFrame.contentWindow.print();
                };
            }).fail(function() {
                toastr.error("{{ __('Failed to load receipt') }}");
            });
        }

        // Print receipt for an already-paid order
        function printReceipt(orderId) {
            printDineInReceipt(orderId);
        }

        // Complete an already-paid running order (no payment processing needed)
        function completeRunningOrderDirectly(orderId) {
            $('#order-details-modal').modal('hide');

            Swal.fire({
                title: "{{ __('Complete Order?') }}",
                text: "{{ __('This order is already paid. Mark it as completed?') }}",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#71dd37',
                confirmButtonText: "{{ __('Yes, Complete') }}",
                cancelButtonText: "{{ __('Cancel') }}"
            }).then((result) => {
                if (result.isConfirmed) {
                    $('.preloader_area').removeClass('d-none');

                    $.ajax({
                        type: 'POST',
                        url: "{{ url('admin/pos/running-orders') }}/" + orderId + "/complete-paid",
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            if (response.success) {
                                toastr.success(response.message);
                                loadRunningOrdersCount();
                                loadRunningOrders();
                                refreshAvailableTables();

                                // Show receipt if returned
                                if (response.receipt) {
                                    $('#pos-receipt-body').html(response.receipt);
                                    $('.receipt-full-invoice').attr('href', response.invoiceRoute);
                                    $('#posReceiptModal').modal('show');

                                    if (posSettings.is_printable) {
                                        setTimeout(function() { printPosReceipt(); }, 500);
                                    }
                                }
                            } else {
                                toastr.error(response.message || "{{ __('Error completing order') }}");
                                $('#order-details-modal').modal('show');
                            }
                            $('.preloader_area').addClass('d-none');
                        },
                        error: function(xhr) {
                            toastr.error(xhr.responseJSON?.message || "{{ __('Server error occurred') }}");
                            $('.preloader_area').addClass('d-none');
                            $('#order-details-modal').modal('show');
                        }
                    });
                } else {
                    $('#order-details-modal').modal('show');
                }
            });
        }

        function cancelRunningOrder(orderId) {
            // Hide the modal first to prevent z-index conflict
            $('#order-details-modal').modal('hide');

            Swal.fire({
                title: "{{ __('Cancel Order?') }}",
                text: "{{ __('This action cannot be undone. The table will be released.') }}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                confirmButtonText: "{{ __('Yes, Cancel Order') }}",
                cancelButtonText: "{{ __('No, Keep It') }}"
            }).then((result) => {
                if (result.isConfirmed) {
                    $('.preloader_area').removeClass('d-none');

                    $.ajax({
                        type: 'POST',
                        url: "{{ url('admin/pos/running-orders') }}/" + orderId + "/cancel",
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            if (response.success) {
                                toastr.success(response.message);
                                loadRunningOrders();
                                loadRunningOrdersCount();
                                refreshAvailableTables(); // Refresh table availability
                            } else {
                                toastr.error(response.message || "{{ __('Error cancelling order') }}");
                                $('#order-details-modal').modal('show'); // Re-show modal on error
                            }
                            $('.preloader_area').addClass('d-none');
                        },
                        error: function() {
                            toastr.error("{{ __('Server error occurred') }}");
                            $('.preloader_area').addClass('d-none');
                            $('#order-details-modal').modal('show'); // Re-show modal on error
                        }
                    });
                } else {
                    // User clicked "No, Keep It" - re-show the modal
                    $('#order-details-modal').modal('show');
                }
            });
        }

        // Cancel edit mode and return to normal POS
        function cancelEditMode() {
            Swal.fire({
                title: "{{ __('Cancel Edit?') }}",
                text: "{{ __('Are you sure you want to cancel editing this order? Items in cart will be cleared.') }}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                confirmButtonText: "{{ __('Yes, Cancel') }}",
                cancelButtonText: "{{ __('No, Continue Editing') }}"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'GET',
                        url: "{{ route('admin.cart-clear') }}",
                        success: function() {
                            location.reload();
                        }
                    });
                }
            });
        }

        // Add items from cart to existing order
        function addItemsToExistingOrder(orderId) {
            // Check if cart has items
            const cartItems = $('.product-table tbody tr').length;
            if (cartItems === 0) {
                toastr.warning("{{ __('Please add items to cart first') }}");
                return;
            }

            Swal.fire({
                title: "{{ __('Add Items to Order?') }}",
                text: "{{ __('The items in cart will be added to the running order.') }}",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                confirmButtonText: "{{ __('Yes, Add Items') }}",
                cancelButtonText: "{{ __('Cancel') }}"
            }).then((result) => {
                if (result.isConfirmed) {
                    $('.preloader_area').removeClass('d-none');

                    // Get cart items and add them to the order
                    const cartData = [];
                    $('.product-table tbody tr').each(function() {
                        const row = $(this);
                        cartData.push({
                            rowid: row.data('rowid'),
                            id: row.data('id'),
                            name: row.find('.product-name').text().trim(),
                            qty: parseInt(row.find('.qty_input').val()) || 1,
                            price: parseFloat(row.data('price')) || 0
                        });
                    });

                    // Submit cart items to add to order via AJAX
                    $.ajax({
                        type: 'POST',
                        url: "{{ url('admin/pos/running-orders') }}/" + orderId + "/update",
                        data: {
                            _token: "{{ csrf_token() }}",
                            add_from_cart: true
                        },
                        success: function(response) {
                            if (response.success) {
                                toastr.success(response.message || "{{ __('Items added to order') }}");
                                // Clear cart and editing session
                                $.ajax({
                                    type: 'GET',
                                    url: "{{ route('admin.cart-clear') }}",
                                    success: function() {
                                        // Show the updated order details
                                        viewOrderDetails(orderId);
                                        $('#order-details-modal').modal('show');
                                        location.reload();
                                    }
                                });
                            } else {
                                toastr.error(response.message || "{{ __('Error adding items') }}");
                                $('.preloader_area').addClass('d-none');
                            }
                        },
                        error: function(xhr) {
                            toastr.error(xhr.responseJSON?.message || "{{ __('Server error occurred') }}");
                            $('.preloader_area').addClass('d-none');
                        }
                    });
                }
            });
        }

        // Load running orders count on page load and periodically
        $(document).ready(function() {
            loadRunningOrdersCount();
            // Refresh count every 30 seconds
            setInterval(loadRunningOrdersCount, 30000);
        });

        // Loyalty Points Variables
        let customerLoyaltyInfo = null;
        let pointsRedemptionRate = {{ $loyaltyProgram?->redemption_rate ?? 100 }};
        let pointsEarningRate = {{ $loyaltyProgram?->earning_rate ?? 1 }};

        // Fetch customer loyalty info when customer changes
        function loadCustomerLoyalty(customerId) {
            if (!customerId || customerId === 'walk-in-customer') {
                customerLoyaltyInfo = null;
                $('#customerLoyaltyRow').addClass('d-none');
                $('.points-redemption-row').addClass('d-none');
                $('.points-discount-row').addClass('d-none');
                return;
            }

            $.ajax({
                type: 'GET',
                url: "{{ route('admin.pos.loyalty.customer') }}",
                data: { customer_id: customerId },
                success: function(response) {
                    if (response.success && response.customer) {
                        customerLoyaltyInfo = response.customer;
                        pointsRedemptionRate = response.redemption_rate || 100;
                        pointsEarningRate = response.earning_rate || 1;

                        // Update UI
                        $('#customerPointsBalance').text(response.customer.total_points || 0);
                        const pointsValue = (response.customer.total_points || 0) / pointsRedemptionRate;
                        $('#customerPointsValue').text('{{ currency_icon() }}' + pointsValue.toFixed(2));
                        $('#customerLoyaltyRow').removeClass('d-none');
                        $('#loyaltyCustomerIdInput').val(response.customer.id);

                        // Calculate earnable points for current cart
                        calculateEarnablePoints();
                    } else {
                        customerLoyaltyInfo = null;
                        $('#customerLoyaltyRow').addClass('d-none');
                    }
                },
                error: function() {
                    customerLoyaltyInfo = null;
                    $('#customerLoyaltyRow').addClass('d-none');
                }
            });
        }

        // Calculate points to earn for current order
        function calculateEarnablePoints() {
            const total = parseFloat($('#finalTotal').text().replace(/[^0-9.]/g, '')) || 0;
            if (total > 0 && customerLoyaltyInfo) {
                $.ajax({
                    type: 'GET',
                    url: "{{ route('admin.pos.loyalty.calculate') }}",
                    data: { amount: total },
                    success: function(response) {
                        if (response.success) {
                            $('#earnablePoints').text(response.points);
                        }
                    }
                });
            } else {
                $('#earnablePoints').text(0);
            }
        }

        // Update points redemption when opening payment modal
        function updatePointsRedemption() {
            if (!customerLoyaltyInfo || customerLoyaltyInfo.total_points <= 0) {
                $('.points-redemption-row').addClass('d-none');
                $('.points-discount-row').addClass('d-none');
                return;
            }

            const availablePoints = customerLoyaltyInfo.total_points;
            const total = parseFloat($('#total_amountModal').text()) || 0;
            const maxRedeemableDiscount = total; // Can't redeem more than total
            const maxRedeemablePoints = Math.min(availablePoints, Math.floor(maxRedeemableDiscount * pointsRedemptionRate));

            $('#availablePointsText').text(availablePoints + ' pts available');
            $('#pointsToRedeem').attr('max', maxRedeemablePoints).val(0);
            $('.points-redemption-row').removeClass('d-none');
        }

        // Apply maximum points
        function applyMaxPoints() {
            const maxPoints = parseInt($('#pointsToRedeem').attr('max')) || 0;
            $('#pointsToRedeem').val(maxPoints).trigger('change');
        }

        // Handle points redemption change
        $(document).on('change input', '#pointsToRedeem', function() {
            const points = parseInt($(this).val()) || 0;
            const discount = points / pointsRedemptionRate;

            $('#pointsDiscountInput').val(discount.toFixed(2));
            $('#pointsDiscountModal').text('-{{ currency_icon() }}' + discount.toFixed(2));

            if (points > 0) {
                $('.points-discount-row').removeClass('d-none');
            } else {
                $('.points-discount-row').addClass('d-none');
            }

            // Update totals
            updatePaymentTotals();
        });

        // Update payment totals with points discount
        function updatePaymentTotals() {
            const subTotal = parseFloat($('[name="sub_total"]').val()) || 0;
            const discountAmount = parseFloat($('[name="discount_amount"]').val()) || 0;
            const pointsDiscount = parseFloat($('#pointsDiscountInput').val()) || 0;

            const grandTotal = subTotal - discountAmount - pointsDiscount;

            $('#total_amountModal').text(grandTotal.toFixed(2));
            $('#total_amount_modal_input').val(grandTotal.toFixed(2));
            $('#total_amountModal2').text(grandTotal.toFixed(2));
            $('.paying_amount').val(grandTotal.toFixed(2));
            $('#paid_amountModal').text(grandTotal.toFixed(2));
        }

        // Override openPaymentModal to handle dine-in flow
        const originalOpenPaymentModal = openPaymentModal;
        openPaymentModal = function() {
            const orderType = $('input[name="order_type_radio"]:checked').val();
            const tableId = $('#table_id').val();

            // For dine-in, require table selection
            if (orderType === 'dine_in') {
                if (!tableId) {
                    // Open table selection modal
                    $('#tableSelectionModal').modal('show');
                    toastr.info("{{ __('Please select a table first') }}");
                    return;
                }
                // Place dine-in order without immediate payment
                placeDineInOrder();
                return;
            }

            // For take-away, open normal payment modal
            originalOpenPaymentModal();
            updatePointsRedemption();
        };

        // Place dine-in order without payment - show Start Dine-In modal or submit if already configured
        function placeDineInOrder() {
            // Check if cart is empty
            if ($('.product-table tbody > tr').length == 0) {
                toastr.error("{{ __('Cart is empty') }}");
                return;
            }

            // Check if guest count was already set (meaning modal was completed before)
            const existingGuestCount = parseInt($('#guest_count').val()) || 0;
            if (existingGuestCount > 0) {
                // Already configured, submit directly
                submitDineInOrder(existingGuestCount);
                return;
            }

            // Get table info from selectedTableData or hidden inputs
            let tableName = selectedTableData ? selectedTableData.name : $('#selectedTableText').text().replace(/^.*?(?=\w)/, '');
            let tableSeats = selectedTableData ? selectedTableData.capacity : 4;
            let availableSeats = selectedTableData ? selectedTableData.availableSeats : tableSeats;
            let occupiedSeats = selectedTableData ? selectedTableData.occupiedSeats : 0;

            // Populate Start Dine-In modal
            $('#dineInTableName').text(tableName);
            $('#dineInTableCapacity').text(tableSeats);

            // Update table number visual - extract short name
            let tableNumber = tableName.replace(/[^A-Za-z0-9]/g, '').substring(0, 4).toUpperCase();
            if (tableName.toLowerCase().includes('table')) {
                tableNumber = 'T' + tableName.replace(/\D/g, '');
            } else if (tableName.toLowerCase().includes('patio')) {
                tableNumber = 'P' + tableName.replace(/\D/g, '');
            } else if (tableName.toLowerCase().includes('vip')) {
                tableNumber = 'VIP' + tableName.replace(/\D/g, '');
            }
            $('#dineInTableNumber').text(tableNumber || 'T1');

            // Show available seats if partially occupied
            if (occupiedSeats > 0) {
                $('#dineInAvailableSeats').html('<i class="fas fa-exclamation-circle text-warning"></i> ' + availableSeats + ' {{ __("available") }}');
            } else {
                $('#dineInAvailableSeats').html('<i class="fas fa-check-circle text-success"></i> {{ __("All available") }}');
            }

            // Set max guest count based on available seats
            $('#dineInGuestCount').attr('max', availableSeats).val(1);
            $('#guestCountHint').text("{{ __('Maximum') }}: " + availableSeats + " {{ __('guests') }}");

            // Reset other fields
            $('#dineInWaiter').val('').trigger('change');
            $('#dineInNote').val('');

            // Show Start Dine-In modal
            $('#startDineInModal').modal('show');
        }

        // Submit dine-in order
        function submitDineInOrder(guestCount = 1) {
            const customerId = $('#customer_id').val();
            const tableId = $('#table_id').val();
            const discountAmount = $('#discount_total_amount').val() || 0;
            const discountType = $('#discount_type').val();
            const waiterId = $('#dineInWaiter').val() || $('#waiter_id').val() || '';
            const saleNote = $('#dineInNote').val() || $('#sale_note').val() || '';

            $('.preloader_area').removeClass('d-none');

            // Get today's date in d-m-Y format
            const today = new Date();
            const saleDate = String(today.getDate()).padStart(2, '0') + '-' +
                            String(today.getMonth() + 1).padStart(2, '0') + '-' +
                            today.getFullYear();

            $.ajax({
                type: 'POST',
                url: "{{ route('admin.place-order') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    order_customer_id: customerId || 'walk-in-customer',
                    order_type: 'dine_in',
                    table_id: tableId,
                    guest_count: guestCount,
                    waiter_id: waiterId,
                    sale_note: saleNote,
                    defer_payment: 1,
                    discount_amount: discountAmount,
                    discount_type: discountType,
                    sub_total: $('#total').text().replace(/[^0-9.]/g, ''),
                    total_amount: $('#finalTotal').text().replace(/[^0-9.]/g, ''),
                    sale_date: saleDate,
                    payment_type: [],
                    paying_amount: [],
                    account_id: [],
                    loyalty_customer_id: $('#loyaltyCustomerIdInput').val() || ''
                },
                success: function(response) {
                    if (response['alert-type'] == 'success') {
                        // Reset cart
                        $(".product-table tbody").html('');
                        $('#titems').text(0);
                        $('#discount_total_amount').val(0);
                        $('#tds').text(0);
                        totalSummery();

                        // Reset selections
                        $("#customer_id").val('').trigger('change');
                        $('#discount_type').val(1).trigger('change');
                        $('.dis-form').hide();

                        // Reset table selection
                        clearTableSelection();

                        // Reset loyalty info
                        customerLoyaltyInfo = null;
                        $('#customerLoyaltyRow').addClass('d-none');

                        // Refresh running orders count
                        loadRunningOrdersCount();

                        // Refresh available tables
                        refreshAvailableTables();

                        // Update payment button state
                        updatePaymentButtonState();

                        // Get order ID from response
                        const orderId = response.order ? response.order.id : null;
                        const tableName = response.table_name || '';
                        const successMessage = tableName
                            ? response.message + ' - ' + tableName
                            : response.message;

                        // Show success dialog with print option
                        Swal.fire({
                            icon: 'success',
                            title: "{{ __('Order Started') }}",
                            text: successMessage,
                            showCancelButton: true,
                            showDenyButton: orderId ? true : false,
                            confirmButtonColor: '#28a745',
                            cancelButtonColor: '#6c757d',
                            denyButtonColor: '#17a2b8',
                            confirmButtonText: '<i class="fas fa-list me-1"></i> {{ __("View Orders") }}',
                            cancelButtonText: '<i class="fas fa-plus me-1"></i> {{ __("New Order") }}',
                            denyButtonText: '<i class="fas fa-print me-1"></i> {{ __("Print Receipt") }}',
                            reverseButtons: true
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Show running orders modal
                                openRunningOrders();
                            } else if (result.isDenied && orderId) {
                                // Print receipt only
                                printDineInReceipt(orderId);
                            }
                            // If cancelled, just stay on POS for new order
                        });
                    } else {
                        toastr.error(response.message);
                    }
                    $('.preloader_area').addClass('d-none');
                },
                error: function(response) {
                    let errorMsg = "{{ __('Server error occurred') }}";
                    if (response.responseJSON && response.responseJSON.message) {
                        errorMsg = response.responseJSON.message;
                    }
                    toastr.error(errorMsg);
                    console.log(response);
                    $('.preloader_area').addClass('d-none');
                }
            });
        }

        // Update customer change handler
        const originalCustomerChange = $('#customer_id').data('events')?.change;
        $('#customer_id').off('change').on('change', function() {
            let customer_id = $(this).val();
            $("#order_customer_id").val(customer_id ? customer_id : 'walk-in-customer');

            const discount = $('select#customer_id option:selected').data('discount');
            if (discount) {
                $('[name="discount_type"]').val(2).niceSelect('update');
                $('#discount_total_amount').val(discount);
                updateDiscountType(2);
            }

            // Load customer loyalty info
            loadCustomerLoyalty(customer_id);
        });

        // Update totals recalculation to include earnable points
        const originalTotalSummery = totalSummery;
        totalSummery = function() {
            originalTotalSummery();
            if (customerLoyaltyInfo) {
                calculateEarnablePoints();
            }
        };
    </script>
@endpush
