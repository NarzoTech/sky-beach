@php
    $routeList = [
        'admin.report.barcode-wise-product',
        'admin.report.barcode-sale',
        'admin.report.categories',
        'admin.report.customers',
        'admin.report.receivable',
        'admin.report.details-sale',
        'admin.report.due-date-sale',
        'admin.report.expense',
        'admin.report.master-sale',
        'admin.report.monthly-sale',
        'admin.report.profit-loss',
        'admin.report.product-sale-report',
        'admin.report.received-report',
        'admin.report.purchase',
        'admin.report.supplier',
        'admin.report.supplier-payment',
        'admin.report.salary',
    ];
@endphp
@adminCan('report.view')
    <li class="menu-item  {{ isRoute($routeList, 'active open') }}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class='menu-icon tf-icons bx bx-store'></i>
            <div class="text-truncate" data-i18n="{{ __('Reports') }}">{{ __('Reports') }}</div>
        </a>

        <ul class="menu-sub">

            <li class="{{ Route::is('admin.report.barcode-wise-product') ? 'active' : '' }} menu-item">
                <a href="{{ route('admin.report.barcode-wise-product') }}" class="menu-link">
                    {{ __('Barcode Wise Product Report') }}
                </a>
            </li>
            <li class="{{ Route::is('admin.report.barcode-sale') ? 'active' : '' }} menu-item">
                <a href="{{ route('admin.report.barcode-sale') }}" class="menu-link">
                    {{ __('Barcode Wise Sale Report') }}
                </a>
            </li>
            <li class="{{ Route::is('admin.report.categories') ? 'active' : '' }} menu-item">
                <a href="{{ route('admin.report.categories') }}" class="menu-link">
                    {{ __('Categories Report') }}
                </a>
            </li>
            <li class="{{ Route::is('admin.report.customers') ? 'active' : '' }} menu-item">
                <a href="{{ route('admin.report.customers') }}" class="menu-link">
                    {{ __('Customers Report') }}
                </a>
            </li>
            <li class="{{ Route::is('admin.report.receivable') ? 'active' : '' }} menu-item">
                <a href="{{ route('admin.report.receivable') }}" class="menu-link">
                    {{ __('Due Report') }}
                </a>
            </li>
            <li class="{{ Route::is('admin.report.details-sale') ? 'active' : '' }} menu-item">
                <a href="{{ route('admin.report.details-sale') }}" class="menu-link">
                    {{ __('Detail Sales Report') }}
                </a>
            </li>
            <li class="{{ Route::is('admin.report.due-date-sale') ? 'active' : '' }} menu-item">
                <a href="{{ route('admin.report.due-date-sale') }}" class="menu-link">
                    {{ __('Due Date Sales Report') }}
                </a>
            </li>
            {{-- <li class="{{ Route::is('admin.report.dts') ? 'active' : '' }} menu-item">
            <a href="{{ route('admin.report.dts') }}" class="menu-link">
                {{ __('Daily Sales Report') }}
            </a>
        </li> --}}
            <li class="{{ Route::is('admin.report.expense') ? 'active' : '' }} menu-item">
                <a href="{{ route('admin.report.expense') }}" class="menu-link">
                    {{ __('Expense Report') }}
                </a>
            </li>
            {{-- <li class="{{ Route::is('admin.report.master-sale') ? 'active' : '' }} menu-item">
            <a href="{{ route('admin.report.master-sale') }}" class="menu-link">
                {{ __('Master Sales Report') }}
            </a>
        </li> --}}
            {{-- <li class="{{ Route::is('admin.report.monthly-sale') ? 'active' : '' }} menu-item">
            <a href="{{ route('admin.report.monthly-sale') }}" class="menu-link">
                {{ __('Monthly Sales Report') }}
            </a>
        </li> --}}
            <li class="{{ Route::is('admin.report.profit-loss') ? 'active' : '' }} menu-item">
                <a href="{{ route('admin.report.profit-loss') }}" class="menu-link">
                    {{ __('Profit/Loss Report') }}
                </a>
            </li>
            {{-- <li class="{{ Route::is('admin.report.product-sale-report') ? 'active' : '' }} menu-item">
            <a href="{{ route('admin.report.product-sale-report') }}" class="menu-link">
                {{ __('Products Sales Report') }}
            </a>
        </li> --}}
            <li class="{{ Route::is('admin.report.received-report') ? 'active' : '' }} menu-item">
                <a href="{{ route('admin.report.received-report') }}" class="menu-link">
                    {{ __('Payment Received Report') }}
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
            {{-- <li class="{{ Route::is('admin.report.dts') ? 'active' : '' }}  menu-item">
            <a href="{{ route('admin.report.dts') }}" class="menu-link">
                {{ __('Stock Report') }}
            </a>
        </li> --}}
            {{-- <li class="{{ Route::is('admin.report.dts') ? 'active' : '' }}  menu-item">
            <a href="{{ route('admin.report.dts') }}" class="menu-link">
                {{ __('Low Stock Product Report') }}
            </a>
        </li> --}}
            {{-- <li class="{{ Route::is('admin.report.dts') ? 'active' : '' }}  menu-item">
            <a href="{{ route('admin.report.dts') }}" class="menu-link">
                {{ __('Summary') }}
            </a>
        </li> --}}
            {{-- <li class="{{ Route::is('admin.report.dts') ? 'active' : '' }}  menu-item">
            <a href="{{ route('admin.report.dts') }}" class="menu-link">
                {{ __('Yearly Sales Report') }}
            </a>
        </li> --}}
        </ul>
    </li>
@endadminCan
