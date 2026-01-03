@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Dashboard') }}</title>
@endsection

@push('css')
    <style>
        .dashboard-card {
            border: none;
            border-radius: 15px;
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }

        .card-primary {
            background: linear-gradient(135deg, #696cff 0%, #5a5edd 100%);
        }

        .card-success {
            background: linear-gradient(135deg, #71dd37 0%, #5fc52e 100%);
        }

        .card-danger {
            background: linear-gradient(135deg, #ff3e1d 0%, #e63617 100%);
        }

        .card-info {
            background: linear-gradient(135deg, #03c3ec 0%, #02a8cc 100%);
        }

        .card-secondary {
            background: linear-gradient(135deg, #8592a3 0%, #6e7a8a 100%);
        }

        .stat-card .card-body {
            padding: 1.5rem;
        }

        .stat-card .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
        }

        .stat-card .stat-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 0;
        }

        .stat-card .stat-label {
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.85);
            margin-bottom: 0.25rem;
        }

        .chart-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .chart-card .card-header {
            background: transparent;
            border-bottom: 1px solid #eee;
            padding: 1.25rem 1.5rem;
        }

        .chart-card .card-title {
            font-weight: 600;
            color: #333;
            margin: 0;
        }


        .table-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .table-card .nav-pills .nav-link {
            border-radius: 10px;
            padding: 0.5rem 1rem;
            font-weight: 500;
        }

        .table-card .nav-pills .nav-link.active {
            background: linear-gradient(135deg, #696cff 0%, #5a5edd 100%);
        }

        .table-scroll {
            max-height: 350px;
            overflow-y: auto;
        }

        .table-card .table th {
            border-top: none;
            font-weight: 600;
            color: #555;
            background: #f8f9fa;
        }

        .badge-gradient {
            background: linear-gradient(135deg, #696cff 0%, #5a5edd 100%);
            color: #fff;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 500;
        }

        .quick-stats {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .quick-stat-item {
            background: #f8f9fa;
            padding: 1rem 1.5rem;
            border-radius: 10px;
            flex: 1;
            min-width: 150px;
        }

        .percentage-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .percentage-badge.positive {
            background: rgba(67, 233, 123, 0.2);
            color: #28a745;
        }

        .percentage-badge.negative {
            background: rgba(255, 99, 132, 0.2);
            color: #dc3545;
        }

        .section-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .section-title i {
            color: #696cff;
        }

        /* Monthly Stats Cards Redesign */
        .monthly-stat-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .monthly-stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }

        .monthly-stat-header {
            padding: 1rem 1.5rem;
            color: #fff;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .monthly-stat-header.bg-sales {
            background: linear-gradient(135deg, #71dd37 0%, #5fc52e 100%);
        }

        .monthly-stat-header.bg-purchase {
            background: linear-gradient(135deg, #03c3ec 0%, #02a8cc 100%);
        }

        .monthly-stat-header.bg-expense {
            background: linear-gradient(135deg, #ff3e1d 0%, #e63617 100%);
        }

        .monthly-stat-header i {
            font-size: 1.5rem;
        }

        .monthly-stat-body {
            padding: 1.5rem;
        }

        .monthly-stat-value {
            font-size: 1.75rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .monthly-stat-comparison {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .comparison-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .comparison-badge.up {
            background: rgba(113, 221, 55, 0.15);
            color: #71dd37;
        }

        .comparison-badge.down {
            background: rgba(255, 62, 29, 0.15);
            color: #ff3e1d;
        }

        .monthly-stat-footer {
            background: #f8f9fa;
            padding: 0.75rem 1.5rem;
            font-size: 0.85rem;
            color: #666;
            border-top: 1px solid #eee;
        }
    </style>
@endpush

@section('content')
    <section class="section">
        <div class="section-body">
            <!-- Welcome Header -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div>
                            <h4 class="mb-1">{{ __('Welcome Back') }}! {{ auth()->guard('admin')->user()->name }}</h4>
                            <p class="text-muted mb-0">{{ __("Here's what's happening with your business today.") }}</p>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-primary fs-6 px-3 py-2">
                                <i class="bx bx-calendar me-1"></i> {{ formatDate(now(), 'l, d M Y') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Stats Row -->
            <div class="row g-4 mb-4">
                <div class="col-xl-3 col-lg-6 col-md-6">
                    <div class="card dashboard-card stat-card card-success">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="stat-label">{{ __('Today Sales') }}</p>
                                    <h3 class="stat-value">{{ currency($data['todaySales']) }}</h3>
                                </div>
                                <div class="stat-icon">
                                    <i class='bx bx-cart'></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-6 col-md-6">
                    <div class="card dashboard-card stat-card card-info">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="stat-label">{{ __('Today Purchase') }}</p>
                                    <h3 class="stat-value">{{ currency($data['todayPurchase']) }}</h3>
                                </div>
                                <div class="stat-icon">
                                    <i class='bx bx-package'></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-6 col-md-6">
                    <div class="card dashboard-card stat-card card-danger">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="stat-label">{{ __('Today Expense') }}</p>
                                    <h3 class="stat-value">{{ currency($data['todayExpense']) }}</h3>
                                </div>
                                <div class="stat-icon">
                                    <i class='bx bx-money'></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-6 col-md-6">
                    <div class="card dashboard-card stat-card card-primary">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="stat-label">{{ __('Total Ingredients') }}</p>
                                    <h3 class="stat-value">{{ number_format($data['totalIngredients']) }}</h3>
                                </div>
                                <div class="stat-icon">
                                    <i class='bx bx-box'></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Due Stats Row -->
            <div class="row g-4 mb-4">
                <div class="col-xl-3 col-lg-6 col-md-6">
                    <div class="card dashboard-card stat-card card-danger">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="stat-label">{{ __('Customer Due') }}</p>
                                    <h3 class="stat-value">{{ currency($data['customerDues']) }}</h3>
                                </div>
                                <div class="stat-icon">
                                    <i class='bx bx-user-plus'></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-6 col-md-6">
                    <div class="card dashboard-card stat-card card-secondary">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="stat-label">{{ __('Supplier Due') }}</p>
                                    <h3 class="stat-value">{{ currency($data['total_supplier_due']) }}</h3>
                                </div>
                                <div class="stat-icon">
                                    <i class='bx bx-user-minus'></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-6 col-md-6">
                    <div class="card dashboard-card stat-card card-success">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="stat-label">{{ __('Total Sales') }}</p>
                                    <h3 class="stat-value">{{ currency($data['totalSales']) }}</h3>
                                </div>
                                <div class="stat-icon">
                                    <i class='bx bx-trending-up'></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-6 col-md-6">
                    <div class="card dashboard-card stat-card card-info">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="stat-label">{{ __('Total Purchase') }}</p>
                                    <h3 class="stat-value">{{ currency($data['totalPurchases']) }}</h3>
                                </div>
                                <div class="stat-icon">
                                    <i class='bx bx-store'></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Monthly Stats with Percentage -->
            <div class="row g-4 mb-4 mt-5">
                <div class="col-xl-4 col-lg-6">
                    <div class="card monthly-stat-card h-100">
                        <div class="monthly-stat-header bg-sales">
                            <i class="bx bx-cart"></i>
                            <span class="fw-semibold">{{ __('Monthly Sales') }}</span>
                        </div>
                        <div class="monthly-stat-body">
                            <div class="monthly-stat-value">{{ currency($chart['currentSales']) }}</div>
                            <div class="monthly-stat-comparison">
                                <span class="comparison-badge {{ $chart['salePercentage'] >= 0 ? 'up' : 'down' }}">
                                    <i
                                        class="bx {{ $chart['salePercentage'] >= 0 ? 'bx-trending-up' : 'bx-trending-down' }}"></i>
                                    {{ abs($chart['salePercentage']) }}%
                                </span>
                                <span class="text-muted">{{ __('vs last month') }}</span>
                            </div>
                        </div>
                        <div class="monthly-stat-footer">
                            <i class="bx bx-calendar me-1"></i> {{ formatDate(now(), 'F Y') }}
                        </div>
                    </div>
                </div>

                <div class="col-xl-4 col-lg-6">
                    <div class="card monthly-stat-card h-100">
                        <div class="monthly-stat-header bg-purchase">
                            <i class="bx bx-package"></i>
                            <span class="fw-semibold">{{ __('Monthly Purchase') }}</span>
                        </div>
                        <div class="monthly-stat-body">
                            <div class="monthly-stat-value">{{ currency($chart['currentPurchases']) }}</div>
                            <div class="monthly-stat-comparison">
                                <span class="comparison-badge {{ $chart['purchasePercentage'] <= 0 ? 'up' : 'down' }}">
                                    <i
                                        class="bx {{ $chart['purchasePercentage'] >= 0 ? 'bx-trending-up' : 'bx-trending-down' }}"></i>
                                    {{ abs($chart['purchasePercentage']) }}%
                                </span>
                                <span class="text-muted">{{ __('vs last month') }}</span>
                            </div>
                        </div>
                        <div class="monthly-stat-footer">
                            <i class="bx bx-calendar me-1"></i> {{ formatDate(now(), 'F Y') }}
                        </div>
                    </div>
                </div>

                <div class="col-xl-4 col-lg-6">
                    <div class="card monthly-stat-card h-100">
                        <div class="monthly-stat-header bg-expense">
                            <i class="bx bx-money"></i>
                            <span class="fw-semibold">{{ __('Monthly Expense') }}</span>
                        </div>
                        <div class="monthly-stat-body">
                            <div class="monthly-stat-value">{{ currency($chart['currentMonthExpense']) }}</div>
                            <div class="monthly-stat-comparison">
                                <span class="comparison-badge {{ $chart['expensePercentage'] <= 0 ? 'up' : 'down' }}">
                                    <i
                                        class="bx {{ $chart['expensePercentage'] >= 0 ? 'bx-trending-up' : 'bx-trending-down' }}"></i>
                                    {{ abs($chart['expensePercentage']) }}%
                                </span>
                                <span class="text-muted">{{ __('vs last month') }}</span>
                            </div>
                        </div>
                        <div class="monthly-stat-footer">
                            <i class="bx bx-calendar me-1"></i> {{ formatDate(now(), 'F Y') }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="row g-4 mb-4 mt-5">
                <!-- Income vs Expense Pie Chart -->
                <div class="col-xl-4 col-lg-6">
                    <div class="card chart-card h-100">
                        <div class="card-header">
                            <h5 class="card-title">
                                <i class="bx bx-pie-chart-alt-2 me-2 text-primary"></i>
                                {{ __('Income vs Expense') }}
                            </h5>
                            <small class="text-muted">{{ formatDate(now(), 'F Y') }}</small>
                        </div>
                        <div class="card-body">
                            <div id="incomeExpenseChart"></div>
                        </div>
                    </div>
                </div>

                <!-- Expense by Type Pie Chart -->
                <div class="col-xl-4 col-lg-6">
                    <div class="card chart-card h-100">
                        <div class="card-header">
                            <h5 class="card-title">
                                <i class="bx bx-doughnut-chart me-2 text-danger"></i>
                                {{ __('Expense Breakdown') }}
                            </h5>
                            <small class="text-muted">{{ formatDate(now(), 'F Y') }}</small>
                        </div>
                        <div class="card-body">
                            <div id="expenseByTypeChart"></div>
                        </div>
                    </div>
                </div>

                <!-- Weekly Sales Chart -->
                <div class="col-xl-4 col-lg-12">
                    <div class="card chart-card h-100">
                        <div class="card-header">
                            <h5 class="card-title">
                                <i class="bx bx-bar-chart-alt-2 me-2 text-success"></i>
                                {{ __('Weekly Sales') }}
                            </h5>
                            <small class="text-muted">{{ __('Last 7 days') }}</small>
                        </div>
                        <div class="card-body">
                            <div id="weeklySalesChart"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Current Month Sales Area Chart -->
            <div class="row g-4 mb-4 mt-5">
                <div class="col-12">
                    <div class="card chart-card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title">
                                    <i class="bx bx-line-chart me-2 text-info"></i>
                                    {{ __('Daily Sales Trend') }}
                                </h5>
                                <small class="text-muted">{{ formatDate(now(), 'F Y') }}</small>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="salesChart"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Year Wise Sales & Purchase Chart -->
            <div class="row g-4 mb-4 mt-5">
                <div class="col-12">
                    <div class="card chart-card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title">
                                    <i class="bx bx-bar-chart me-2 text-warning"></i>
                                    {{ __('Year Wise Sales & Purchase') }}
                                </h5>
                                <small class="text-muted">{{ formatDate(now(), 'Y') }}</small>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="profitChart"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tables Section -->
            <div class="row g-4 mt-5">
                <div class="col-12">
                    <div class="card table-card">
                        <div class="card-header nav-align-top">
                            <ul class="nav nav-pills flex-wrap row-gap-2 me-auto" role="tablist">
                                <li class="nav-item">
                                    <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab"
                                        data-bs-target="#navs-pills-browser" aria-controls="navs-pills-browser"
                                        aria-selected="true">
                                        <i class="bx bx-error-circle me-1"></i>
                                        {{ __('Low Stock') }}
                                        @if (count($data['low_stock_ingredients']) > 0)
                                            <span
                                                class="badge bg-danger ms-1">{{ count($data['low_stock_ingredients']) }}</span>
                                        @endif
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                                        data-bs-target="#navs-pills-os" aria-controls="navs-pills-os"
                                        aria-selected="false">
                                        <i class="bx bx-user me-1"></i>
                                        {{ __('Customer Due') }}
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                                        data-bs-target="#navs-pills-country" aria-controls="navs-pills-country"
                                        aria-selected="false">
                                        <i class="bx bx-store me-1"></i>
                                        {{ __('Supplier Due') }}
                                    </button>
                                </li>
                            </ul>
                        </div>
                        <div class="tab-content pt-0 pb-4">
                            <div class="tab-pane fade show active" id="navs-pills-browser" role="tabpanel">
                                <div class="table-responsive text-start text-nowrap table-scroll">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>{{ __('No') }}</th>
                                                <th>{{ __('Product') }}</th>
                                                <th>{{ __('Stock Alert') }}</th>
                                                <th>{{ __('Current Stock') }}</th>
                                                <th>{{ __('Status') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($data['low_stock_ingredients'] as $index => $product)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>
                                                        <strong>{{ $product->name }}</strong>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-warning">{{ $product->stock_alert }}</span>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-danger">{{ $product->stock }}</span>
                                                    </td>
                                                    <td>
                                                        @if ($product->stock <= 0)
                                                            <span class="badge bg-danger">{{ __('Out of Stock') }}</span>
                                                        @else
                                                            <span class="badge bg-warning">{{ __('Low Stock') }}</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center py-4">
                                                        <i class="bx bx-check-circle text-success fs-1"></i>
                                                        <p class="mb-0 mt-2">{{ __('All ingredients are well stocked!') }}
                                                        </p>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="navs-pills-os" role="tabpanel">
                                <div class="table-responsive text-start text-nowrap table-scroll">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>{{ __('No') }}</th>
                                                <th class="w-50">{{ __('Customer Name') }}</th>
                                                <th>{{ __('Total Sales') }}</th>
                                                <th>{{ __('Total Due') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($data['customers'] as $customer)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>
                                                        <strong>{{ $customer->name }}</strong>
                                                    </td>
                                                    <td>{{ currency($customer->sales->sum('grand_total')) }}</td>
                                                    <td>
                                                        <span class="text-danger fw-bold">
                                                            {{ currency($customer->total_due - $customer->total_sale_return_due - $customer->advances()) }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center py-4">
                                                        <i class="bx bx-check-circle text-success fs-1"></i>
                                                        <p class="mb-0 mt-2">{{ __('No customer dues!') }}</p>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="navs-pills-country" role="tabpanel">
                                <div class="table-responsive text-start text-nowrap table-scroll">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>{{ __('No') }}</th>
                                                <th class="w-50">{{ __('Supplier Name') }}</th>
                                                <th>{{ __('Total Purchase') }}</th>
                                                <th>{{ __('Total Due') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($data['suppliers'] as $supplier)
                                                @php
                                                    $totalReturn = $supplier->purchaseReturn->sum('return_amount');
                                                @endphp
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>
                                                        <strong>{{ $supplier->name }}</strong>
                                                    </td>
                                                    <td>{{ currency($supplier->purchases->sum('total_amount')) }}</td>
                                                    <td>
                                                        <span class="text-danger fw-bold">
                                                            {{ currency($supplier->total_due - $totalReturn) }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center py-4">
                                                        <i class="bx bx-check-circle text-success fs-1"></i>
                                                        <p class="mb-0 mt-2">{{ __('No supplier dues!') }}</p>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection

@push('js')
    <script>
        // Income vs Expense Pie Chart
        const incomeExpenseData = @json($chart['incomeVsExpense']);
        var incomeExpenseOptions = {
            series: incomeExpenseData.map(item => item.value),
            chart: {
                type: 'donut',
                height: 300
            },
            labels: incomeExpenseData.map(item => item.name),
            colors: ['#28a745', '#dc3545'],
            legend: {
                position: 'bottom'
            },
            plotOptions: {
                pie: {
                    donut: {
                        size: '65%'
                    }
                }
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return "{{ currency_icon() }} " + val.toLocaleString();
                    }
                }
            },
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        height: 250
                    }
                }
            }]
        };
        var incomeExpenseChart = new ApexCharts(document.querySelector("#incomeExpenseChart"), incomeExpenseOptions);
        incomeExpenseChart.render();

        // Expense by Type Pie Chart
        const expenseByTypeData = @json($chart['expenseByType']);
        var expenseTypeOptions = {
            series: expenseByTypeData.map(item => item.value),
            chart: {
                type: 'pie',
                height: 300
            },
            labels: expenseByTypeData.map(item => item.name),
            colors: ['#696cff', '#ff3e1d', '#03c3ec', '#71dd37', '#8592a3'],
            legend: {
                position: 'bottom'
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return "{{ currency_icon() }} " + val.toLocaleString();
                    }
                }
            },
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        height: 250
                    }
                }
            }],
            noData: {
                text: '{{ __('No expense data available') }}'
            }
        };
        var expenseTypeChart = new ApexCharts(document.querySelector("#expenseByTypeChart"), expenseTypeOptions);
        expenseTypeChart.render();

        // Weekly Sales Bar Chart
        const weeklySalesLabels = @json($chart['weeklySalesLabels']);
        const weeklySalesData = @json($chart['weeklySalesData']);
        var weeklySalesOptions = {
            series: [{
                name: '{{ __('Sales') }}',
                data: weeklySalesData
            }],
            chart: {
                type: 'bar',
                height: 300,
                toolbar: {
                    show: false
                }
            },
            plotOptions: {
                bar: {
                    borderRadius: 8,
                    columnWidth: '60%',
                    distributed: true
                }
            },
            colors: ['#696cff', '#71dd37', '#03c3ec', '#ff3e1d', '#8592a3', '#696cff', '#71dd37'],
            dataLabels: {
                enabled: false
            },
            legend: {
                show: false
            },
            xaxis: {
                categories: weeklySalesLabels
            },
            yaxis: {
                labels: {
                    formatter: function(val) {
                        return "{{ currency_icon() }} " + val.toLocaleString();
                    }
                }
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return "{{ currency_icon() }} " + val.toLocaleString();
                    }
                }
            }
        };
        var weeklySalesChart = new ApexCharts(document.querySelector("#weeklySalesChart"), weeklySalesOptions);
        weeklySalesChart.render();

        // Current Month Sales Area Chart
        let currentMonthData = @json($chart['currentMonthSaleData']);
        const currentMonthKeys = Object.keys(currentMonthData).map(key => new Date(key).toISOString());
        currentMonthData = Object.values(currentMonthData).map(value => parseInt(value, 10)).filter(value => !isNaN(
            value));

        var salesOptions = {
            series: [{
                name: '{{ __('Sales') }}',
                data: [...currentMonthData]
            }],
            chart: {
                height: 350,
                type: 'area',
                toolbar: {
                    show: true,
                    tools: {
                        download: true,
                        selection: false,
                        zoom: false,
                        zoomin: false,
                        zoomout: false,
                        pan: false,
                        reset: false
                    }
                }
            },
            colors: ['#696cff'],
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.7,
                    opacityTo: 0.2,
                    stops: [0, 100]
                }
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                width: 3
            },
            xaxis: {
                type: 'datetime',
                categories: [...currentMonthKeys],
            },
            tooltip: {
                x: {
                    format: 'dd MMM'
                },
                y: {
                    formatter: function(val) {
                        return "{{ currency_icon() }} " + val.toLocaleString();
                    }
                }
            },
        };
        var salesChart = new ApexCharts(document.querySelector("#salesChart"), salesOptions);
        salesChart.render();

        // Year Wise Sales & Purchase Bar Chart
        const purchase = @json($purchaseData);
        const purchaseVal = Object.values(purchase).map(value => parseInt(value, 10)).filter(value => !isNaN(value));

        const sales = @json($saleData);
        const salesVal = Object.values(sales).map(value => parseInt(value, 10)).filter(value => !isNaN(value));

        var chartOptions = {
            series: [{
                name: '{{ __('Purchase') }}',
                data: purchaseVal
            }, {
                name: '{{ __('Sales') }}',
                data: salesVal
            }],
            chart: {
                type: 'bar',
                height: 400,
                toolbar: {
                    show: true,
                    tools: {
                        download: true,
                        selection: false,
                        zoom: false,
                        zoomin: false,
                        zoomout: false,
                        pan: false,
                        reset: false
                    }
                }
            },
            colors: ['#03c3ec', '#71dd37'],
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '55%',
                    borderRadius: 8,
                    borderRadiusApplication: 'end'
                },
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
            },
            xaxis: {
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            },
            yaxis: {
                labels: {
                    formatter: function(val) {
                        return "{{ currency_icon() }} " + val.toLocaleString();
                    }
                }
            },
            fill: {
                opacity: 1
            },
            legend: {
                position: 'top'
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return "{{ currency_icon() }} " + val.toLocaleString();
                    }
                }
            }
        };

        var profitChart = new ApexCharts(document.querySelector("#profitChart"), chartOptions);
        profitChart.render();
    </script>
@endpush
