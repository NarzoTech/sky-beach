<div class="modal fade" id="unitModal">
    <div class="modal-dialog">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title" id="unitModalTitle">{{ __('Create Unit') }}</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <!-- Modal body -->
            <div class="modal-body pt-0 pb-0">
                <form action="javascript:;" method="post" enctype="multipart/form-data" id="unitForm">
                    @csrf
                    <input type="hidden" name="_method" id="unitFormMethod" value="POST">
                    <input type="hidden" name="unit_id" id="unitFormId" value="">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label>{{ __('Name') }} <span class="text-danger">*</span></label>
                                <input type="text" id="unitName" class="form-control" name="name" required>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label>{{ __('Short Name') }} <span class="text-danger">*</span></label>
                                <input type="text" id="unitShortName" class="form-control" name="ShortName" required>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label>{{ __('Base Unit') }}</label>
                                <select name="base_unit" id="unitBaseUnit" class="form-control">
                                    <option value="">{{ __('Select Base Unit (None = This is a base unit)') }}</option>
                                    @foreach ($parentUnits ?? [] as $parentUnit)
                                        <option value="{{ $parentUnit->id }}">{{ $parentUnit->name }} ({{ $parentUnit->ShortName }})</option>
                                    @endforeach
                                </select>
                                <small class="text-muted">{{ __('Leave empty if this is a base unit (e.g., Kilogram, Liter)') }}</small>
                            </div>
                        </div>
                        <div class="col-12 unit-operator d-none">
                            <div class="form-group">
                                <label>{{ __('Operator') }} <span class="text-danger">*</span></label>
                                <select name="operator" id="unitOperator" class="form-control">
                                    <option value="*">{{ __('Multiply') }} (*)</option>
                                    <option value="/">{{ __('Divide') }} (/)</option>
                                </select>
                                <small class="text-muted">{{ __('How to convert from this unit to base unit') }}</small>
                            </div>
                        </div>
                        <div class="col-12 unit-operator-value d-none">
                            <div class="form-group">
                                <label>{{ __('Operator Value') }} <span class="text-danger">*</span></label>
                                <input type="number" step="0.0001" id="unitOperatorValue" class="form-control" name="operator_value" value="1">
                                <small class="text-muted" id="unitConversionHelp">{{ __('E.g., 1 Gram = 1/1000 Kilogram, so use Divide by 1000') }}</small>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label>{{ __('Status') }} </label>
                                <div class="d-flex flex-wrap gap-5 border rounded py-2 px-4">
                                    <div class="d-flex flex-wrap gap-2 align-items-center">
                                        <input id="unitActive" type="radio" name='status' value="1" checked />
                                        <label for="unitActive" class="mb-0">{{ __('Active') }} </label>
                                    </div>
                                    <div class="d-flex flex-wrap gap-2 align-items-center">
                                        <input id="unitInactive" type="radio" name='status' value="0" />
                                        <label for="unitInactive" class="mb-0">{{ __('Inactive') }} </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Modal footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">{{ __('Close') }}</button>
                <button type="submit" class="btn btn-primary" form="unitForm" id="unitFormSubmitBtn">
                    <i class="fa fa-save me-2"></i>
                    <span id="unitFormSubmitText">{{ __('Save') }}</span>
                </button>
            </div>

        </div>
    </div>
</div>

