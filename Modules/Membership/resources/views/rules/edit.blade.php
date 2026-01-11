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
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="action_value">{{ __('Action Value') }} <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="action_value" id="action_value" class="form-control" value="{{ old('action_value', $rule->action_value) }}" required>
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
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="start_date">{{ __('Start Date') }}</label>
                            <input type="date" name="start_date" id="start_date" class="form-control" value="{{ old('start_date', $rule->start_date?->format('Y-m-d')) }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="end_date">{{ __('End Date') }}</label>
                            <input type="date" name="end_date" id="end_date" class="form-control" value="{{ old('end_date', $rule->end_date?->format('Y-m-d')) }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="switch switch-square">
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
