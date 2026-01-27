@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Create Quotation') }}</title>
@endsection

@section('content')
    <div class="main-content">
        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-md-12">
                        <form method="POST" action="{{ route('admin.quotation.store') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <div class="section_title">{{ __('Create Quotation') }}</div>
                                    <a href="{{ route('admin.quotation.index') }}" class="btn btn-outline-secondary btn-sm">
                                        <i class="fas fa-arrow-left me-1"></i>{{ __('Back') }}
                                    </a>
                                </div>

                                <div class="card-body">
                                    <div class="row">
                                        <!-- Customer Selection with Add New Option -->
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>{{ __('Customer') }} <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <select class="form-control select2" name="customer_id" id="customer_id" required>
                                                        <option value="">{{ __('Select Customer') }}</option>
                                                        @foreach ($customers as $customer)
                                                            <option value="{{ $customer->id }}">{{ $customer->name }} ({{ $customer->phone ?? $customer->email }})</option>
                                                        @endforeach
                                                    </select>
                                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCustomerModal">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                </div>
                                                @error('customer_id')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>{{ __('Date') }} <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control datepicker" name="date"
                                                    value="{{ formatDate(now()) }}" autocomplete="off" required>
                                                @error('date')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Quotation Items -->
                                    <div class="row mt-4">
                                        <div class="col-12">
                                            <h6 class="mb-3">{{ __('Quotation Items') }}</h6>
                                            <div class="table-responsive">
                                                <table class="table table-bordered" id="quotation_items_table">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th style="width: 50%">{{ __('Description') }}</th>
                                                            <th style="width: 15%" class="text-center">{{ __('Quantity') }}</th>
                                                            <th style="width: 20%" class="text-end">{{ __('Price') }}</th>
                                                            <th style="width: 15%" class="text-end">{{ __('Total') }}</th>
                                                            <th style="width: 50px" class="text-center"></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="quotation_table">
                                                        <tr>
                                                            <td>
                                                                <input type="text" class="form-control" name="description[]" placeholder="{{ __('Item description') }}" required>
                                                            </td>
                                                            <td>
                                                                <input type="number" class="form-control text-center item-qty" name="quantity[]" value="1" min="1" required>
                                                            </td>
                                                            <td>
                                                                <input type="number" class="form-control text-end item-price" name="unit_price[]" value="0" min="0" step="0.01" required>
                                                            </td>
                                                            <td>
                                                                <input type="number" class="form-control text-end item-total" name="total[]" value="0" readonly step="0.01">
                                                            </td>
                                                            <td class="text-center">
                                                                <button type="button" class="btn btn-sm btn-outline-danger remove-row" disabled>
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <button type="button" class="btn btn-outline-primary btn-sm" id="add_item_btn">
                                                <i class="fas fa-plus me-1"></i>{{ __('Add Item') }}
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Summary -->
                                    <div class="row justify-content-end mt-4">
                                        <div class="col-md-5">
                                            <table class="table table-sm">
                                                <tr>
                                                    <td class="border-0"><strong>{{ __('Subtotal') }}</strong></td>
                                                    <td class="border-0 text-end">
                                                        <input type="number" class="form-control form-control-sm text-end" name="subtotal" value="0" readonly step="0.01">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="border-0"><strong>{{ __('Discount') }}</strong></td>
                                                    <td class="border-0 text-end">
                                                        <input type="text" class="form-control form-control-sm text-end" name="discount" value="" placeholder="0 or 10%">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="border-0"><strong>{{ __('Tax/VAT') }}</strong></td>
                                                    <td class="border-0 text-end">
                                                        <input type="text" class="form-control form-control-sm text-end" name="vat" value="" placeholder="0 or 15%">
                                                    </td>
                                                </tr>
                                                <tr class="table-primary">
                                                    <td><strong class="fs-5">{{ __('Total') }}</strong></td>
                                                    <td class="text-end">
                                                        <input type="number" class="form-control form-control-sm text-end fw-bold fs-5" name="total_amount" value="0" readonly>
                                                        <input type="hidden" name="after_discount" value="0">
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>

                                    <!-- Notes -->
                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label>{{ __('Notes / Terms & Conditions') }}</label>
                                                <textarea name="note" class="form-control" rows="3" placeholder="{{ __('Add any notes or terms for this quotation...') }}"></textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Submit Buttons -->
                                    <div class="row mt-4">
                                        <div class="col-12">
                                            <div class="d-flex justify-content-end gap-2">
                                                <a href="{{ route('admin.quotation.index') }}" class="btn btn-outline-secondary">
                                                    {{ __('Cancel') }}
                                                </a>
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fas fa-save me-1"></i>{{ __('Save Quotation') }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Add Customer Modal -->
    <div class="modal fade" id="addCustomerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Add New Customer') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addCustomerForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">{{ __('Name') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="customer_name" id="customer_name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Phone') }}</label>
                            <input type="text" class="form-control" name="customer_phone" id="customer_phone">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Email') }}</label>
                            <input type="email" class="form-control" name="customer_email" id="customer_email">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Address') }}</label>
                            <textarea class="form-control" name="customer_address" id="customer_address" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-primary" id="saveCustomerBtn">
                            <i class="fas fa-save me-1"></i>{{ __('Save Customer') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        'use strict';

        $(document).ready(function() {
            // Add new item row
            $('#add_item_btn').on('click', function() {
                addNewRow();
            });

            // Calculate on input change
            $(document).on('input', '.item-qty, .item-price', function() {
                calculateRowTotal($(this).closest('tr'));
                calculateTotals();
            });

            // Remove row
            $(document).on('click', '.remove-row', function() {
                $(this).closest('tr').remove();
                updateRemoveButtons();
                calculateTotals();
            });

            // Discount/VAT change
            $('input[name="discount"], input[name="vat"]').on('input', function() {
                calculateTotals();
            });

            // Save new customer
            $('#addCustomerForm').on('submit', function(e) {
                e.preventDefault();
                saveNewCustomer();
            });

            // Initial calculation
            calculateTotals();
        });

        function addNewRow() {
            const row = `
                <tr>
                    <td>
                        <input type="text" class="form-control" name="description[]" placeholder="{{ __('Item description') }}" required>
                    </td>
                    <td>
                        <input type="number" class="form-control text-center item-qty" name="quantity[]" value="1" min="1" required>
                    </td>
                    <td>
                        <input type="number" class="form-control text-end item-price" name="unit_price[]" value="0" min="0" step="0.01" required>
                    </td>
                    <td>
                        <input type="number" class="form-control text-end item-total" name="total[]" value="0" readonly step="0.01">
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-outline-danger remove-row">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
            $('#quotation_table').append(row);
            updateRemoveButtons();
        }

        function updateRemoveButtons() {
            const rows = $('#quotation_table tr');
            if (rows.length === 1) {
                rows.find('.remove-row').prop('disabled', true);
            } else {
                rows.find('.remove-row').prop('disabled', false);
            }
        }

        function calculateRowTotal(row) {
            const qty = parseFloat(row.find('.item-qty').val()) || 0;
            const price = parseFloat(row.find('.item-price').val()) || 0;
            row.find('.item-total').val((qty * price).toFixed(2));
        }

        function calculateTotals() {
            let subtotal = 0;
            $('.item-total').each(function() {
                subtotal += parseFloat($(this).val()) || 0;
            });
            $('input[name="subtotal"]').val(subtotal.toFixed(2));

            // Discount
            const discountVal = $('input[name="discount"]').val();
            let discountAmount = 0;
            if (discountVal) {
                if (discountVal.includes('%')) {
                    discountAmount = subtotal * (parseFloat(discountVal) / 100);
                } else {
                    discountAmount = parseFloat(discountVal) || 0;
                }
            }

            const afterDiscount = subtotal - discountAmount;
            $('input[name="after_discount"]').val(afterDiscount.toFixed(2));

            // VAT/Tax
            const vatVal = $('input[name="vat"]').val();
            let vatAmount = 0;
            if (vatVal) {
                if (vatVal.includes('%')) {
                    vatAmount = afterDiscount * (parseFloat(vatVal) / 100);
                } else {
                    vatAmount = parseFloat(vatVal) || 0;
                }
            }

            const total = afterDiscount + vatAmount;
            $('input[name="total_amount"]').val(total.toFixed(2));
        }

        function saveNewCustomer() {
            const name = $('#customer_name').val();
            const phone = $('#customer_phone').val();
            const email = $('#customer_email').val();
            const address = $('#customer_address').val();

            if (!name) {
                alert('{{ __("Please enter customer name") }}');
                return;
            }

            $('#saveCustomerBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>{{ __("Saving...") }}');

            $.ajax({
                url: '{{ route("admin.user.quick-store") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    name: name,
                    phone: phone,
                    email: email,
                    address: address
                },
                success: function(response) {
                    if (response.success) {
                        // Add new customer to select
                        const newOption = new Option(response.customer.name + ' (' + (response.customer.phone || response.customer.email || '') + ')', response.customer.id, true, true);
                        $('#customer_id').append(newOption).trigger('change');

                        // Close modal and reset form
                        $('#addCustomerModal').modal('hide');
                        $('#addCustomerForm')[0].reset();

                        // Show success message
                        if (typeof toastr !== 'undefined') {
                            toastr.success('{{ __("Customer created successfully") }}');
                        }
                    } else {
                        alert(response.message || '{{ __("Failed to create customer") }}');
                    }
                },
                error: function(xhr) {
                    alert(xhr.responseJSON?.message || '{{ __("Failed to create customer") }}');
                },
                complete: function() {
                    $('#saveCustomerBtn').prop('disabled', false).html('<i class="fas fa-save me-1"></i>{{ __("Save Customer") }}');
                }
            });
        }
    </script>
@endpush
