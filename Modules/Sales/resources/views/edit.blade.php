@extends('admin.layouts.master')
@section('title', __('Edit Sale') . ' - #' . $sale->invoice)
@push('css')
    <link rel="stylesheet" href="{{ asset('backend/css/pos.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/css/jquery-ui.min.css') }}">
    <style>
        .ui-autocomplete {
            z-index: 215000000 !important;
        }

        /* Order Info Banner */
        .order-info-banner {
            background: #6777ef;
            color: #fff;
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 15px;
        }
        .order-info-banner .order-number {
            font-size: 1.5rem;
            font-weight: 700;
        }
        .order-info-banner .order-meta {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        .order-info-banner .badge {
            font-size: 0.85rem;
        }

        /* Summary table styling */
        .summary-table {
            width: 100%;
            margin-bottom: 0;
        }
        .summary-table td {
            padding: 8px 12px;
            border-bottom: 1px solid #eee;
        }
        .summary-table tr:last-child td {
            border-bottom: none;
        }
        .summary-table .pay-row td {
            background: #343a40 !important;
            color: #fff !important;
            font-weight: 600;
            font-size: 1.1rem;
        }

        /* Product Search Dropdown */
        #itemList {
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
        .product_list_item:hover {
            background: #f8f9fa;
        }
    </style>
@endpush
@section('content')

    <!-- Main Content -->
    <div class="main-content">
        <section class="section">
            <div class="section-body">
                <div class="row">
                    <!-- Left Column: Products -->
                    <div class="col-lg-5">
                        <div class="card" id="products">
                            <div class="card-header">
                                <form id="product_search_form" class="pos_pro_search_form w-100">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group mb-2">
                                                <input type="text" class="form-control" name="name" id="name"
                                                    placeholder="{{ __('Enter Product name / SKU / Scan bar code') }}"
                                                    autocomplete="off" value="{{ request()->get('name') }}">
                                                <ul class="dropdown-menu" id="itemList"></ul>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group mb-2">
                                                <select name="category_id" id="product_category_id" class="form-control select2">
                                                    <option value="">{{ __('Select Category') }}</option>
                                                    @foreach ($categories as $category)
                                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group mb-2">
                                                <select name="brand_id" id="product_brand_id" class="form-control select2">
                                                    <option value="">{{ __('Select Brand') }}</option>
                                                    @foreach ($brands as $brand)
                                                        <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="card-body product_body"></div>
                        </div>

                        <!-- Combo Packages -->
                        <div class="card mt-3" id="combos">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-box me-2"></i>{{ __('Combo Packages') }}</h6>
                            </div>
                            <div class="card-body combo_body" style="max-height: 300px; overflow-y: auto;"></div>
                        </div>

                    </div>

                    <!-- Right Column: Cart & Summary -->
                    <div class="col-lg-7">
                        <div class="card">
                            <!-- Order Info Banner -->
                            <div class="card-header p-0">
                                <div class="order-info-banner">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="order-number">{{ __('Order') }} #{{ $sale->invoice }}</span>
                                            <div class="order-meta mt-1">
                                                <i class="fas fa-calendar me-1"></i>
                                                {{ $sale->order_date ? $sale->order_date->format('d M Y') : '-' }}
                                                @if($sale->table)
                                                    <span class="mx-2">|</span>
                                                    <i class="fas fa-chair me-1"></i>{{ $sale->table->name }}
                                                @endif
                                                @if($sale->waiter)
                                                    <span class="mx-2">|</span>
                                                    <i class="fas fa-user me-1"></i>{{ $sale->waiter->name }}
                                                @endif
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            @php
                                                $orderTypeBadgeClass = match($sale->order_type) {
                                                    'dine_in' => 'bg-primary',
                                                    'take_away' => 'bg-success',
                                                    'delivery' => 'bg-info',
                                                    default => 'bg-secondary'
                                                };
                                            @endphp
                                            <span class="badge {{ $orderTypeBadgeClass }} mb-1">
                                                {{ ucfirst(str_replace('_', ' ', $sale->order_type ?? 'N/A')) }}
                                            </span>
                                            @if($sale->due_amount > 0)
                                                <span class="badge bg-danger">{{ __('Due') }}: {{ currency($sale->due_amount) }}</span>
                                            @else
                                                <span class="badge bg-success">{{ __('Paid') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Customer Selection -->
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
                                                <i class="fa fa-plus" aria-hidden="true"></i>{{ __('New') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Cart Items -->
                            <div class="card-body pos_pro_table">
                                <div class="row">
                                    @php
                                        // Calculate subtotal from cart contents
                                        $cumalitive_sub_total = 0;
                                        foreach ($cart_contents as $item) {
                                            $cumalitive_sub_total += $item['sub_total'] ?? 0;
                                        }
                                        // Calculate totals - use POS settings tax rate, fallback to sale tax rate
                                        $discount = $sale->order_discount ?? 0;
                                        $taxRate = $posSettings->pos_tax_rate ?? $sale->tax_rate ?? 0;
                                        $afterDiscount = $cumalitive_sub_total - $discount;
                                        $taxAmount = $afterDiscount * ($taxRate / 100);
                                        $grandTotal = $afterDiscount + $taxAmount;
                                    @endphp
                                    <div class="col-md-12 product-table-container">
                                        @include('pos::ajax_cart', ['cart_contents' => $cart_contents])
                                    </div>
                                </div>

                                <!-- Summary Table -->
                                <div class="table-responsive mt-3">
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
                                            <tr id="taxRow">
                                                <td>{{ __('Tax') }} (<span id="taxRateDisplay">{{ $taxRate }}</span>%)</td>
                                                <td class="text-end" id="taxDisplay">{{ currency($taxAmount) }}</td>
                                            </tr>
                                            <tr class="pay-row">
                                                <td>{{ __('Total Payable') }}</td>
                                                <td class="text-end" id="totalAmountWithVat">{{ currency($grandTotal) }}</td>
                                            </tr>
                                            @php
                                                // Calculate already paid from grand_total - due_amount
                                                // This handles cases where paid_amount field wasn't properly set
                                                $alreadyPaid = max(0, $sale->grand_total - $sale->due_amount);
                                            @endphp
                                            <tr>
                                                <td>{{ __('Already Paid') }}</td>
                                                <td class="text-end text-success">{{ currency($alreadyPaid) }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <!-- Hidden fields -->
                                    <input type="hidden" id="total" value="{{ $cumalitive_sub_total }}">
                                    <input type="hidden" id="subtotal" value="{{ $cumalitive_sub_total }}">
                                    <input type="hidden" id="extra" value="0">
                                    <input type="hidden" id="gtotal" value="{{ $afterDiscount }}">
                                    <input type="hidden" id="totalVat" value="{{ $taxAmount }}">
                                    <input type="hidden" id="taxRate" value="{{ $taxRate }}">
                                    <input type="hidden" id="taxAmount" value="{{ $taxAmount }}">
                                    <input type="hidden" id="business_vat" value="{{ $taxRate }}">
                                    <input type="hidden" id="discount_type" value="1">
                                    <input type="hidden" id="discount_total_amount" value="0">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="pos-footer" style="z-index: 9999">
            <div>
                <a href="{{ route('admin.sales.index') }}" class="btn btn-block back-btn">
                    <i class="fa fa-backward fa-lg mt-3"></i>
                </a>
            </div>
            <h3 class="final-text">
                {{ __('Total') }} : <span id="finalTotal">{{ currency($grandTotal) }}</span>
            </h3>
            <div class="btn-group lg-btns">
                <button type="button" class="btn cancel-btn" onclick="resetCart()">
                    {{ __('Clear') }}
                </button>
                <button type="button" class="btn payment-btn" onclick="openPaymentModal()">
                    {{ __('Update & Pay') }}
                </button>
            </div>
        </footer>
    </div>

    @include('components.admin.preloader')

    <!-- Product Modal -->
    <div class="modal fade" id="cartModal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document" style="max-width: 900px;">
            <div class="modal-content" style="border-radius: 12px; overflow: hidden; border: none;">
                <div class="modal-body p-0">
                    <div class="load_product_modal_response"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create new user modal -->
    @include('customer::customer-modal')

    <!-- Item details modal -->
    <div class="modal fade" id="itemDetailsModal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content load_item_details_modal_response"></div>
        </div>
    </div>

    <!-- Payment Modal -->
    <div class="modal fade" id="payment-modal" role="dialog" aria-labelledby="paymentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" action="" id="checkoutForm" onSubmit="paymentSubmit(event)">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="order_customer_id" id="order_customer_id" value="">
                    <input type="hidden" name="sub_total" value="" autocomplete="off">
                    <input type="hidden" name="total_amount" value="0" id="total_amount_modal_input" autocomplete="off">
                    <input type="hidden" name="discount_amount" value="0" autocomplete="off">
                    <input type="hidden" name="sale_date" value="{{ formatDate($sale->order_date) }}" autocomplete="off">

                    <!-- Modal Header -->
                    <div class="modal-header py-2">
                        <h5 class="modal-title">
                            <i class="fas fa-cash-register me-2"></i>{{ __('Checkout') }}
                            <span class="badge bg-secondary ms-2">#{{ $sale->invoice }}</span>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
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
                                            <input type="date" class="form-control form-control-sm" name="due_date">
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
                        <button type="submit" id="checkout" class="btn btn-success btn-lg px-4">
                            <i class="fas fa-check me-1"></i>{{ __('Complete Payment') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Stock Update Modal -->
    <div class="modal fade" id="stockUpdateModal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <form action="javascript:;" id="stockUpdateModalForm">
                        <input type="hidden" name="row_number">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="purchase_price">{{ __('Purchase Price') }} ({{ currency_icon() }})</label>
                                    <input type="number" name="purchase_price" class="form-control" id="purchase_price"
                                        value="{{ old('purchase_price') }}" step="0.01">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="selling_price">{{ __('Selling Price') }} ({{ currency_icon() }})</label>
                                    <input type="number" name="selling_price" class="form-control" id="selling_price"
                                        value="{{ old('selling_price') }}" step="0.01">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-success stockModalSave" form="stockUpdateModalForm">{{ __('Save') }}</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="{{ asset('backend/js/jquery-ui.min.js') }}"></script>
    <script>
        // Initialize
        (function($) {
            "use strict";
            $(document).ready(function() {
                totalSummery();
                loadProudcts();

                // Set initial customer
                @if($sale->customer_id)
                    $('#customer_id').val('{{ $sale->customer_id }}').trigger('change');
                @endif

                // Update POS quantity
                $(document).on("input", ".pos_input_qty", function(e) {
                    let quantity = $(this).val();
                    if (quantity < 1) return;

                    $('.preloader_area').removeClass('d-none');
                    let parernt_td = $(this).parents('td');
                    let rowid = parernt_td.data('rowid');

                    $.ajax({
                        type: 'get',
                        data: { rowid, quantity, edit: 1 },
                        url: "{{ route('admin.cart-quantity-update') }}",
                        success: function(response) {
                            $(".product-table-container").html(response);
                            totalSummery();
                            $('.preloader_area').addClass('d-none');
                        },
                        error: function(response) {
                            toastr.error("{{ __('Server error occurred') }}");
                            $('.preloader_area').addClass('d-none');
                        }
                    });
                });

                // Customer change
                $("#customer_id").on("change", function() {
                    let customer_id = $(this).val();
                    $("#order_customer_id").val(customer_id ? customer_id : 'walk-in-customer');

                    const discount = $('select#customer_id option:selected').data('discount');
                    if (discount) {
                        $('[name="discount_type"]').val(2);
                        $('#discount_total_amount').val(discount);
                        discountExist();
                    }
                });

                // Add new customer
                $("#add-customer-form").on("submit", function(e) {
                    e.preventDefault();
                    $.ajax({
                        type: 'POST',
                        data: $('#add-customer-form').serialize(),
                        url: $('#add-customer-form').attr('action'),
                        success: function(response) {
                            toastr.success(response.message);
                            $("#addCustomer").modal('hide');
                            $('#add-customer-form')[0].reset();
                            $("#customer_id").html(response.view);
                        },
                        error: function(response) {
                            if (response.status == 500) {
                                toastr.error("{{ __('Server error occurred') }}");
                            }
                        }
                    });
                });

                // Category/Brand filter
                $("#product_category_id,#product_brand_id").on('change', function() {
                    const category_id = $('#product_category_id').val();
                    const brand = $('#product_brand_id').val();
                    const name = $('#name').val();
                    loadProudcts({ category_id, brand, name });
                });

                // Product autocomplete
                $(document).on('click', function(event) {
                    var searchInput = $("#name");
                    var itemList = $("#itemList");
                    if (!searchInput.is(event.target) && !itemList.is(event.target) && itemList.has(event.target).length === 0) {
                        itemList.removeClass("show");
                    }
                });

                $('#name').autocomplete({
                    html: true,
                    source: function(request, response) {
                        $.ajax({
                            url: "{{ route('admin.load-products-list') }}",
                            dataType: 'json',
                            data: { name: request.term },
                            success: function(response) {
                                if (response.total > 0) {
                                    $('#itemList').html(response.view).addClass('show');
                                }
                            }
                        });
                    },
                    minLength: 2
                });


                // Discount toggle
                $(document).on('click', '.dis-tgl', function() {
                    $(".dis-form").slideToggle("fast");
                });

                // Add payment row
                $('.add-payment').on('click', function() {
                    const row = `@include('pos::payment-row', ['add' => true])`;
                    $('#paymentRow').append(row);
                    $('[name="payment_type[]"]').niceSelect();
                });

                $(document).on('click', '.remove-payment', function() {
                    $(this).parents('.payment-row').remove();
                    calDue();
                });

                // Quick amount buttons
                $(document).on('click', '.quick-amount-btn', function() {
                    const amount = parseFloat($(this).data('amount'));
                    const currentVal = parseFloat($('.receive_cash').val()) || 0;
                    $('.receive_cash').val(currentVal + amount).trigger('input');
                });

                // Price edit
                $(document).on('click', '.price', function() {
                    let child = $(this).children('input');
                    child.removeClass('d-none');
                    child.siblings('span').addClass('d-none');
                });

                $(document).on('focusout', '.price > input', function() {
                    const $this = $(this);
                    const rowId = $this.data('rowid');
                    const value = $this.val();
                    updatePrice(rowId, value);
                    calculateExtra();
                });

                // Stock update modal
                $(document).on('click', '.edit-btn', function() {
                    let source = $(this).parents('tr').data('rowid');
                    const purchasePrice = $(this).data('purchase');
                    const sellingPrice = $(this).data('selling');
                    $('#purchase_price').val(purchasePrice);
                    $('#selling_price').val(sellingPrice);
                    $('[name="row_number"]').val(source);
                    $('#stockUpdateModal').modal('show');
                });

                $('#stockUpdateModalForm').on('submit', function() {
                    const rowId = $('[name="row_number"]').val();
                    const purchasePrice = $('#purchase_price').val();
                    const sellingPrice = $('#selling_price').val();

                    $.ajax({
                        type: 'get',
                        data: {
                            rowid: rowId,
                            purchase_price: purchasePrice,
                            selling_price: sellingPrice,
                            price: sellingPrice,
                            edit: true
                        },
                        url: "{{ route('admin.cart.price.update') }}",
                        success: function(response) {
                            $('#stockUpdateModal').modal('hide');
                            updatePrice(rowId, sellingPrice);
                            calculateExtra();
                            totalSummery();
                        }
                    });
                });

                // Add customer modal
                $('.addCustomer').on('click', function(e) {
                    e.preventDefault();
                    $('#addCustomer').modal('show');
                    $('.pos-footer').css('z-index', 0);
                });

                $('#addCustomer .close').on('click', function() {
                    modalHide('#addCustomer');
                });
            });
        })(jQuery);

        function setExactAmount() {
            const total = parseFloat($('#total_amountModal').text().replace(/[^0-9.]/g, '')) || 0;
            $('.receive_cash').val(total).trigger('input');
        }

        function calculateExtra() {
            let total = 0;
            $('[name="source"]').each(function() {
                if ($(this).val() == '2') {
                    const row = $(this).closest('tr');
                    const editBtn = row.find('.edit-btn');
                    const sellingPrice = parseFloat(editBtn.data('selling')) || 0;
                    const purchasePrice = parseFloat(editBtn.data('purchase')) || 0;
                    const qty = parseFloat(row.find('.pos_input_qty').val()) || 1;
                    const profit = (sellingPrice - purchasePrice) * qty;
                    total += profit;
                }
            });
            $('#extra').val(total);
        }

        function updatePrice(rowId, price) {
            $.ajax({
                type: 'get',
                data: { rowId, price, edit: 1 },
                url: "{{ route('admin.cart-price-update') }}",
                success: function(response) {
                    $(".product-table-container").html(response);
                    totalSummery();
                }
            });
        }

        function load_product_model(product_id) {
            $('.preloader_area').removeClass('d-none');
            $.ajax({
                type: 'get',
                url: "{{ route('admin.check-cart-restaurant', '') }}" + "/" + product_id,
                success: function(response) {
                    if (response.status == true) {
                        $(".modal-reset-button").attr('data-product-id', product_id);
                        $("#resetCartModal").modal('show');
                        $('.preloader_area').addClass('d-none');
                    } else {
                        loadProductModal(product_id);
                    }
                },
                error: function(response) {
                    toastr.error("{{ __('Server error occurred') }}");
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
                    $(".load_product_modal_response").html(response);
                    $("#cartModal").modal('show');
                    $('.preloader_area').addClass('d-none');
                },
                error: function(response) {
                    toastr.error("{{ __('Server error occurred') }}");
                    $('.preloader_area').addClass('d-none');
                }
            });
        }

        function removeCartItem(rowId) {
            $.ajax({
                type: 'get',
                url: "{{ url('admin/pos/remove-cart-item') }}" + "/" + rowId + '?edit=1',
                success: function(response) {
                    $(".product-table-container").html(response);
                    totalSummery();
                    toastr.success("{{ __('Remove successfully') }}");
                },
                error: function(response) {
                    toastr.error("{{ __('Server error occurred') }}");
                }
            });
        }

        function loadProudcts(data = null, type = 'product') {
            $('.preloader_area').removeClass('d-none');
            $.ajax({
                type: 'get',
                url: "{{ route('admin.load-products') }}",
                data: data,
                success: function(response) {
                    $(".product_body").html(response.productView);
                    $(".combo_body").html(response.comboView);
                    $('.preloader_area').addClass('d-none');
                },
                error: function(response) {
                    toastr.error("{{ __('Server error occurred') }}");
                    $('.preloader_area').addClass('d-none');
                }
            });
        }

        function loadPagination(url) {
            $('.preloader_area').removeClass('d-none');
            $.ajax({
                type: 'get',
                url: url,
                success: function(response) {
                    $(".product_body").html(response.productView);
                    $(".combo_body").html(response.comboView);
                    $('.preloader_area').addClass('d-none');
                },
                error: function(response) {
                    toastr.error("{{ __('Server error occurred') }}");
                }
            });
        }

        function openPaymentModal() {
            calDue();
            $('.pos-footer').css('z-index', 0);

            const finalTotal = $('#finalTotal').text().replace(/[^0-9.]/g, '');
            const discountAmount = $('#tds').text().replace(/[^0-9.]/g, '') || 0;
            const subTotal = $('#total').val() || $('#subtotalDisplay').text().replace(/[^0-9.]/g, '');
            const item = $('#titems').text();

            $('[name="sub_total"]').val(subTotal);
            $('#sub_totalModal').text(subTotal);

            $('#discount_amountModal').text(discountAmount);
            $('[name="discount_amount"]').val(discountAmount);

            let grandTotal = parseFloat(finalTotal);
            $('#total_amountModal').text(grandTotal.toFixed(2));
            $('#total_amount_modal_input').val(grandTotal);
            $('#total_amountModal2').text(grandTotal.toFixed(2));

            // Customer info
            let customer_id = $('#customer_id').val();
            $("#order_customer_id").val(customer_id ? customer_id : 'walk-in-customer');
            loadCustomer(customer_id);

            $('#itemModal').text(item);

            // Update payment row with full amount
            const paymentRows = $('#paymentRow .payment-row');
            if (paymentRows.length === 1) {
                paymentRows.first().find('.paying_amount').val(grandTotal.toFixed(2));
            }

            // Hide discount row if no discount
            if (!discountAmount || discountAmount == 0) {
                $('.discount-row').addClass('d-none');
            } else {
                $('.discount-row').removeClass('d-none');
            }

            $('#payment-modal').modal('show');
        }

        function resetCart() {
            $.ajax({
                type: 'get',
                url: "{{ route('admin.modal-cart-clear') }}?edit=1",
                success: function(response) {
                    $(".product-table tbody").html('');
                    totalSummery();
                    toastr.success("{{ __('Cart reset successfully') }}");
                },
                error: function(response) {
                    toastr.error("{{ __('Server error occurred') }}");
                }
            });
        }

        function singleAddToCart(id, serviceType = 'product') {
            $('.preloader_area').removeClass('d-none');
            $.ajax({
                type: 'get',
                data: { product_id: id, type: 'single', serviceType: serviceType, edit: 1 },
                url: "{{ url('/admin/pos/add-to-cart') }}",
                success: function(response) {
                    $(".product-table-container").html(response);
                    toastr.success("{{ __('Item added successfully') }}");
                    totalSummery();
                    $('.preloader_area').addClass('d-none');
                    scrollToCurrent();
                },
                error: function(response) {
                    if (response.status == 500) {
                        toastr.error("{{ __('Server error occurred') }}");
                    }
                    if (response.status == 403) {
                        toastr.error(response.responseJSON.message);
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
                    serviceType: 'menu_item',
                    edit: 1
                },
                url: "{{ url('/admin/pos/add-to-cart') }}",
                success: function(response) {
                    $(".product-table-container").html(response);
                    toastr.success("{{ __('Item added successfully') }}");
                    totalSummery();
                    $('.preloader_area').addClass('d-none');
                    scrollToCurrent();
                },
                error: function(response) {
                    if (response.status == 500) {
                        toastr.error("{{ __('Server error occurred') }}");
                    }
                    if (response.status == 403) {
                        toastr.error(response.responseJSON.message);
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
                    serviceType: 'combo',
                    edit: 1
                },
                url: "{{ url('/admin/pos/add-to-cart') }}",
                success: function(response) {
                    $(".product-table-container").html(response);
                    toastr.success("{{ __('Combo added successfully') }}");
                    totalSummery();
                    $('.preloader_area').addClass('d-none');
                    scrollToCurrent();
                },
                error: function(response) {
                    if (response.status == 500) {
                        toastr.error("{{ __('Server error occurred') }}");
                    }
                    if (response.status == 403) {
                        toastr.error(response.responseJSON.message);
                    }
                    $('.preloader_area').addClass('d-none');
                }
            });
        }

        function scrollToCurrent(pos = 'scroll') {
            const $current = $('.pos_pro_list_table tbody tr:last-child');
            const sidebar = $('.product-table .table-responsive');
            if (pos !== 'scroll') {
                sidebar.animate({ scrollTop: pos }, 300);
            } else if ($current.length) {
                const sidebarOffset = sidebar.offset().top;
                const currentOffset = $current.offset().top;
                const scrollTop = sidebar.scrollTop() + (currentOffset - sidebarOffset) - 100;
                sidebar.animate({ scrollTop: scrollTop }, 300);
            }
        }

        function numberFormat(n) {
            return Number(n).toFixed(2);
        }

        function numberOnly(str) {
            if (typeof str === 'number') return str;
            return parseFloat(str.replace(/[^0-9.]/g, '')) || 0;
        }

        function discountExist() {
            let discount_total_amount = $('#discount_total_amount').val();
            let discount_type = $('#discount_type').val();
            let total_amount_get_text = Number($('#subtotal').val() || $('#subtotalDisplay').text().replace(/[^0-9.]/g, ''));
            let totalAmount = 0;
            let percentage = null;

            if (discount_type == 1) {
                if (discount_total_amount > total_amount_get_text) {
                    discount_total_amount = total_amount_get_text;
                }
                totalAmount = numberFormat(Number(total_amount_get_text - discount_total_amount).toFixed(6));
            } else {
                if (discount_total_amount > 100) {
                    discount_total_amount = 100;
                }
                percentage = (discount_total_amount * total_amount_get_text) / 100;
                totalAmount = total_amount_get_text - percentage;
            }

            $('#tds').text(percentage ? numberFormat(percentage) : numberFormat(discount_total_amount));
            $('input[name=discount_amount]').val(percentage ? percentage : discount_total_amount);
            $('#discount_amountModal').text(percentage ? numberFormat(percentage) : numberFormat(discount_total_amount));

            let grand_total = numberFormat(Number(totalAmount));
            $('#gtotal').val(totalAmount);
            $('#finalTotal').text('{{ currency_icon() }}' + grand_total);
            $('#discount_total_amount').val(discount_total_amount);
            $('input[name=total_amount]').val(grand_total);
            $('#total_amountModal').text(grand_total);
            $('input[name=paying_amount]').val(grand_total);
            $('#total_amountModal2').text(grand_total);
            totalSummery();
        }

        const accountsList = @json($accounts);

        $(document).on('change', 'select[name="payment_type[]"]', function() {
            const accounts = accountsList.filter(account => account.account_type == $(this).val());

            if (accounts.length > 0) {
                let html = '<select name="account_id[]" class="form-control form-control-sm">';
                accounts.forEach(account => {
                    switch ($(this).val()) {
                        case 'bank':
                            html += `<option value="${account.id}">${account.bank_account_number} (${account.bank?.name})</option>`;
                            break;
                        case "mobile_banking":
                            html += `<option value="${account.id}">${account.mobile_number} (${account.mobile_bank_name})</option>`;
                            break;
                        case 'card':
                            html += `<option value="${account.id}">${account.card_number} (${account.bank?.name})</option>`;
                            break;
                    }
                });
                html += '</select>';
                $(this).parents('.payment-row').find('.account_info').html(html);
            }

            if ($(this).val() == 'cash' || $(this).val() == 'advance') {
                const cash = `<input type="text" name="account_id[]" class="form-control form-control-sm" value="${$(this).val()}" readonly>`;
                $(this).parents('.payment-row').find('.account_info').html(cash);
            }
        });

        $('.receive_cash').on('input', function() {
            const cash = parseFloat($(this).val()) || 0;
            const total = parseFloat($('#total_amountModal').text().replace(/[^0-9.]/g, '')) || 0;
            let change_amount = cash - total;

            if (change_amount < 0) {
                $('.change_amount').val(0);
            } else {
                $('.change_amount').val(change_amount.toFixed(2));
            }
        });

        $(document).on('input', '[name="paying_amount[]"]', function() {
            const amount = [];
            $('[name="paying_amount[]"]').each(function() {
                amount.push(parseFloat($(this).val()) || 0);
            });
            const amountVal = amount.reduce((a, b) => a + b, 0);
            $('#paid_amountModal').text('{{ currency_icon() }}' + amountVal.toFixed(2));

            let totalAmount = parseFloat($('#total_amountModal').text().replace(/[^0-9.]/g, '')) || 0;

            if (totalAmount > amountVal) {
                $('[name="total_due"]').val((totalAmount - amountVal).toFixed(2));
                $(".due-date").removeClass('d-none');
                $(".due").removeClass('d-none');
            } else {
                $(".due-date").addClass('d-none');
                $('[name="total_due"]').val(0);
            }
            calDue();
        });

        function modalHide(id) {
            $(id).modal('hide');
            $('.pos-footer').css('z-index', 9000);
            calDue();
        }

        $(document).on('keydown', function(event) {
            if (event.key === 'Escape' || event.keyCode === 27) {
                modalHide('#payment-modal');
            }
        });

        function paymentSubmit(e) {
            e.preventDefault();
            const formData = $('#checkoutForm').serialize();
            $.ajax({
                type: 'PUT',
                data: formData,
                url: "{{ route('admin.sales.update', $sale->id) }}",
                success: function(response) {
                    $(".product-table tbody").html('');
                    if (response['alert-type'] == 'success') {
                        toastr.success(response.message);
                        window.location.href = "{{ route('admin.sales.index') }}";
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function(response) {
                    if (response.status == 500) {
                        toastr.error("{{ __('Server error occurred') }}");
                    }
                    console.log(response);
                }
            });
        }

        function totalSummery() {
            const products = $('.product-table tbody > tr > .row_total');

            let total = 0;
            let itemCount = 0;

            products.each(function() {
                total += numberOnly($(this).text());
                itemCount++;
            });

            $('#total').val(total);
            $('#subtotal').val(total);
            $('#subtotalDisplay').text('{{ currency_icon() }}' + numberFormat(total));
            $('#titems').text(itemCount);

            // Discount
            const discount = $('#discount_total_amount').val() ? $('#discount_total_amount').val() : 0;
            const discountType = $('#discount_type').val();
            let discountAmount = 0;

            if (discountType == 2) {
                discountAmount = total * parseFloat(discount) / 100;
            } else {
                discountAmount = parseFloat(discount);
            }

            const afterDiscount = total - discountAmount;
            $('#gtotal').val(afterDiscount);

            // Tax
            const taxRate = parseFloat($('#taxRate').val()) || 0;
            const taxAmount = afterDiscount * (taxRate / 100);
            $('#taxAmount').val(taxAmount);
            $('#taxDisplay').text('{{ currency_icon() }}' + numberFormat(taxAmount));
            $('#taxRateDisplay').text(taxRate);

            // Grand Total
            const grandTotal = afterDiscount + taxAmount;
            $('#totalAmountWithVat').text('{{ currency_icon() }}' + numberFormat(grandTotal));
            $('#finalTotal').text('{{ currency_icon() }}' + numberFormat(grandTotal));

            calculateExtra();
        }

        function loadCustomer(id) {
            if (id && id != 'walk-in-customer') {
                $.ajax({
                    type: 'GET',
                    url: "{{ route('admin.customer.single', '') }}/" + id,
                    success: function(response) {
                        $('#previous_due').text(response.total_due);
                        $('.due').removeClass('d-none');
                        calDue();
                    }
                });
            } else {
                $('.due').addClass('d-none');
            }
        }

        function calDue() {
            let previous_due = parseFloat($('#previous_due').text()) || 0;
            let currentDue = parseFloat($('[name="total_due"]').val()) || 0;
            const totalDue = currentDue + previous_due;
            $('#due_amountModal').text('{{ currency_icon() }}' + numberFormat(totalDue > 0 ? totalDue : 0));
        }
    </script>
@endpush
