@extends('admin.layouts.master')
@section('title', __('Stock Adjustments'))

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body pb-0">
                    <form class="search_form" action="" method="GET">
                        <div class="row">
                            <div class="col-xxl-2 col-md-6 col-lg-4">
                                <div class="form-group search-wrapper">
                                    <input type="text" name="keyword" value="{{ request()->get('keyword') }}"
                                        class="form-control" placeholder="Search..." autocomplete="off">
                                    <button type="submit">
                                        <i class='bx bx-search'></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-xxl-2 col-md-6 col-lg-4">
                                <div class="form-group">
                                    <select name="adjustment_type" class="form-control">
                                        <option value="">{{ __('All Types') }}</option>
                                        @foreach ($types as $key => $label)
                                            <option value="{{ $key }}" {{ request('adjustment_type') == $key ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-xxl-2 col-md-6 col-lg-4">
                                <div class="form-group">
                                    <select class="form-control select2" name="ingredient_id">
                                        <option value="">{{ __('All Ingredients') }}</option>
                                        @foreach ($ingredients as $ingredient)
                                            <option value="{{ $ingredient->id }}" {{ request('ingredient_id') == $ingredient->id ? 'selected' : '' }}>
                                                {{ $ingredient->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-xxl-2 col-md-6 col-lg-4">
                                <div class="form-group">
                                    <div class="input-group input-daterange">
                                        <input type="text" placeholder="From Date" class="form-control datepicker" name="from_date" value="{{ request('from_date') }}" autocomplete="off">
                                        <span class="input-group-text">to</span>
                                        <input type="text" placeholder="To Date" class="form-control datepicker" name="to_date" value="{{ request('to_date') }}" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                            <div class="col-xxl-2 col-md-6 col-lg-4">
                                <div class="form-group">
                                    <button type="button" class="btn bg-danger form-reset">{{ __('Reset') }}</button>
                                    <button type="submit" class="btn bg-primary">{{ __('Search') }}</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-5">
        <div class="card-header">
            <div class="card-header-title font-size-lg text-capitalize font-weight-normal">
                <h4 class="section_title">{{ __('Stock Adjustments') }}</h4>
            </div>
            <div class="btn-actions-pane-right actions-icon-btn">
                <a href="{{ route('admin.stock-adjustment.create') }}" class="btn btn-primary">
                    <i class="fa fa-plus"></i> {{ __('Add Adjustment') }}
                </a>
                <a href="{{ route('admin.stock-adjustment.wastage-summary') }}" class="btn btn-warning">
                    <i class="fa fa-chart-pie"></i> {{ __('Wastage Summary') }}
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive list_table">
                <table style="width: 100%;" class="table mb-3">
                    <thead>
                        <tr>
                            <th>{{ __('SN') }}</th>
                            <th>{{ __('Adjustment #') }}</th>
                            <th>{{ __('Date') }}</th>
                            <th>{{ __('Type') }}</th>
                            <th>{{ __('Ingredient') }}</th>
                            <th>{{ __('Quantity') }}</th>
                            <th>{{ __('Cost/Unit') }}</th>
                            <th>{{ __('Total Cost') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($adjustments as $index => $adjustment)
                            <tr>
                                <td>{{ $adjustments->firstItem() + $index }}</td>
                                <td>{{ $adjustment->adjustment_number }}</td>
                                <td>{{ $adjustment->adjustment_date->format('d-m-Y') }}</td>
                                <td>
                                    <span class="badge {{ $adjustment->isDecrease() ? 'bg-danger' : 'bg-success' }}">
                                        {{ $adjustment->adjustment_type_label }}
                                    </span>
                                </td>
                                <td>{{ $adjustment->ingredient->name ?? 'N/A' }}</td>
                                <td class="{{ $adjustment->quantity < 0 ? 'text-danger' : 'text-success' }}">
                                    {{ number_format(abs($adjustment->quantity), 2) }}
                                    {{ $adjustment->unit->name ?? '' }}
                                    ({{ $adjustment->quantity < 0 ? '-' : '+' }})
                                </td>
                                <td>{{ currency($adjustment->cost_per_unit) }}</td>
                                <td>{{ currency($adjustment->total_cost) }}</td>
                                <td>
                                    <span class="badge bg-{{ $adjustment->status === 'approved' ? 'success' : ($adjustment->status === 'pending' ? 'warning' : 'danger') }}">
                                        {{ ucfirst($adjustment->status) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-bs-toggle="dropdown">
                                            {{ __('Action') }}
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="{{ route('admin.stock-adjustment.show', $adjustment->id) }}">
                                                {{ __('View') }}
                                            </a>
                                            <a class="dropdown-item" href="{{ route('admin.stock-adjustment.edit', $adjustment->id) }}">
                                                {{ __('Edit') }}
                                            </a>
                                            <a href="javascript:;" class="dropdown-item" onclick="deleteData({{ $adjustment->id }})">
                                                {{ __('Delete') }}
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center">{{ __('No adjustments found') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="float-right">
                {{ $adjustments->links() }}
            </div>
        </div>
    </div>

    @push('js')
        <script>
            function deleteData(id) {
                let url = "{{ route('admin.stock-adjustment.destroy', ':id') }}"
                url = url.replace(':id', id);
                $("#deleteForm").attr("action", url);
                $('#deleteModal').modal('show');
            }
        </script>
    @endpush
@endsection
