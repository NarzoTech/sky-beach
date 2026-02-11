@extends('admin.layouts.master')
@section('title', __('Profit/Loss Report'))

@push('css')
    <style>
        .table {
            margin-bottom: 0 !important;
        }

        .card-header {
            margin-bottom: 0 !important;
        }
    </style>
@endpush

@section('content')
    <div class="card">
        <div class="card-body pb-0">
            <form class="search_form" action="" method="GET">
                <div class="row">
                    <div class="col-lg-8 col-md-6">
                        <div class="form-group">
                            <div class="input-group input-daterange" id="bs-datepicker-daterange">
                                <input type="text" id="dateRangePicker" placeholder="From Date"
                                    class="form-control datepicker" name="from_date"
                                    value="{{ request()->get('from_date') }}" autocomplete="off">
                                <span class="input-group-text">to</span>
                                <input type="text" placeholder="To Date" class="form-control datepicker" name="to_date"
                                    value="{{ request()->get('to_date') }}" autocomplete="off">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="form-group">
                            <button type="button" class="btn bg-danger form-reset">{{ __('Reset') }}</button>
                            <button type="submit" class="btn btn-primary">{{ __('Search') }}</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="section_title mb-0">{{ __('Profit/Loss Report') }}</h4>
            <div class="btn-actions-pane-right actions-icon-btn">
                <span class="badge bg-label-info me-2">{{ $data['fromDate'] }} - {{ $data['toDate'] }}</span>
                <button type="button" class="btn btn-success export"><i class="fa fa-file-excel"></i>
                    {{ __('Excel') }}</button>
                <button type="button" class="btn btn-danger export-pdf"><i class="fa fa-file-pdf"></i>
                    {{ __('PDF') }}</button>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- Income Section -->
                <div class="col-md-6">
                    <div class="card border shadow-none mb-4">
                        <div class="card-header bg-success">
                            <h5 class="mb-0 text-white"><i class="fas fa-arrow-up me-2"></i>{{ __('Income') }}</h5>
                        </div>
                        <div class="card-body p-0">
                            <table class="table mb-0">
                                <tbody>
                                    <tr>
                                        <td>{{ __('Total Sales') }} <small class="text-muted">({{ __('incl. Tax') }})</small></td>
                                        <td class="text-end">{{ currency($data['totalSales']) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-danger">{{ __('Less: Tax Collected') }} <small class="text-muted">({{ __('Govt. Liability') }})</small></td>
                                        <td class="text-end text-danger">- {{ currency($data['totalTax']) }}</td>
                                    </tr>
                                    <tr class="table-light">
                                        <td><strong>{{ __('Net Sales') }}</strong> <small class="text-muted">({{ __('excl. Tax') }})</small></td>
                                        <td class="text-end"><strong>{{ currency($data['netSales']) }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('Purchase Returns') }} <small
                                                class="text-muted">({{ __('Refund from Supplier') }})</small></td>
                                        <td class="text-end">{{ currency($data['purchaseReturns']) }}</td>
                                    </tr>
                                    <tr class="table-success">
                                        <td><strong>{{ __('Total Income') }}</strong></td>
                                        <td class="text-end"><strong
                                                class="text-black">{{ currency($data['totalIncome']) }}</strong></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Expense Section -->
                <div class="col-md-6">
                    <div class="card border shadow-none mb-4 h-100">
                        <div class="card-header bg-danger">
                            <h5 class="mb-0 text-white"><i class="fas fa-arrow-down me-2"></i>{{ __('Expenses') }}</h5>
                        </div>
                        <div class="card-body p-0">
                            <table class="table mb-0">
                                <tbody>
                                    <tr>
                                        <td>{{ __('Cost of Goods Sold') }} <small class="text-muted">({{ __('COGS') }})</small></td>
                                        <td class="text-end">{{ currency($data['cogs']) }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('Operating Expenses') }}</td>
                                        <td class="text-end">{{ currency($data['expenses']) }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('Employee Salaries') }}</td>
                                        <td class="text-end">{{ currency($data['salaries']) }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('Wastage & Loss') }} <small class="text-muted">({{ __('Spoilage, Damage, Theft') }})</small></td>
                                        <td class="text-end">{{ currency($data['wastageCost'] ?? 0) }}</td>
                                    </tr>
                                    <tr class="table-secondary">
                                        <td><strong>{{ __('Gross Profit') }}</strong> <small class="text-muted">({{ __('Net Sales - COGS') }})</small></td>
                                        <td class="text-end"><strong>{{ currency($data['grossProfit']) }}</strong></td>
                                    </tr>
                                    <tr class="table-danger">
                                        <td><strong>{{ __('Total Expenses') }}</strong></td>
                                        <td class="text-end"><strong
                                                class="text-black">{{ currency($data['totalExpenses']) }}</strong></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profit/Loss Summary -->
            <div class="row">
                <div class="col-12">
                    <div class="card border {{ $data['profitLoss'] >= 0 ? 'border-success' : 'border-danger' }}">
                        <div class="card-body text-center py-4">
                            <h4 class="mb-3">{{ __('Net Profit / Loss') }}</h4>
                            <h1 class="{{ $data['profitLoss'] >= 0 ? 'text-success' : 'text-danger' }}">
                                @if ($data['profitLoss'] >= 0)
                                    <i class="fas fa-arrow-up me-2"></i>
                                @else
                                    <i class="fas fa-arrow-down me-2"></i>
                                @endif
                                {{ currency(abs($data['profitLoss'])) }}
                            </h1>
                            <p class="mb-0 {{ $data['profitLoss'] >= 0 ? 'text-success' : 'text-danger' }}">
                                @if ($data['profitLoss'] >= 0)
                                    <span class="badge bg-success fs-6">{{ __('Profit') }}</span>
                                @else
                                    <span class="badge bg-danger fs-6">{{ __('Loss') }}</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Summary Table -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('Description') }}</th>
                                    <th class="text-end">{{ __('Amount') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ __('Total Sales') }} <small class="text-muted">({{ __('incl. Tax') }})</small></td>
                                    <td class="text-end">{{ currency($data['totalSales']) }}</td>
                                </tr>
                                <tr>
                                    <td class="text-danger">{{ __('Less: Tax Collected') }}</td>
                                    <td class="text-end text-danger">- {{ currency($data['totalTax']) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>{{ __('Net Sales') }}</strong> <small class="text-muted">({{ __('excl. Tax') }})</small></td>
                                    <td class="text-end"><strong>{{ currency($data['netSales']) }}</strong></td>
                                </tr>
                                <tr>
                                    <td>{{ __('Purchase Returns') }}</td>
                                    <td class="text-end">{{ currency($data['purchaseReturns']) }}</td>
                                </tr>
                                <tr class="table-info">
                                    <td><strong>{{ __('Total Income') }}</strong></td>
                                    <td class="text-end"><strong>{{ currency($data['totalIncome']) }}</strong></td>
                                </tr>
                                <tr>
                                    <td>{{ __('Cost of Goods Sold (COGS)') }}</td>
                                    <td class="text-end">{{ currency($data['cogs']) }}</td>
                                </tr>
                                <tr class="table-secondary">
                                    <td><strong>{{ __('Gross Profit') }}</strong></td>
                                    <td class="text-end"><strong>{{ currency($data['grossProfit']) }}</strong></td>
                                </tr>
                                <tr>
                                    <td>{{ __('Operating Expenses') }}</td>
                                    <td class="text-end">{{ currency($data['expenses']) }}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('Employee Salaries') }}</td>
                                    <td class="text-end">{{ currency($data['salaries']) }}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('Wastage & Loss') }}</td>
                                    <td class="text-end">{{ currency($data['wastageCost'] ?? 0) }}</td>
                                </tr>
                                <tr class="table-warning">
                                    <td><strong>{{ __('Total Expenses') }}</strong></td>
                                    <td class="text-end"><strong>{{ currency($data['totalExpenses']) }}</strong></td>
                                </tr>
                                <tr class="{{ $data['profitLoss'] >= 0 ? 'table-success' : 'table-danger' }}">
                                    <td><strong>{{ __('Net Profit / Loss') }}</strong></td>
                                    <td class="text-end">
                                        <strong>{{ currency($data['profitLoss']) }}</strong>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Profit Margins -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card border">
                        <div class="card-body text-center">
                            <h6>{{ __('Gross Profit Margin') }}</h6>
                            <h3 class="{{ ($data['grossProfitMargin'] ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ number_format($data['grossProfitMargin'] ?? 0, 1) }}%
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border">
                        <div class="card-body text-center">
                            <h6>{{ __('Net Profit Margin') }}</h6>
                            <h3 class="{{ ($data['netProfitMargin'] ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ number_format($data['netProfitMargin'] ?? 0, 1) }}%
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