@push('js')
<script>
    $(document).ready(function() {
        // Show/hide operator fields based on base unit selection
        $('#unitBaseUnit').on('change', function() {
            const baseUnit = $(this).val();
            if (baseUnit) {
                $('.unit-operator').removeClass('d-none');
                $('.unit-operator-value').removeClass('d-none');
            } else {
                $('.unit-operator').addClass('d-none');
                $('.unit-operator-value').addClass('d-none');
            }
        });

        // Reset modal to create mode when opened fresh
        $('#unitModal').on('show.bs.modal', function(e) {
            // Only reset if not triggered by edit button
            if (!$(e.relatedTarget).hasClass('edit-unit-btn')) {
                resetUnitModal();
            }
        });

        // Reset modal when closed
        $('#unitModal').on('hidden.bs.modal', function() {
            resetUnitModal();
        });

        function resetUnitModal() {
            $('#unitModalTitle').text('{{ __('Create Unit') }}');
            $('#unitFormSubmitText').text('{{ __('Save') }}');
            $('#unitForm').trigger('reset');
            $('#unitFormMethod').val('POST');
            $('#unitFormId').val('');
            $('#unitBaseUnit').val('');
            $('.unit-operator').addClass('d-none');
            $('.unit-operator-value').addClass('d-none');
            $('input[name="status"][value="1"]').prop('checked', true);
        }

        // Edit unit function - can be called from anywhere
        window.editUnit = function(unitId) {
            $.ajax({
                url: "{{ route('admin.unit.edit', '') }}/" + unitId,
                type: 'GET',
                beforeSend: function() {
                    // Show loading if preloader exists
                    if ($('.preloader_area').length) {
                        $('.preloader_area').removeClass('d-none');
                    }
                },
                success: function(response) {
                    $('#unitModalTitle').text('{{ __('Edit Unit') }}');
                    $('#unitFormSubmitText').text('{{ __('Update') }}');
                    $('#unitFormMethod').val('PUT');
                    $('#unitFormId').val(response.id);
                    $('#unitName').val(response.name);
                    $('#unitShortName').val(response.ShortName);
                    $('#unitBaseUnit').val(response.base_unit);
                    $('#unitOperator').val(response.operator);
                    $('#unitOperatorValue').val(response.operator_value || 1);

                    if (response.base_unit) {
                        $('.unit-operator').removeClass('d-none');
                        $('.unit-operator-value').removeClass('d-none');
                    } else {
                        $('.unit-operator').addClass('d-none');
                        $('.unit-operator-value').addClass('d-none');
                    }

                    $('input[name="status"][value="' + response.status + '"]').prop('checked', true);

                    $('#unitModal').modal('show');

                    if ($('.preloader_area').length) {
                        $('.preloader_area').addClass('d-none');
                    }
                },
                error: function(error) {
                    console.log(error);
                    toastr.error('{{ __('Failed to load unit data') }}');
                    if ($('.preloader_area').length) {
                        $('.preloader_area').addClass('d-none');
                    }
                }
            });
        };

        // Form submission handler
        $('#unitForm').on('submit', function(e) {
            e.preventDefault();

            const isEdit = $('#unitFormMethod').val() === 'PUT';
            const unitId = $('#unitFormId').val();
            let url = "{{ route('admin.unit.store') }}";

            if (isEdit && unitId) {
                url = "{{ route('admin.unit.update', '') }}/" + unitId;
            }

            $.ajax({
                url: url,
                type: 'POST',
                data: $('#unitForm').serialize(),
                success: function(response) {
                    if (response.status == 200) {
                        toastr.success(response.message || (isEdit ? '{{ __('Unit updated successfully') }}' : '{{ __('Unit created successfully') }}'));
                        $('#unitModal').modal('hide');

                        // If creating new unit, append to dropdowns
                        if (!isEdit && response.unit) {
                            let html = `<option value="${response.unit.id}">${response.unit.name} (${response.unit.ShortName})</option>`;

                            // Append to all unit select dropdowns on the page
                            if ($('#purchase_unit_id').length) {
                                $('#purchase_unit_id').append(html);
                            }
                            if ($('#consumption_unit_id').length) {
                                $('#consumption_unit_id').append(html);
                            }
                            if ($('#unit_id').length) {
                                $('#unit_id').append(html);
                            }

                            // Also add to base unit dropdown in modal if it's a base unit
                            if (!response.unit.base_unit) {
                                $('#unitBaseUnit').append(html);
                            }
                        }

                        // If on unit index page, reload the page to show changes
                        if (window.location.href.includes('/unit')) {
                            window.location.reload();
                        }
                    } else {
                        toastr.error(response.message || '{{ __('Something went wrong') }}');
                    }
                },
                error: function(error) {
                    if (error.responseJSON && error.responseJSON.errors) {
                        Object.keys(error.responseJSON.errors).forEach(function(key) {
                            toastr.error(error.responseJSON.errors[key][0]);
                        });
                    } else if (error.responseJSON && error.responseJSON.message) {
                        toastr.error(error.responseJSON.message);
                    } else {
                        toastr.error('{{ __('Something went wrong') }}');
                    }
                }
            });
        });
    });
</script>
@endpush
