@php
    $routeList = [
        'admin.report.details-sale',
        'admin.report.profit-loss',
        'admin.report.menu-item-sales',
        'admin.report.order-type',
        'admin.report.customers',
        'admin.report.waiter-performance',
        'admin.report.table-performance',
        'admin.report.expense',
        'admin.report.purchase',
        'admin.report.supplier',
        'admin.report.supplier-payment',
        'admin.report.salary',
        'admin.report.low-stock-alert',
    ];
@endphp
@adminCan('report.view')
    <li class="menu-item  {{ isRoute($routeList, 'active open') }}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class='menu-icon tf-icons bx bx-bar-chart-alt-2'></i>
            <div class="text-truncate" data-i18n="{{ __('Reports') }}">{{ __('Reports') }}</div>
        </a>

        <ul class="menu-sub">
            {{-- Sales & Revenue --}}
            <li class="{{ Route::is('admin.report.details-sale') ? 'active' : '' }} menu-item">
                <a href="{{ route('admin.report.details-sale') }}" class="menu-link">
                    {{ __('Detail Sales Report') }}
                </a>
            </li>
            <li class="{{ Route::is('admin.report.profit-loss') ? 'active' : '' }} menu-item">
                <a href="{{ route('admin.report.profit-loss') }}" class="menu-link">
                    {{ __('Profit/Loss Report') }}
                </a>
            </li>
            <li class="{{ Route::is('admin.report.menu-item-sales') ? 'active' : '' }} menu-item">
                <a href="{{ route('admin.report.menu-item-sales') }}" class="menu-link">
                    {{ __('Menu Item Sales') }}
                </a>
            </li>
            <li class="{{ Route::is('admin.report.order-type') ? 'active' : '' }} menu-item">
                <a href="{{ route('admin.report.order-type') }}" class="menu-link">
                    {{ __('Order Type Report') }}
                </a>
            </li>
            <li class="{{ Route::is('admin.report.customers') ? 'active' : '' }} menu-item">
                <a href="{{ route('admin.report.customers') }}" class="menu-link">
                    {{ __('Customers Report') }}
                </a>
            </li>

            {{-- Operations --}}
            <li class="{{ Route::is('admin.report.waiter-performance') ? 'active' : '' }} menu-item">
                <a href="{{ route('admin.report.waiter-performance') }}" class="menu-link">
                    {{ __('Waiter Performance') }}
                </a>
            </li>
            <li class="{{ Route::is('admin.report.table-performance') ? 'active' : '' }} menu-item">
                <a href="{{ route('admin.report.table-performance') }}" class="menu-link">
                    {{ __('Table Performance') }}
                </a>
            </li>

            {{-- Financial --}}
            <li class="{{ Route::is('admin.report.expense') ? 'active' : '' }} menu-item">
                <a href="{{ route('admin.report.expense') }}" class="menu-link">
                    {{ __('Expense Report') }}
                </a>
            </li>
            <li class="{{ Route::is('admin.report.purchase') ? 'active' : '' }} menu-item">
                <a href="{{ route('admin.report.purchase') }}" class="menu-link">
                    {{ __('Purchases Report') }}
                </a>
            </li>
            <li class="{{ Route::is('admin.report.supplier') ? 'active' : '' }} menu-item">
                <a href="{{ route('admin.report.supplier') }}" class="menu-link">
                    {{ __('Suppliers Report') }}
                </a>
            </li>
            <li class="{{ Route::is('admin.report.supplier-payment') ? 'active' : '' }} menu-item">
                <a href="{{ route('admin.report.supplier-payment') }}" class="menu-link">
                    {{ __('Suppliers Payment') }}
                </a>
            </li>
            <li class="{{ Route::is('admin.report.salary') ? 'active' : '' }} menu-item">
                <a href="{{ route('admin.report.salary') }}" class="menu-link">
                    {{ __('Salary Report') }}
                </a>
            </li>

            {{-- Inventory --}}
            <li class="{{ Route::is('admin.report.low-stock-alert') ? 'active' : '' }} menu-item">
                <a href="{{ route('admin.report.low-stock-alert') }}" class="menu-link">
                    {{ __('Low Stock Alert') }}
                </a>
            </li>
        </ul>
    </li>
@endadminCan
