@extends('admin.layouts.master')
@section('title', __('Customer Details') . ' - ' . $customer->phone)
@section('content')
    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5>{{ __('Customer Information') }}</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th>{{ __('Phone') }}</th>
                            <td>{{ $customer->phone }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Name') }}</th>
                            <td>{{ $customer->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Email') }}</th>
                            <td>{{ $customer->email ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Status') }}</th>
                            <td>
                                @if ($customer->status == 'active')
                                    <span class="badge bg-success">{{ __('Active') }}</span>
                                @elseif ($customer->status == 'blocked')
                                    <span class="badge bg-danger">{{ __('Blocked') }}</span>
                                @else
                                    <span class="badge bg-warning">{{ __('Suspended') }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>{{ __('Joined') }}</th>
                            <td>{{ $customer->joined_at?->format('Y-m-d H:i') ?? $customer->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5>{{ __('Points Summary') }}</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th>{{ __('Current Points') }}</th>
                            <td><strong class="text-primary">{{ number_format($customer->total_points) }}</strong></td>
                        </tr>
                        <tr>
                            <th>{{ __('Lifetime Points') }}</th>
                            <td>{{ number_format($customer->lifetime_points) }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Total Earned') }}</th>
                            <td class="text-success">{{ number_format($summary['lifetime_earned'] ?? 0) }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Total Redeemed') }}</th>
                            <td class="text-danger">{{ number_format($summary['total_redeemed'] ?? 0) }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5>{{ __('Adjust Points') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('membership.customers.adjustPoints', $customer) }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="adjustment_amount">{{ __('Amount') }} <span class="text-danger">*</span></label>
                            <input type="number" name="adjustment_amount" id="adjustment_amount" class="form-control" required>
                            <small class="text-muted">{{ __('Use negative value to deduct points') }}</small>
                        </div>
                        <div class="form-group">
                            <label for="reason">{{ __('Reason') }} <span class="text-danger">*</span></label>
                            <select name="reason" id="reason" class="form-control" required>
                                <option value="">{{ __('Select Reason') }}</option>
                                <option value="manual_adjustment">{{ __('Manual Adjustment') }}</option>
                                <option value="correction">{{ __('Correction') }}</option>
                                <option value="bonus">{{ __('Bonus') }}</option>
                                <option value="promotion">{{ __('Promotion') }}</option>
                                <option value="refund">{{ __('Refund') }}</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="notes">{{ __('Notes') }}</label>
                            <textarea name="notes" id="notes" class="form-control" rows="2"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">{{ __('Adjust Points') }}</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header-tab card-header">
                    <div class="card-header-title">
                        <h5>{{ __('Transaction History') }}</h5>
                    </div>
                    <div class="btn-actions-pane-right">
                        <a href="{{ route('membership.customers.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fa fa-arrow-left"></i> {{ __('Back') }}
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Type') }}</th>
                                    <th>{{ __('Points') }}</th>
                                    <th>{{ __('Balance') }}</th>
                                    <th>{{ __('Description') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($transactions as $transaction)
                                    <tr>
                                        <td>{{ $transaction->created_at->format('Y-m-d H:i') }}</td>
                                        <td>
                                            @if ($transaction->transaction_type == 'earn')
                                                <span class="badge bg-success">{{ __('Earned') }}</span>
                                            @elseif ($transaction->transaction_type == 'redeem')
                                                <span class="badge bg-danger">{{ __('Redeemed') }}</span>
                                            @elseif ($transaction->transaction_type == 'adjust')
                                                <span class="badge bg-info">{{ __('Adjusted') }}</span>
                                            @else
                                                <span class="badge bg-secondary">{{ $transaction->transaction_type }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($transaction->points_amount >= 0)
                                                <span class="text-success">+{{ number_format($transaction->points_amount) }}</span>
                                            @else
                                                <span class="text-danger">{{ number_format($transaction->points_amount) }}</span>
                                            @endif
                                        </td>
                                        <td>{{ number_format($transaction->points_balance_after) }}</td>
                                        <td>{{ $transaction->description ?? 'N/A' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">{{ __('No transactions found') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
