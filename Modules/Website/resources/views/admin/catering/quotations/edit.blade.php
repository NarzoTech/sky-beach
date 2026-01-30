@extends('admin.layouts.master')

@section('title')
    <title>{{ __('Edit Quotation') }} - {{ $quotation->quotation_number }}</title>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">{{ __('Edit Quotation') }}</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.restaurant.catering.quotations.index') }}">{{ __('Quotations') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.restaurant.catering.quotations.show', $quotation) }}">{{ $quotation->quotation_number }}</a></li>
                    <li class="breadcrumb-item active">{{ __('Edit') }}</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.restaurant.catering.quotations.show', $quotation) }}" class="btn btn-outline-secondary">
            <i class="bx bx-arrow-back me-1"></i>{{ __('Back') }}
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('admin.restaurant.catering.quotations.update', $quotation) }}" method="POST" id="quotationForm">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-lg-8">
                <!-- Customer Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bx bx-user me-2"></i>{{ __('Customer Information') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">{{ __('Full Name') }} <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', $quotation->name) }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">{{ __('Email') }} <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" value="{{ old('email', $quotation->email) }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">{{ __('Phone') }} <span class="text-danger">*</span></label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone', $quotation->phone) }}" required>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Event Details -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bx bx-calendar-event me-2"></i>{{ __('Event Details') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">{{ __('Event Type') }} <span class="text-danger">*</span></label>
                                <select name="event_type" class="form-select" required>
                                    <option value="">{{ __('Select Event Type') }}</option>
                                    @foreach($eventTypes as $key => $label)
                                        <option value="{{ $key }}" {{ old('event_type', $quotation->event_type) === $key ? 'selected' : '' }}>{{ __($label) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">{{ __('Event Date') }} <span class="text-danger">*</span></label>
                                <input type="date" name="event_date" class="form-control" value="{{ old('event_date', $quotation->event_date->format('Y-m-d')) }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">{{ __('Event Time') }}</label>
                                <input type="time" name="event_time" class="form-control" value="{{ old('event_time', $quotation->event_time ? \Carbon\Carbon::parse($quotation->event_time)->format('H:i') : '') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">{{ __('Number of Guests') }} <span class="text-danger">*</span></label>
                                <input type="number" name="guest_count" id="guest_count" class="form-control" value="{{ old('guest_count', $quotation->guest_count) }}" min="1" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">{{ __('Package') }}</label>
                                <select name="package_id" id="package_id" class="form-select">
                                    <option value="">{{ __('Custom (No Package)') }}</option>
                                    @foreach($packages as $package)
                                        <option value="{{ $package->id }}" data-price="{{ $package->price_per_person }}" {{ old('package_id', $quotation->package_id) == $package->id ? 'selected' : '' }}>
                                            {{ $package->name }} ({{ currency($package->price_per_person) }}/person)
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">{{ __('Guest Estimate') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-success text-white" id="guest-estimate">{{ currency(0) }}</span>
                                    <button type="button" class="btn btn-outline-success" id="applyEstimate">{{ __('Apply') }}</button>
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label">{{ __('Venue Address') }}</label>
                                <textarea name="venue_address" class="form-control" rows="2">{{ old('venue_address', $quotation->venue_address) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quotation Items -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bx bx-list-ul me-2"></i>{{ __('Quotation Items') }}</h5>
                        <button type="button" class="btn btn-primary btn-sm" id="add-item">
                            <i class="bx bx-plus me-1"></i>{{ __('Add Item') }}
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="items-table">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 40%;">{{ __('Item / Ingredient') }}</th>
                                        <th style="width: 15%;">{{ __('Quantity') }}</th>
                                        <th style="width: 20%;">{{ __('Unit Price') }}</th>
                                        <th style="width: 20%;">{{ __('Total') }}</th>
                                        <th style="width: 5%;"></th>
                                    </tr>
                                </thead>
                                <tbody id="quotation-items">
                                    @if($quotation->quotation_items && count($quotation->quotation_items) > 0)
                                        @foreach($quotation->quotation_items as $index => $item)
                                            <tr class="quotation-item" data-index="{{ $index }}">
                                                <td>
                                                    <input type="text" name="items[{{ $index }}][description]" class="form-control item-description" placeholder="{{ __('Enter item or ingredient name') }}" value="{{ $item['description'] }}" required>
                                                </td>
                                                <td>
                                                    <input type="number" name="items[{{ $index }}][quantity]" class="form-control item-qty" value="{{ $item['quantity'] }}" min="1" required>
                                                </td>
                                                <td>
                                                    <input type="number" name="items[{{ $index }}][unit_price]" class="form-control item-price" value="{{ $item['unit_price'] }}" step="0.01" min="0" required>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control item-total" value="{{ currency($item['total'] ?? $item['quantity'] * $item['unit_price']) }}" readonly>
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-sm btn-icon btn-outline-danger remove-item">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr class="quotation-item" data-index="0">
                                            <td>
                                                <input type="text" name="items[0][description]" class="form-control item-description" placeholder="{{ __('Enter item or ingredient name') }}" required>
                                            </td>
                                            <td>
                                                <input type="number" name="items[0][quantity]" class="form-control item-qty" value="1" min="1" required>
                                            </td>
                                            <td>
                                                <input type="number" name="items[0][unit_price]" class="form-control item-price" value="0" step="0.01" min="0" required>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control item-total" value="{{ currency(0) }}" readonly>
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-icon btn-outline-danger remove-item">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Notes & Terms -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bx bx-note me-2"></i>{{ __('Notes & Terms') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">{{ __('Quotation Notes') }}</label>
                                <textarea name="quotation_notes" class="form-control" rows="3" placeholder="{{ __('Additional notes for the customer...') }}">{{ old('quotation_notes', $quotation->quotation_notes) }}</textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label">{{ __('Terms & Conditions') }}</label>
                                <textarea name="quotation_terms" class="form-control" rows="3" placeholder="{{ __('Payment terms, cancellation policy, etc...') }}">{{ old('quotation_terms', $quotation->quotation_terms) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Pricing Summary -->
                <div class="card mb-4 sticky-top" style="top: 80px;">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bx bx-calculator me-2"></i>{{ __('Pricing Summary') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">{{ __('Discount') }}</label>
                            <div class="input-group">
                                <input type="number" name="discount" id="discount" class="form-control" value="{{ old('discount', $quotation->quotation_discount) }}" step="0.01" min="0">
                                <select name="discount_type" id="discount_type" class="form-select" style="max-width: 100px;">
                                    <option value="fixed" {{ old('discount_type', $quotation->quotation_discount_type) === 'fixed' ? 'selected' : '' }}>{{ currency_icon() }}</option>
                                    <option value="percentage" {{ old('discount_type', $quotation->quotation_discount_type) === 'percentage' ? 'selected' : '' }}>%</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Tax Rate') }} (%)</label>
                            <input type="number" name="tax_rate" id="tax_rate" class="form-control" value="{{ old('tax_rate', $quotation->quotation_tax_rate) }}" step="0.01" min="0" max="100">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Delivery Fee') }}</label>
                            <input type="number" name="delivery_fee" id="delivery_fee" class="form-control" value="{{ old('delivery_fee', $quotation->quotation_delivery_fee) }}" step="0.01" min="0">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Valid Until') }}</label>
                            <input type="date" name="valid_until" class="form-control" value="{{ old('valid_until', $quotation->quotation_valid_until ? $quotation->quotation_valid_until->format('Y-m-d') : '') }}">
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ __('Subtotal') }}:</span>
                            <span class="fw-semibold" id="summary-subtotal">{{ currency(0) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 text-success" id="summary-discount-row" style="display: none;">
                            <span>{{ __('Discount') }}:</span>
                            <span id="summary-discount">-{{ currency(0) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2" id="summary-tax-row" style="display: none;">
                            <span>{{ __('Tax') }}:</span>
                            <span id="summary-tax">{{ currency(0) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2" id="summary-delivery-row" style="display: none;">
                            <span>{{ __('Delivery Fee') }}:</span>
                            <span id="summary-delivery">{{ currency(0) }}</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <strong class="fs-5">{{ __('Grand Total') }}:</strong>
                            <strong class="fs-5 text-primary" id="summary-total">{{ currency(0) }}</strong>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary w-100 btn-lg">
                                <i class="bx bx-save me-1"></i>{{ __('Update Quotation') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const currencySymbol = '{{ currency_icon() }}';
    let itemIndex = {{ $quotation->quotation_items ? count($quotation->quotation_items) : 1 }};

    // Add new item row
    document.getElementById('add-item').addEventListener('click', function() {
        const tbody = document.getElementById('quotation-items');
        const newRow = document.createElement('tr');
        newRow.className = 'quotation-item';
        newRow.dataset.index = itemIndex;
        newRow.innerHTML = `
            <td>
                <input type="text" name="items[${itemIndex}][description]" class="form-control item-description" placeholder="{{ __('Enter item or ingredient name') }}" required>
            </td>
            <td>
                <input type="number" name="items[${itemIndex}][quantity]" class="form-control item-qty" value="1" min="1" required>
            </td>
            <td>
                <input type="number" name="items[${itemIndex}][unit_price]" class="form-control item-price" value="0" step="0.01" min="0" required>
            </td>
            <td>
                <input type="text" class="form-control item-total" value="${currencySymbol}0.00" readonly>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-icon btn-outline-danger remove-item">
                    <i class="bx bx-trash"></i>
                </button>
            </td>
        `;
        tbody.appendChild(newRow);
        itemIndex++;
        attachEventHandlers();
        calculateTotals();
    });

    // Remove item row
    function attachEventHandlers() {
        document.querySelectorAll('.remove-item').forEach(btn => {
            btn.onclick = function() {
                const items = document.querySelectorAll('.quotation-item');
                if (items.length > 1) {
                    this.closest('.quotation-item').remove();
                    calculateTotals();
                }
            };
        });

        document.querySelectorAll('.item-qty, .item-price').forEach(input => {
            input.oninput = function() {
                updateRowTotal(this.closest('.quotation-item'));
                calculateTotals();
            };
        });
    }

    function updateRowTotal(row) {
        const qty = parseFloat(row.querySelector('.item-qty').value) || 0;
        const price = parseFloat(row.querySelector('.item-price').value) || 0;
        const total = qty * price;
        row.querySelector('.item-total').value = currencySymbol + total.toFixed(2);
    }

    function calculateTotals() {
        let subtotal = 0;

        document.querySelectorAll('.quotation-item').forEach(row => {
            const qty = parseFloat(row.querySelector('.item-qty').value) || 0;
            const price = parseFloat(row.querySelector('.item-price').value) || 0;
            subtotal += qty * price;
        });

        const discount = parseFloat(document.getElementById('discount').value) || 0;
        const discountType = document.getElementById('discount_type').value;
        const taxRate = parseFloat(document.getElementById('tax_rate').value) || 0;
        const deliveryFee = parseFloat(document.getElementById('delivery_fee').value) || 0;

        let discountAmount = 0;
        if (discountType === 'percentage') {
            discountAmount = subtotal * (discount / 100);
        } else {
            discountAmount = discount;
        }

        const afterDiscount = subtotal - discountAmount;
        const taxAmount = afterDiscount * (taxRate / 100);
        const grandTotal = afterDiscount + taxAmount + deliveryFee;

        // Update summary
        document.getElementById('summary-subtotal').textContent = currencySymbol + subtotal.toFixed(2);
        document.getElementById('summary-discount').textContent = '-' + currencySymbol + discountAmount.toFixed(2);
        document.getElementById('summary-tax').textContent = currencySymbol + taxAmount.toFixed(2);
        document.getElementById('summary-delivery').textContent = currencySymbol + deliveryFee.toFixed(2);
        document.getElementById('summary-total').textContent = currencySymbol + grandTotal.toFixed(2);

        // Show/hide rows
        document.getElementById('summary-discount-row').style.display = discountAmount > 0 ? 'flex' : 'none';
        document.getElementById('summary-tax-row').style.display = taxAmount > 0 ? 'flex' : 'none';
        document.getElementById('summary-delivery-row').style.display = deliveryFee > 0 ? 'flex' : 'none';
    }

    // Guest estimate calculation
    function calculateGuestEstimate() {
        const guestCount = parseInt(document.getElementById('guest_count').value) || 0;
        const packageSelect = document.getElementById('package_id');
        const selectedOption = packageSelect.options[packageSelect.selectedIndex];
        const pricePerPerson = parseFloat(selectedOption.dataset.price) || 0;
        const estimate = guestCount * pricePerPerson;
        document.getElementById('guest-estimate').textContent = currencySymbol + estimate.toFixed(2);
    }

    // Apply guest estimate to first item
    document.getElementById('applyEstimate').addEventListener('click', function() {
        const guestCount = parseInt(document.getElementById('guest_count').value) || 0;
        const packageSelect = document.getElementById('package_id');
        const selectedOption = packageSelect.options[packageSelect.selectedIndex];
        const pricePerPerson = parseFloat(selectedOption.dataset.price) || 0;

        if (pricePerPerson > 0) {
            const firstRow = document.querySelector('.quotation-item');
            const packageName = selectedOption.text.split('(')[0].trim();
            firstRow.querySelector('.item-description').value = packageName + ' (' + guestCount + ' guests)';
            firstRow.querySelector('.item-qty').value = guestCount;
            firstRow.querySelector('.item-price').value = pricePerPerson;
            updateRowTotal(firstRow);
            calculateTotals();
        }
    });

    document.getElementById('guest_count').addEventListener('input', calculateGuestEstimate);
    document.getElementById('package_id').addEventListener('change', calculateGuestEstimate);

    // Pricing inputs
    document.getElementById('discount').addEventListener('input', calculateTotals);
    document.getElementById('discount_type').addEventListener('change', calculateTotals);
    document.getElementById('tax_rate').addEventListener('input', calculateTotals);
    document.getElementById('delivery_fee').addEventListener('input', calculateTotals);

    // Initial setup
    attachEventHandlers();
    calculateTotals();
    calculateGuestEstimate();
});
</script>
@endpush
