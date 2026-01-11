@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Transaction Details') }}</title>
@endsection
@section('content')
    <div class="card mb-5">
        <div class="card-header-tab card-header">
            <div class="card-header-title font-size-lg text-capitalize font-weight-normal">
                <h4 class="section_title">{{ __('Transaction Details') }}</h4>
            </div>
            <div class="btn-actions-pane-right actions-icon-btn">
                <a href="{{ route('membership.transactions.index') }}" class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i> {{ __('Back') }}
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th>{{ __('Transaction ID') }}</th>
                            <td>{{ $transaction->id }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Date') }}</th>
                            <td>{{ $transaction->created_at->format('Y-m-d H:i:s') }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Customer') }}</th>
                            <td>
                                <a href="{{ route('membership.customers.show', $transaction->loyalty_customer_id) }}">
                                    {{ $transaction->customer->phone ?? 'N/A' }} - {{ $transaction->customer->name ?? 'N/A' }}
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <th>{{ __('Transaction Type') }}</th>
                            <td>
                                @if ($transaction->transaction_type == 'earn')
                                    <span class="badge bg-success">{{ __('Earned') }}</span>
                                @elseif ($transaction->transaction_type == 'redeem')
                                    <span class="badge bg-danger">{{ __('Redeemed') }}</span>
                                @elseif ($transaction->transaction_type == 'adjustment')
                                    <span class="badge bg-info">{{ __('Adjusted') }}</span>
                                @else
                                    <span class="badge bg-secondary">{{ $transaction->transaction_type }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>{{ __('Points Amount') }}</th>
                            <td>
                                @if ($transaction->points_amount >= 0)
                                    <span class="text-success fw-bold">+{{ number_format($transaction->points_amount) }}</span>
                                @else
                                    <span class="text-danger fw-bold">{{ number_format($transaction->points_amount) }}</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th>{{ __('Balance Before') }}</th>
                            <td>{{ number_format($transaction->points_balance_before) }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Balance After') }}</th>
                            <td>{{ number_format($transaction->points_balance_after) }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Source Type') }}</th>
                            <td>{{ ucfirst(str_replace('_', ' ', $transaction->source_type ?? 'N/A')) }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Source ID') }}</th>
                            <td>{{ $transaction->source_id ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Warehouse') }}</th>
                            <td>{{ $transaction->warehouse->name ?? 'N/A' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            @if ($transaction->transaction_type == 'redeem')
                <div class="row mt-4">
                    <div class="col-md-6">
                        <h5>{{ __('Redemption Details') }}</h5>
                        <table class="table table-bordered">
                            <tr>
                                <th>{{ __('Redemption Method') }}</th>
                                <td>{{ ucfirst(str_replace('_', ' ', $transaction->redemption_method ?? 'N/A')) }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('Redemption Value') }}</th>
                                <td>{{ $transaction->redemption_value ?? 'N/A' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            @endif

            <div class="row mt-4">
                <div class="col-md-12">
                    <h5>{{ __('Additional Information') }}</h5>
                    <table class="table table-bordered">
                        <tr>
                            <th>{{ __('Description') }}</th>
                            <td>{{ $transaction->description ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Created By') }}</th>
                            <td>{{ $transaction->createdBy->name ?? 'System' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
