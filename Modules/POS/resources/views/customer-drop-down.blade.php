<option value="" disabled>{{ __('Select Customer') }}</option>
<option value="walk-in-customer" @if (isset($sale) && $sale->customer_id == 0) selected @endif>{{ __('walk-in-customer') }}</option>
@foreach ($customers as $customer)
    <option value="{{ $customer->id }}" @if (isset($sale) && $sale->customer_id == $customer->id) selected @endif data-discount="{{ $customer->group->discount }}">{{ $customer->name }}
        {{ $customer->phone ? '-' . $customer->phone : '' }}</option>
@endforeach
