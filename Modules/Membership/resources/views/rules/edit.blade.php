@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Edit Loyalty Rule') }}</title>
@endsection
@section('content')
    <div class="card mb-5">
        <div class="card-header">
            <h4 class="section_title">{{ __('Edit Rule') }}: {{ $rule->name }}</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('membership.rules.update', $rule) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">{{ __('Rule Name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $rule->name) }}" required>
                            @error('name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="priority">{{ __('Priority') }}</label>
                            <input type="number" name="priority" id="priority" class="form-control" value="{{ old('priority', $rule->priority) }}" min="0" max="1000">
                            <small class="text-muted">{{ __('Higher priority rules are evaluated first (0-1000)') }}</small>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="description">{{ __('Description') }}</label>
                            <textarea name="description" id="description" class="form-control" rows="2">{{ old('description', $rule->description) }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="condition_type">{{ __('Condition Type') }} <span class="text-danger">*</span></label>
                            <select name="condition_type" id="condition_type" class="form-control" required>
                                <option value="category" {{ old('condition_type', $rule->condition_type) == 'category' ? 'selected' : '' }}>{{ __('Category') }}</option>
                                <option value="item" {{ old('condition_type', $rule->condition_type) == 'item' ? 'selected' : '' }}>{{ __('Item') }}</option>
                                <option value="amount" {{ old('condition_type', $rule->condition_type) == 'amount' ? 'selected' : '' }}>{{ __('Amount') }}</option>
                                <option value="time_period" {{ old('condition_type', $rule->condition_type) == 'time_period' ? 'selected' : '' }}>{{ __('Time Period') }}</option>
                                <option value="customer_group" {{ old('condition_type', $rule->condition_type) == 'customer_group' ? 'selected' : '' }}>{{ __('Customer Group') }}</option>
                            </select>
                            <small class="text-muted">{{ __('When this rule should apply (e.g., specific category, time period, purchase amount)') }}</small>
                        </div>
                    </div>

                    @php
                        $conditionValue = $rule->condition_value ?? [];
                    @endphp

                    <!-- Dynamic Condition Value Fields -->
                    <div class="col-md-6 condition-field" id="condition_category_field" style="display: none;">
                        <div class="form-group">
                            <label for="condition_categories">{{ __('Select Categories') }} <span class="text-danger">*</span></label>
                            <select name="condition_categories[]" id="condition_categories" class="form-control select2" multiple>
                                @foreach ($menuCategories as $category)
                                    <option value="{{ $category->id }}" {{ in_array($category->id, $conditionValue['categories'] ?? []) ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">{{ __('Select categories this rule applies to') }}</small>
                        </div>
                    </div>

                    <div class="col-md-6 condition-field" id="condition_item_field" style="display: none;">
                        <div class="form-group">
                            <label for="condition_items">{{ __('Select Items') }} <span class="text-danger">*</span></label>
                            <select name="condition_items[]" id="condition_items" class="form-control select2" multiple>
                                @foreach ($menuItems as $item)
                                    <option value="{{ $item->id }}" {{ in_array($item->id, $conditionValue['items'] ?? []) ? 'selected' : '' }}>{{ $item->name }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">{{ __('Select menu items this rule applies to') }}</small>
                        </div>
                    </div>

                    <div class="col-md-6 condition-field" id="condition_amount_field" style="display: none;">
                        <div class="form-group">
                            <label for="condition_min_amount">{{ __('Minimum Amount') }} <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="condition_min_amount" id="condition_min_amount" class="form-control" value="{{ old('condition_min_amount', $conditionValue['min_amount'] ?? '') }}">
                            <small class="text-muted">{{ __('Minimum purchase amount to trigger this rule') }}</small>
                        </div>
                    </div>

                    <div class="col-md-6 condition-field" id="condition_time_field" style="display: none;">
                        <div class="form-group">
                            <label for="condition_days">{{ __('Days of Week') }}</label>
                            <select name="condition_days[]" id="condition_days" class="form-control select2" multiple>
                                @php $selectedDays = $conditionValue['days'] ?? []; @endphp
                                <option value="0" {{ in_array(0, $selectedDays) ? 'selected' : '' }}>{{ __('Sunday') }}</option>
                                <option value="1" {{ in_array(1, $selectedDays) ? 'selected' : '' }}>{{ __('Monday') }}</option>
                                <option value="2" {{ in_array(2, $selectedDays) ? 'selected' : '' }}>{{ __('Tuesday') }}</option>
                                <option value="3" {{ in_array(3, $selectedDays) ? 'selected' : '' }}>{{ __('Wednesday') }}</option>
                                <option value="4" {{ in_array(4, $selectedDays) ? 'selected' : '' }}>{{ __('Thursday') }}</option>
                                <option value="5" {{ in_array(5, $selectedDays) ? 'selected' : '' }}>{{ __('Friday') }}</option>
                                <option value="6" {{ in_array(6, $selectedDays) ? 'selected' : '' }}>{{ __('Saturday') }}</option>
                            </select>
                            <small class="text-muted">{{ __('Select days when this rule is active (leave empty for all days)') }}</small>
                        </div>
                    </div>

                    <div class="col-md-6 condition-field" id="condition_customer_group_field" style="display: none;">
                        <div class="form-group">
                            <label for="condition_customer_segments">{{ __('Customer Segments') }} <span class="text-danger">*</span></label>
                            <select name="condition_customer_segments[]" id="condition_customer_segments" class="form-control select2" multiple>
                                @foreach ($customerSegments as $segment)
                                    <option value="{{ $segment->id }}" {{ in_array($segment->id, $conditionValue['segments'] ?? []) ? 'selected' : '' }}>{{ $segment->name }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">{{ __('Select customer segments this rule applies to') }}</small>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="action_type">{{ __('Action Type') }} <span class="text-danger">*</span></label>
                            <select name="action_type" id="action_type" class="form-control" required>
                                <option value="earn_points" {{ old('action_type', $rule->action_type) == 'earn_points' ? 'selected' : '' }}>{{ __('Earn Points') }}</option>
                                <option value="bonus_points" {{ old('action_type', $rule->action_type) == 'bonus_points' ? 'selected' : '' }}>{{ __('Bonus Points') }}</option>
                                <option value="multiply_points" {{ old('action_type', $rule->action_type) == 'multiply_points' ? 'selected' : '' }}>{{ __('Multiply Points') }}</option>
                                <option value="redeem_discount" {{ old('action_type', $rule->action_type) == 'redeem_discount' ? 'selected' : '' }}>{{ __('Redeem Discount') }}</option>
                            </select>
                            <small class="text-muted">{{ __('Earn: Override base rate | Bonus: Add extra points | Multiply: 2x, 3x points | Redeem: Special discount') }}</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="action_value">{{ __('Action Value') }} <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="action_value" id="action_value" class="form-control" value="{{ old('action_value', $rule->action_value) }}" required>
                            <small class="text-muted">{{ __('Points to earn/add, or multiplier value (e.g., 2 for double points)') }}</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="applies_to">{{ __('Applies To') }} <span class="text-danger">*</span></label>
                            <select name="applies_to" id="applies_to" class="form-control" required>
                                <option value="all" {{ old('applies_to', $rule->applies_to) == 'all' ? 'selected' : '' }}>{{ __('All') }}</option>
                                <option value="specific_items" {{ old('applies_to', $rule->applies_to) == 'specific_items' ? 'selected' : '' }}>{{ __('Specific Items') }}</option>
                                <option value="specific_categories" {{ old('applies_to', $rule->applies_to) == 'specific_categories' ? 'selected' : '' }}>{{ __('Specific Categories') }}</option>
                                <option value="specific_customers" {{ old('applies_to', $rule->applies_to) == 'specific_customers' ? 'selected' : '' }}>{{ __('Specific Customers') }}</option>
                            </select>
                            <small class="text-muted">{{ __('Scope of this rule - all purchases or specific items/categories/customers') }}</small>
                        </div>
                    </div>

                    @php
                        $applicableItems = json_decode($rule->applicable_items ?? '[]', true) ?: [];
                        $applicableCategories = json_decode($rule->applicable_categories ?? '[]', true) ?: [];
                        $applicableCustomerSegments = json_decode($rule->applicable_customer_segments ?? '[]', true) ?: [];
                    @endphp

                    <!-- Dynamic Applies To Fields -->
                    <div class="col-md-6 applies-to-field" id="applies_to_items_field" style="display: none;">
                        <div class="form-group">
                            <label for="applicable_items">{{ __('Select Items') }} <span class="text-danger">*</span></label>
                            <select name="applicable_items_select[]" id="applicable_items" class="form-control select2" multiple>
                                @foreach ($menuItems as $item)
                                    <option value="{{ $item->id }}" {{ in_array($item->id, $applicableItems) ? 'selected' : '' }}>{{ $item->name }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">{{ __('Select menu items this rule applies to') }}</small>
                        </div>
                    </div>

                    <div class="col-md-6 applies-to-field" id="applies_to_categories_field" style="display: none;">
                        <div class="form-group">
                            <label for="applicable_categories">{{ __('Select Categories') }} <span class="text-danger">*</span></label>
                            <select name="applicable_categories_select[]" id="applicable_categories" class="form-control select2" multiple>
                                @foreach ($menuCategories as $category)
                                    <option value="{{ $category->id }}" {{ in_array($category->id, $applicableCategories) ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">{{ __('Select categories this rule applies to') }}</small>
                        </div>
                    </div>

                    <div class="col-md-6 applies-to-field" id="applies_to_customers_field" style="display: none;">
                        <div class="form-group">
                            <label for="applicable_customer_segments">{{ __('Select Customer Segments') }} <span class="text-danger">*</span></label>
                            <select name="applicable_customer_segments_select[]" id="applicable_customer_segments" class="form-control select2" multiple>
                                @foreach ($customerSegments as $segment)
                                    <option value="{{ $segment->id }}" {{ in_array($segment->id, $applicableCustomerSegments) ? 'selected' : '' }}>{{ $segment->name }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">{{ __('Select customer segments this rule applies to') }}</small>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="start_date">{{ __('Start Date') }}</label>
                            <input type="date" name="start_date" id="start_date" class="form-control" value="{{ old('start_date', $rule->start_date?->format('Y-m-d')) }}">
                            <small class="text-muted">{{ __('Leave empty for no start date restriction') }}</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="end_date">{{ __('End Date') }}</label>
                            <input type="date" name="end_date" id="end_date" class="form-control" value="{{ old('end_date', $rule->end_date?->format('Y-m-d')) }}">
                            <small class="text-muted">{{ __('Leave empty for no end date restriction') }}</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="switch switch-square">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" class="switch-input" value="1" {{ old('is_active', $rule->is_active) ? 'checked' : '' }}>
                                <span class="switch-toggle-slider">
                                    <span class="switch-on"><i class="bx bx-check"></i></span>
                                    <span class="switch-off"><i class="bx bx-x"></i></span>
                                </span>
                                <span class="switch-label">{{ __('Active') }}</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="{{ route('membership.rules.index', ['program_id' => $rule->loyalty_program_id]) }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                    <button type="submit" class="btn btn-primary">{{ __('Update Rule') }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize select2 for multiple selects
        $('.select2').select2();

        function toggleConditionFields() {
            var conditionType = $('#condition_type').val();

            // Hide all condition fields first
            $('.condition-field').hide();

            // Show the relevant field based on condition type
            switch(conditionType) {
                case 'category':
                    $('#condition_category_field').show();
                    break;
                case 'item':
                    $('#condition_item_field').show();
                    break;
                case 'amount':
                    $('#condition_amount_field').show();
                    break;
                case 'time_period':
                    $('#condition_time_field').show();
                    break;
                case 'customer_group':
                    $('#condition_customer_group_field').show();
                    break;
            }
        }

        // Toggle Applies To fields
        function toggleAppliesToFields() {
            var appliesTo = $('#applies_to').val();

            // Hide all applies-to fields first
            $('.applies-to-field').hide();

            // Show the relevant field based on applies_to value
            switch(appliesTo) {
                case 'specific_items':
                    $('#applies_to_items_field').show();
                    break;
                case 'specific_categories':
                    $('#applies_to_categories_field').show();
                    break;
                case 'specific_customers':
                    $('#applies_to_customers_field').show();
                    break;
            }
        }

        // Initial toggle
        toggleConditionFields();
        toggleAppliesToFields();

        // On change
        $('#condition_type').on('change', function() {
            toggleConditionFields();
        });

        $('#applies_to').on('change', function() {
            toggleAppliesToFields();
        });
    });
</script>
@endpush
