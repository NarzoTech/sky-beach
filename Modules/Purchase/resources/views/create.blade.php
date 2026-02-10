@extends('admin.layouts.master')
@section('title', __('Create Purchase'))

@push('css')
    <style>
        .ingredient-name-wrapper {
            position: relative;
        }

        .ingredient-name-wrapper .ingredient-tooltip {
            visibility: hidden;
            opacity: 0;
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            background-color: #fff;
            color: #333;
            padding: 10px;
            border-radius: 8px;
            font-size: 13px;
            z-index: 1000;
            transition: opacity 0.3s, visibility 0.3s;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            margin-bottom: 8px;
            min-width: 150px;
            max-width: 250px;
            width: max-content;
            text-align: center;
        }

        .ingredient-name-wrapper .ingredient-tooltip::after {
            content: '';
            position: absolute;
            top: 100%;
            left: 50%;
            transform: translateX(-50%);
            border-width: 8px;
            border-style: solid;
            border-color: #fff transparent transparent transparent;
        }

        .ingredient-name-wrapper .ingredient-tooltip img {
            max-width: 120px;
            max-height: 120px;
            border-radius: 6px;
            margin-bottom: 8px;
            object-fit: cover;
        }

        .ingredient-name-wrapper .ingredient-tooltip .tooltip-name {
            font-weight: 600;
            word-wrap: break-word;
            white-space: normal;
            max-width: 200px;
            overflow-wrap: break-word;
        }

        .ingredient-name-wrapper:hover .ingredient-tooltip {
            visibility: visible;
            opacity: 1;
        }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-md-12">

            <form method="POST" action="{{ route('admin.purchase.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="card">

                    <div class="card-header d-flex justify-content-between">
                        <h4 class="section_title">{{ __('Create Purchase') }}</h4>
                        <div>
                            <a href="{{ route('admin.purchase.index') }}" class="btn btn-primary"><i
                                    class="fa fa-arrow-left"></i>{{ __('Back') }}</a>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 col-lg-4">
                                <div class="form-group">
                                    <label>{{ __('Supplier') }}</label>
                                    <select class="form-control select2" name="supplier_id">
                                        <option value="">{{ __('Select Supplier') }}</option>
                                        @foreach ($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}">
                                                {{ $supplier->company ?: $supplier->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('supplier_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4">
                                <div class="form-group">
                                    <label>{{ __('Invoice Number') }}</label>
                                    <input type="text" class="form-control" name="invoice_number"
                                        value="{{ old('invoice_number', $invoiceNumber) }}">
                                    @error('invoice_number')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4">
                                <div class="form-group">
                                    <label>{{ __('Memo No') }}</label>
                                    <input type="text" class="form-control" name="memo_no" value="{{ old('memo_no') }}">
                                    @error('memo_no')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4">
                                <div class="form-group">
                                    <label>{{ __('Purchase Date') }}</label>
                                    <input type="text" class="form-control datepicker" name="purchase_date"
                                        value="{{ old('purchase_date', formatDate(now())) }}" autocomplete="off">
                                    @error('purchase_date')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4">
                                <div class="form-group">
                                    <label>{{ __('Reference No') }}</label>
                                    <input type="text" class="form-control" name="reference_no"
                                        value="{{ old('reference_no') }}">
                                    @error('reference_no')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4">
                                <div class="form-group">
                                    <label>{{ __('Attachment') }}</label>
                                    <input type="file" class="form-control" name="attachment"
                                        value="{{ old('attachment') }}">
                                    @error('attachment')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            {{-- ingredient search box --}}
                            <div class="col-12">
                                <div class="form-group">
                                    <label>{{ __('Ingredient') }}</label>
                                    <select class="form-control select2" id="ingredient_id">
                                        <option value="">{{ __('Select Ingredient') }}</option>
                                        @foreach ($products as $ingredient)
                                            <option value="{{ $ingredient->id }}">{{ $ingredient->name }}
                                                ({{ $ingredient->sku }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-5">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>SL</th>
                                                <th>{{ __('Ingredient Name') }}</th>
                                                <th>{{ __('Unit') }}</th>
                                                <th>{{ __('Stock') }}</th>
                                                <th>{{ __('Quantity') }}</th>
                                                <th>{{ __('Purchase Price') }}</th>
                                                <th>{{ __('Sub Total') }}</th>
                                                <th class="text-center">
                                                    <i class="fas fa-trash text-danger"></i>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody id="purchase_table"> </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        {{-- summery --}}
                        <div class="row justify-content-end mt-5">
                            <div class="col-xxl-5 col-xl-6 col-lg-7">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label>{{ __('Item Count') }}</label>
                                            <input type="number" class="form-control" name="items" value="0"
                                                readonly>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label>{{ __('Total Amount') }}</label>
                                            <input type="total_amount" class="form-control" name="total_amount"
                                                value="0" readonly>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label>{{ __('Payment Type') }}</label>
                                            <div class="paymentsystem">
                                                @include('purchase::add-payment-method')
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label>{{ __('Due') }}</label>
                                            <input type="text" class="form-control" name="due_amount" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="card-action d-flex justify-content-end">
                                    <a href="{{ route('admin.purchase.index') }}"
                                        class="btn btn-danger me-2">{{ __('Cancel') }}</a>
                                    <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </div>
@endsection


@push('js')
    <script>
        'use strict';

        $(document).ready(function() {
            const accountsList = @json($accounts);
            $(document).on('change', 'select[name="payment_type[]"]', function() {
                const accounts = accountsList.filter(account => account.account_type == $(this).val());
                const accountInput = $(this).closest('.payment-row').find('.account');
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


                    accountInput.html(html);
                }

                if ($(this).val() == 'cash') {
                    accountInput.html('');
                    const cash =
                        `<input type="text" name="account_id[]" class="form-control" value="${$(this).val()}" readonly>`;

                    accountInput.html(cash);
                }
            });

            $('.addPayment').on('click', function() {
                const add = `@include('purchase::add-payment-method', ['add' => true])`

                $('.paymentsystem').append(add);
                $('select.nice-select').niceSelect();
            })
            $(document).on('click', '.removePayment', function() {
                $(this).parents('.payment-row').remove();
            })
        })


        function updateSerialNumbers() {
            $('#purchase_table tr').each(function(index) {
                $(this).find('td:first').text(index + 1);
            });
        }

        function addPurchaseRow(ingredient) {
            if ($('#purchase_table tr').length) {
                let exists = false;
                $('#purchase_table tr').each(function() {
                    if ($(this).find('input[name="ingredient_id[]"]').val() == ingredient.id) {
                        exists = true;
                    }
                });
                if (exists) {
                    alert('Ingredient already added!');
                    return;
                }
            }

            const cost = parseFloat(ingredient.purchase_price) || parseFloat(ingredient.cost) || 0;
            const serial = $('#purchase_table tr').length + 1;
            const purchaseUnit = ingredient.purchase_unit || ingredient.unit;
            const unitName = purchaseUnit ? purchaseUnit.ShortName : '-';
            const ingredientUnitId = purchaseUnit ? purchaseUnit.id : '';

            // Build unit family options for dropdown
            let unitOptions = '';
            if (purchaseUnit) {
                // Add purchase unit as default
                unitOptions += `<option value="${purchaseUnit.id}" selected>${purchaseUnit.name} (${purchaseUnit.ShortName})</option>`;

                // Add children units if exist
                if (purchaseUnit.children && purchaseUnit.children.length > 0) {
                    purchaseUnit.children.forEach(childUnit => {
                        unitOptions += `<option value="${childUnit.id}">${childUnit.name} (${childUnit.ShortName})</option>`;
                    });
                }
            }

            let tr = `
                <tr>
                    <td>${serial}</td>
                    <td>
                        <div class="ingredient-name-wrapper">
                            <input type="text" class="form-control" name="ingredient_name[]" value="${ingredient.name}" readonly>
                            <div class="ingredient-tooltip">
                                <img src="${ingredient.single_image}" alt="${ingredient.name}" onerror="this.src='{{ asset('backend/img/image_icon.png') }}'">
                                <div class="tooltip-name">${ingredient.name}</div>
                            </div>
                        </div>
                        <input type="hidden" name="ingredient_id[]" value="${ingredient.id}">
                        <input type="hidden" name="ingredient_base_unit_id[]" value="${ingredientUnitId}" class="ingredient_base_unit_id">
                    </td>
                    <td>
                        <select class="form-control purchase-unit-select" name="purchase_unit_id[]" data-ingredient-id="${ingredient.id}" style="width: 150px;">
                            ${unitOptions}
                        </select>
                    </td>
                    <td>
                        <input type="number" class="form-control" name="stock[]" value="${ingredient.stock}" readonly>
                        <small class="text-muted">${unitName}</small>
                    </td>
                    <td>
                        <input type="number" class="form-control quantity-input" name="quantity[]" value="1" min="0.01" step="0.01">
                    </td>
                    <td>
                        <input type="number" class="form-control unit-price-input" name="unit_price[]" value="${cost}" min="0" step="0.01">
                    </td>
                    <td>
                        <input type="number" class="form-control total-input" name="total[]" value="${cost}" readonly step="0.01">
                    </td>
                    <td>
                        <button type="button" class="btn btn-white" onclick="removePurchaseRow(this)"><i class="fas fa-trash text-danger"></i></button>
                    </td>
                </tr>
            `;

            // check if ingredient is already added
            if ($('#purchase_table tr').length > 0) {
                let isIngredientAdded = false;
                $('#purchase_table tr').each(function() {
                    let ingredient_id = $(this).find('input[name="ingredient_id[]"]').val();
                    if (ingredient_id == ingredient.id) {
                        isIngredientAdded = true;
                    }
                });
                if (isIngredientAdded) {
                    return;
                }
            }
            $('#purchase_table').append(tr);
            calculateTotalAmount();
        }

        function removePurchaseRow(row) {
            $(row).closest('tr').remove();
            updateSerialNumbers();
            calculateTotalAmount();
        }
        const ingredients = @json($products);
        $(document).on('change', '#ingredient_id', function() {
            let ingredient_id = $(this).val();
            const ingredient = ingredients.find(p => p.id == ingredient_id);
            addPurchaseRow(ingredient);
        });

        // Load unit family for purchase unit dropdown
        function loadUnitFamily(ingredientId, selectElement) {
            $.ajax({
                url: '{{ route("admin.ingredient.unit-family") }}',
                type: 'GET',
                data: { ingredient_id: ingredientId },
                success: function(response) {
                    if (response.units && response.units.length > 0) {
                        let currentValue = $(selectElement).val();
                        $(selectElement).empty();
                        response.units.forEach(unit => {
                            let selected = unit.id == currentValue ? 'selected' : '';
                            $(selectElement).append(`<option value="${unit.id}" ${selected}>${unit.name} (${unit.ShortName})</option>`);
                        });
                    }
                }
            });
        }

        // Calculate row total when quantity or price changes
        $(document).on('input', '.quantity-input, .unit-price-input', function() {
            let row = $(this).closest('tr');
            let quantity = parseFloat(row.find('.quantity-input').val()) || 0;
            let unitPrice = parseFloat(row.find('.unit-price-input').val()) || 0;
            let total = quantity * unitPrice;
            row.find('.total-input').val(total.toFixed(2));
            calculateTotalAmount();
        });

        // Debounce function to limit API calls
        let searchTimeout = null;

        // when search ingredient will not in the ingredient list. it will search from the database;
        $(document).on('input', '[aria-controls="select2-ingredient_id-results"]', function() {
            let input = $(this).val();

            // Clear previous timeout
            if (searchTimeout) {
                clearTimeout(searchTimeout);
            }

            if (input.length < 2) {
                return;
            }

            // check if input its in ingredient name or ingredient code
            const filteredIngredients = ingredients.filter(p =>
                p.name.toLowerCase().includes(input.toLowerCase()) ||
                (p.barcode && p.barcode.toLowerCase().includes(input.toLowerCase())) ||
                (p.sku && p.sku.toLowerCase().includes(input.toLowerCase()))
            );

            if (filteredIngredients.length == 0) {
                // Debounce the API call - wait 300ms after user stops typing
                searchTimeout = setTimeout(function() {
                    $.ajax({
                        url: "{{ route('admin.purchase.ingredient.search') }}",
                        type: 'POST',
                        data: {
                            keyword: input
                        },
                        success: function(response) {
                            if (response.status) {
                                response.data.forEach(ingredient => {
                                    // Check if ingredient already exists in the list
                                    const existingIngredient = ingredients.find(p => p.id ==
                                        ingredient.id);

                                    if (!existingIngredient) {
                                        ingredients.push(ingredient);
                                        $('#ingredient_id').append(
                                            `<option value="${ingredient.id}">${ingredient.name} (${ingredient.sku})</option>`
                                        );
                                    }
                                });
                            }
                        }
                    });
                }, 300);
            }
        })

        $(document).on('input', 'input[name="quantity[]"], input[name="unit_price[]"]', function() {
            var tr = $(this).closest('tr');
            var quantity = tr.find('input[name="quantity[]"]').val();
            var unit_price = tr.find('input[name="unit_price[]"]').val();
            var total = quantity * unit_price;
            tr.find('input[name="total[]"]').val(total);
            calculateTotalAmount();
        });

        $(document).on('input', "[name='unit_price[]']", function() {
            calculateTotalAmount();
        })

        $(document).on('change', '[name="payment_type"]', function() {
            let payment_type = $(this).val();
            if (payment_type != '') {
                $('[name="payment_method"]').val(payment_type);
            }
        })
        $(document).on('input', '[name="paid_amount[]"]', function() {
            calculateDue()
        })

        $(document).on('change', '[name="payment_type"]', function() {
            let payment_type = $(this).data('name');
            $('[name="payment_method"]').val(payment_type);
        })


        //

        function calculateTotalAmount() {

            let totalQuantity = 0;
            $('input[name="quantity[]"]').each(function() {
                totalQuantity += parseFloat($(this).val());
            });
            $('[name="items"]').val(totalQuantity);

            let totalAmount = 0;
            $('input[name="total[]"]').each(function() {
                totalAmount += parseFloat($(this).val());
            });
            $('[name="total_amount"]').val(totalAmount);
            $('[name="due_amount"]').val(totalAmount);
        }

        function calculateDue() {

            let totalAmount = $('[name="total_amount"]').val();
            let paidAmount = $('[name="paid_amount[]"]');

            let dueAmount = totalAmount;
            paidAmount.each(function() {
                dueAmount -= parseFloat($(this).val() || 0);
            })

            $('[name="due_amount"]').val(dueAmount);
        }


        // Form validation
        function validatePurchaseForm() {
            let errors = [];

            // Supplier validation
            if (!$('[name="supplier_id"]').val()) {
                errors.push('{{ __('Supplier is required') }}');
                $('[name="supplier_id"]').closest('.form-group').find('.text-danger').remove();
                $('[name="supplier_id"]').closest('.form-group').append(
                    '<span class="text-danger">{{ __('Supplier is required') }}</span>');
            } else {
                $('[name="supplier_id"]').closest('.form-group').find('.text-danger').remove();
            }

            // Invoice number validation
            if (!$('[name="invoice_number"]').val()) {
                errors.push('{{ __('Invoice number is required') }}');
                $('[name="invoice_number"]').closest('.form-group').find('.text-danger').remove();
                $('[name="invoice_number"]').closest('.form-group').append(
                    '<span class="text-danger">{{ __('Invoice number is required') }}</span>');
            } else {
                $('[name="invoice_number"]').closest('.form-group').find('.text-danger').remove();
            }

            // Purchase date validation
            if (!$('[name="purchase_date"]').val()) {
                errors.push('{{ __('Purchase date is required') }}');
                $('[name="purchase_date"]').closest('.form-group').find('.text-danger').remove();
                $('[name="purchase_date"]').closest('.form-group').append(
                    '<span class="text-danger">{{ __('Purchase date is required') }}</span>');
            } else {
                $('[name="purchase_date"]').closest('.form-group').find('.text-danger').remove();
            }

            // Ingredients validation
            if ($('#purchase_table tr').length === 0) {
                errors.push('{{ __('At least one ingredient is required') }}');
            }

            // Quantity validation
            let quantityValid = true;
            $('input[name="quantity[]"]').each(function() {
                if (!$(this).val() || parseFloat($(this).val()) <= 0) {
                    quantityValid = false;
                    $(this).addClass('is-invalid');
                } else {
                    $(this).removeClass('is-invalid');
                }
            });
            if (!quantityValid) {
                errors.push('{{ __('Quantity must be greater than 0') }}');
            }

            // Payment type validation
            let paymentTypeValid = true;
            $('[name="payment_type[]"]').each(function() {
                if (!$(this).val()) {
                    paymentTypeValid = false;
                    $(this).addClass('is-invalid');
                } else {
                    $(this).removeClass('is-invalid');
                }
            });
            if (!paymentTypeValid) {
                errors.push('{{ __('Payment type is required') }}');
            }

            // Paid amount validation
            let paidAmountValid = true;
            $('[name="paid_amount[]"]').each(function() {
                if ($(this).val() === '' || $(this).val() === null) {
                    paidAmountValid = false;
                    $(this).addClass('is-invalid');
                } else {
                    $(this).removeClass('is-invalid');
                }
            });
            if (!paidAmountValid) {
                errors.push('{{ __('Paid amount is required') }}');
            }

            return errors;
        }

        // Form submit handler
        $('form').on('submit', function(e) {
            let errors = validatePurchaseForm();

            if (errors.length > 0) {
                e.preventDefault();
                errors.forEach(function(error) {
                    toastr.error(error);
                });
                return false;
            }

            return true;
        });

        // Real-time validation on field change
        $(document).on('change', '[name="supplier_id"]', function() {
            if ($(this).val()) {
                $(this).closest('.form-group').find('.text-danger').remove();
            }
        });

        $(document).on('input', '[name="invoice_number"]', function() {
            if ($(this).val()) {
                $(this).closest('.form-group').find('.text-danger').remove();
            }
        });

        $(document).on('change', '[name="purchase_date"]', function() {
            if ($(this).val()) {
                $(this).closest('.form-group').find('.text-danger').remove();
            }
        });

        $(document).on('change', '[name="payment_type[]"]', function() {
            if ($(this).val()) {
                $(this).removeClass('is-invalid');
            }
        });

        $(document).on('input', '[name="paid_amount[]"]', function() {
            if ($(this).val() !== '') {
                $(this).removeClass('is-invalid');
            }
        });

        $(document).on('input', '[name="quantity[]"]', function() {
            if ($(this).val() && parseFloat($(this).val()) > 0) {
                $(this).removeClass('is-invalid');
            }
        });
    </script>
@endpush
