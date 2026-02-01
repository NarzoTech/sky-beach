@extends('admin.layouts.master')
@section('title', __('Loyalty Customers'))
@section('content')
    <div class="card mb-3">
        <div class="card-body">
            <form action="" method="GET" class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="search">{{ __('Search') }}</label>
                        <input type="text" name="search" id="search" class="form-control" value="{{ $search }}" placeholder="{{ __('Phone, Name, Email...') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="status">{{ __('Status') }}</label>
                        <select name="status" id="status" class="form-control">
                            <option value="">{{ __('All Status') }}</option>
                            <option value="active" {{ $status == 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                            <option value="blocked" {{ $status == 'blocked' ? 'selected' : '' }}>{{ __('Blocked') }}</option>
                            <option value="suspended" {{ $status == 'suspended' ? 'selected' : '' }}>{{ __('Suspended') }}</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">{{ __('Search') }}</button>
                    <a href="{{ route('membership.customers.index') }}" class="btn btn-secondary">{{ __('Reset') }}</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card mb-5">
        <div class="card-header-tab card-header">
            <div class="card-header-title font-size-lg text-capitalize font-weight-normal">
                <h4 class="section_title">{{ __('Loyalty Customers') }}</h4>
            </div>
            <div class="btn-actions-pane-right actions-icon-btn">
                <a href="{{ route('membership.customers.export', request()->query()) }}" class="btn btn-success">
                    <i class="fa fa-download"></i> {{ __('Export CSV') }}
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table mb-5">
                    <thead>
                        <tr>
                            <th>{{ __('SN') }}</th>
                            <th>{{ __('Phone') }}</th>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Email') }}</th>
                            <th>{{ __('Total Points') }}</th>
                            <th>{{ __('Lifetime Points') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Joined') }}</th>
                            <th>{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($customers as $index => $customer)
                            <tr>
                                <td>{{ $customers->firstItem() + $index }}</td>
                                <td>{{ $customer->phone }}</td>
                                <td>{{ $customer->name ?? 'N/A' }}</td>
                                <td>{{ $customer->email ?? 'N/A' }}</td>
                                <td><strong>{{ number_format($customer->total_points) }}</strong></td>
                                <td>{{ number_format($customer->lifetime_points) }}</td>
                                <td>
                                    @if ($customer->status == 'active')
                                        <span class="badge bg-success">{{ __('Active') }}</span>
                                    @elseif ($customer->status == 'blocked')
                                        <span class="badge bg-danger">{{ __('Blocked') }}</span>
                                    @else
                                        <span class="badge bg-warning">{{ __('Suspended') }}</span>
                                    @endif
                                </td>
                                <td>{{ $customer->joined_at?->format('Y-m-d') ?? $customer->created_at->format('Y-m-d') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-primary dropdown-toggle"
                                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            {{ __('Action') }}
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="{{ route('membership.customers.show', $customer) }}">
                                                {{ __('View Details') }}
                                            </a>
                                            @if ($customer->status == 'active')
                                                <form action="{{ route('membership.customers.block', $customer) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item text-danger">{{ __('Block') }}</button>
                                                </form>
                                                <form action="{{ route('membership.customers.suspend', $customer) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item text-warning">{{ __('Suspend') }}</button>
                                                </form>
                                            @elseif ($customer->status == 'blocked')
                                                <form action="{{ route('membership.customers.unblock', $customer) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item text-success">{{ __('Unblock') }}</button>
                                                </form>
                                            @else
                                                <form action="{{ route('membership.customers.resume', $customer) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item text-success">{{ __('Resume') }}</button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">{{ __('No customers found') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="float-right">
                {{ $customers->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
@endsection
