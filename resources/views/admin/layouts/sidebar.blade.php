<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo ">
        <a href="{{ route('admin.dashboard') }}" class="app-brand-link">
            <span class="app-brand-logo demo">
                <img src="{{ asset($setting->logo) }}" alt="Logo">
            </span>
            {{-- <span class="app-brand-text demo menu-text fw-bold ms-2">sneat</span> --}}
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block">
            <i class="bx bx-chevron-left bx-sm d-flex align-items-center justify-content-center"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">

        <li class="menu-item {{ Route::is('admin.dashboard') ? 'active' : '' }}">
            <a href="{{ route('admin.dashboard') }}" class="menu-link">
                <i class='menu-icon tf-icons bx bx-home-smile'></i>
                <div class="text-truncate" data-i18n="Basic">{{ __('Dashboard') }}</div>
            </a>
        </li>

        @if (Module::isEnabled('Supplier'))
            @include('supplier::sidebar')
        @endif


        @if (Module::isEnabled('Customer'))
            @include('customer::sidebar')
        @endif

        @if (Module::isEnabled('Membership'))
            @include('membership::sidebar')
        @endif

        @if (Module::isEnabled('Ingredient'))
            @include('ingredient::sidebar')
        @endif

        @if (Module::isEnabled('Menu'))
            @include('menu::sidebar')
        @endif

        @if (Module::isEnabled('Purchase'))
            @include('purchase::sidebar')
        @endif

        @adminCan('stock.view')
            <li class="menu-item {{ Route::is('admin.stock.index') ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class='menu-icon tf-icons bx bx-detail'></i>
                    <div class="text-truncate" data-i18n="{{ __('Inventory') }}">{{ __('Inventory') }}</div>
                </a>
                <ul class="menu-sub">

                    <li class="menu-item {{ Route::is('admin.stock.index') ? 'active' : '' }}">
                        <a href="{{ route('admin.stock.index') }}" class="menu-link">
                            <div class="text-truncate" data-i18n="{{ __('Stock') }}">{{ __('Stock') }}</div>
                        </a>
                    </li>

                </ul>
            </li>
        @endadminCan
        @if (Module::isEnabled('Service'))
            @include('service::sidebar')
        @endif
        @if (Module::isEnabled('Sales'))
            @include('sales::sidebar')
        @endif

        @if (Module::isEnabled('POS'))
            @include('pos::sidebar')
        @endif

        @if (Module::isEnabled('Accounts'))
            @include('accounts::sidebar')
        @endif

        @if (checkAdminHasPermission('quotation.view') || checkAdminHasPermission('quotation.create'))
            <li class="menu-item {{ Route::is('admin.quotation*') ? 'active open' : '' }}">

                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class='menu-icon tf-icons bx bx-list-ul'></i>
                    <div class="text-truncate" data-i18n="{{ __('Quotations') }}">{{ __('Quotations') }}</div>
                </a>

                <ul class="menu-sub">
                    @adminCan('quotation.create')
                        <li class="menu-item {{ Route::is('admin.quotation.create') ? 'active' : '' }}">
                            <a class="menu-link" href="{{ route('admin.quotation.create') }}">
                                {{ __('Add Quotation') }}
                            </a>
                        </li>
                    @endadminCan
                    @adminCan('quotation.view')
                        <li
                            class="menu-item {{ Route::is('admin.quotation*') && !Route::is('admin.quotation.create') ? 'active' : '' }}">
                            <a class="menu-link" href="{{ route('admin.quotation.index') }}">
                                {{ __('Quotation Manage') }}
                            </a>
                        </li>
                    @endadminCan
                </ul>
            </li>
        @endif

        @if (Module::isEnabled('Report'))
            @include('report::sidebar')
        @endif

        @if (Module::isEnabled('Expense'))
            @include('expense::sidebar')
        @endif
        @if (checkAdminHasPermission('asset.view') || checkAdminHasPermission('asset.type.view'))
            <li
                class="menu-item {{ Route::is('admin.asset-category*') || Route::is('admin.assets*') ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class='menu-icon tf-icons bx bx-dollar-circle'></i>
                    <div class="text-truncate" data-i18n="{{ __('Assets') }}">{{ __('Assets') }}</div>
                </a>

                <ul class="menu-sub">
                    @adminCan('asset.view')
                        <li class="{{ Route::is('admin.assets*') ? 'active' : '' }} menu-item ">
                            <a class="menu-link" href="{{ route('admin.assets.index') }}">
                                {{ __('Asset List') }}
                            </a>
                        </li>
                    @endadminCan
                    @adminCan('asset.type.view')
                        <li class="{{ Route::is('admin.asset-category*') ? 'active' : '' }} menu-item ">
                            <a class="menu-link" href="{{ route('admin.asset-category.index') }}">
                                {{ __('Asset Type') }}
                            </a>
                        </li>
                    @endadminCan
                </ul>
            </li>
        @endif
        @if (Module::isEnabled('Employee'))
            @include('employee::sidebar')
        @endif

        @if (Module::isEnabled('Attendance'))
            @include('attendance::sidebar')
        @endif

        {{-- Restaurant/Website Management Menu --}}
        @if (Module::isEnabled('Website'))
            <li class="menu-item {{ request()->is('admin/restaurant/*') ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class='menu-icon tf-icons bx bx-restaurant'></i>
                    <div class="text-truncate" data-i18n="{{ __('Restaurant') }}">{{ __('Restaurant') }}</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ request()->is('admin/restaurant/blogs*') ? 'active' : '' }}">
                        <a class="menu-link" href="{{ route('admin.restaurant.blogs.index') }}">
                            {{ __('Blogs') }}
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('admin/restaurant/chefs*') ? 'active' : '' }}">
                        <a class="menu-link" href="{{ route('admin.restaurant.chefs.index') }}">
                            {{ __('Chefs') }}
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('admin/restaurant/website-services*') ? 'active' : '' }}">
                        <a class="menu-link" href="{{ route('admin.restaurant.website-services.index') }}">
                            {{ __('Services') }}
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('admin/restaurant/bookings*') ? 'active' : '' }}">
                        <a class="menu-link" href="{{ route('admin.restaurant.bookings.index') }}">
                            {{ __('Bookings') }}
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('admin/restaurant/cms-pages*') ? 'active' : '' }}">
                        <a class="menu-link" href="{{ route('admin.restaurant.cms-pages.index') }}">
                            {{ __('CMS Pages') }}
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('admin/restaurant/faqs*') ? 'active' : '' }}">
                        <a class="menu-link" href="{{ route('admin.restaurant.faqs.index') }}">
                            {{ __('FAQs') }}
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('admin/restaurant/menu-items*') ? 'active' : '' }}">
                        <a class="menu-link" href="{{ route('admin.restaurant.menu-items.index') }}">
                            {{ __('Menu Items') }}
                        </a>
                    </li>
                </ul>
            </li>
        @endif

        @if (checkAdminHasPermission('setting.view') ||
                checkAdminHasPermission('admin.view') ||
                checkAdminHasPermission('role.view') ||
                checkAdminHasPermission('database.reset'))
            <li
                class="menu-item {{ isRoute(['admin.settings', 'admin.print.settings', 'admin.business*', 'admin.reset.database', 'admin.cache.clear', 'admin.admin*', 'admin.role*'], 'active open') }}">

                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class='menu-icon tf-icons bx bx-cog'></i>
                    <div class="text-truncate" data-i18n="{{ __('Settings') }}">{{ __('Settings') }}</div>
                </a>

                <ul class="menu-sub">
                    @adminCan('setting.view')
                        <li class="{{ isRoute('admin.settings', 'active') }} menu-item ">
                            <a class="menu-link" href="{{ route('admin.settings') }}">
                                {{ __('Business Settings') }}
                            </a>
                        </li>
                    @endadminCan
                    @adminCan('admin.view')
                        <li class="{{ isRoute('admin.admin*', 'active') }} menu-item ">
                            <a class="menu-link" href="{{ route('admin.admin.index') }}">
                                {{ __('Admins') }}
                            </a>
                        </li>
                    @endadminCan
                    @adminCan('role.view')
                        <li class="{{ isRoute('admin.role*', 'active') }} menu-item ">
                            <a class="menu-link" href="{{ route('admin.role.index') }}">
                                {{ __('Roles & Permissions') }}
                            </a>
                        </li>
                    @endadminCan
                    {{-- <li class="{{ isRoute('admin.print.settings', 'active') }} menu-item ">
                    <a class="menu-link" href="{{ route('admin.print.settings') }}">
                        {{ __('Print Settings') }}
                    </a>
                </li>
                <li class="{{ isRoute('admin.business*', 'active') }} menu-item ">
                    <a class="menu-link" href="{{ route('admin.business.index') }}">
                        {{ __('Business Branches') }}
                    </a>
                </li>
                <li class="{{ isRoute('admin.notice.create', 'active') }} menu-item ">
                    <a class="menu-link" href="{{ route('admin.notice.create') }}">
                        {{ __('Notice Send') }}
                    </a>
                </li>
                <li class="{{ isRoute('admin.courier.settings', 'active') }} menu-item ">
                    <a class="menu-link" href="{{ route('admin.courier.settings') }}">
                        {{ __('Courier Settings') }}
                    </a>
                </li> --}}
                    @adminCan('database.reset')
                        <li class="{{ isRoute('admin.reset.database', 'active') }} menu-item ">
                            <a class="menu-link" href="{{ route('admin.reset.database') }}">
                                {{ __('Reset Database') }}
                            </a>
                        </li>
                    @endadminCan
                    @adminCan('setting.view')
                        <li class="{{ isRoute('admin.cache.clear', 'active') }} menu-item ">
                            <a class="menu-link" href="{{ route('admin.cache.clear') }}">
                                {{ __('Clear Cache') }}
                            </a>
                        </li>
                    @endadminCan
                    {{-- @if (Module::isEnabled('Tax'))
                    @include('tax::sidebar')
                @endif --}}
                </ul>
            </li>

        @endif
        <li class="mb-5"></li>
    </ul>
</aside>
