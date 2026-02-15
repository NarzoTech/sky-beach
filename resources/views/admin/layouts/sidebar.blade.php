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

        @if(auth('admin')->user()->hasRole('Waiter'))
        <li class="menu-item {{ Route::is('admin.waiter.dashboard') ? 'active' : '' }}">
            <a href="{{ route('admin.waiter.dashboard') }}" class="menu-link">
                <i class='menu-icon tf-icons bx bx-home-smile'></i>
                <div class="text-truncate" data-i18n="Basic">{{ __('Waiter Dashboard') }}</div>
            </a>
        </li>
        @else
        <li class="menu-item {{ Route::is('admin.dashboard') ? 'active' : '' }}">
            <a href="{{ route('admin.dashboard') }}" class="menu-link">
                <i class='menu-icon tf-icons bx bx-home-smile'></i>
                <div class="text-truncate" data-i18n="Basic">{{ __('Dashboard') }}</div>
            </a>
        </li>
        @endif

        @if (checkAdminHasPermission('pos.view') || checkAdminHasPermission('sales.view') || checkAdminHasPermission('menu.item.view') || checkAdminHasPermission('customer.view') || checkAdminHasPermission('membership.view') || checkAdminHasPermission('quotation.view'))
        <li class="menu-header small text-uppercase"><span class="menu-header-text">{{ __('Core Operations') }}</span></li>
        @if (Module::isEnabled('POS'))
            @include('pos::sidebar')
        @endif

        @if (Module::isEnabled('Sales'))
            @include('sales::sidebar')
        @endif

        @if (Module::isEnabled('Menu'))
            @include('menu::sidebar')
        @endif

        @if (Module::isEnabled('Customer'))
            @include('customer::sidebar')
        @endif

        @if (Module::isEnabled('Membership'))
            @include('membership::sidebar')
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
        @endif

        @if (checkAdminHasPermission('ingredient.view') || checkAdminHasPermission('stock.view') || checkAdminHasPermission('purchase.view') || checkAdminHasPermission('supplier.view'))
        <li class="menu-header small text-uppercase"><span class="menu-header-text">{{ __('Inventory & Supply') }}</span></li>
        @if (Module::isEnabled('Ingredient'))
            @include('ingredient::sidebar')
        @endif

        @adminCan('stock.view')
            <li class="menu-item {{ Route::is('admin.stock.index') ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class='menu-icon tf-icons bx bx-package'></i>
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

        @if (Module::isEnabled('Purchase'))
            @include('purchase::sidebar')
        @endif

        @if (Module::isEnabled('Supplier'))
            @include('supplier::sidebar')
        @endif
        @endif

        @if (checkAdminHasPermission('cash.flow.view') || checkAdminHasPermission('account.view') || checkAdminHasPermission('expense.view') || checkAdminHasPermission('asset.view'))
        <li class="menu-header small text-uppercase"><span class="menu-header-text">{{ __('Finance') }}</span></li>
        @if (Module::isEnabled('Accounts'))
            @include('accounts::sidebar')
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
        @endif

        @if (checkAdminHasPermission('report.view'))
        <li class="menu-header small text-uppercase"><span class="menu-header-text">{{ __('Reports & Analytics') }}</span></li>
        @if (Module::isEnabled('Report'))
            @include('report::sidebar')
        @endif
        @endif

        @if (checkAdminHasPermission('employee.view') || checkAdminHasPermission('attendance.view'))
        <li class="menu-header small text-uppercase"><span class="menu-header-text">{{ __('HR & Staff') }}</span></li>
        @if (Module::isEnabled('Employee'))
            @include('employee::sidebar')
        @endif

        @if (Module::isEnabled('Attendance'))
            @include('attendance::sidebar')
        @endif
        @endif

        @if (checkAdminHasPermission('service.view'))
        <li class="menu-header small text-uppercase"><span class="menu-header-text">{{ __('Services') }}</span></li>
        @if (Module::isEnabled('Service'))
            @include('service::sidebar')
        @endif
        @endif

        @if (checkAdminHasPermission('setting.view') || checkAdminHasPermission('admin.view') || checkAdminHasPermission('role.view') || checkAdminHasPermission('cms.settings.view') || checkAdminHasPermission('restaurant.blog.view'))
        <li class="menu-header small text-uppercase"><span class="menu-header-text">{{ __('Administration') }}</span></li>
        @if(!auth('admin')->user()->hasRole('Waiter'))
        @if (Module::isEnabled('CMS') || Module::isEnabled('Website'))
            <li class="menu-item {{ request()->is('admin/cms/*') || request()->is('admin/restaurant/*') ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class='menu-icon tf-icons bx bx-globe'></i>
                    <div class="text-truncate" data-i18n="{{ __('Website') }}">{{ __('Website') }}</div>
                </a>
                <ul class="menu-sub">
                    @if (Module::isEnabled('CMS'))
                        {{-- Page Sections --}}
                        @adminCan('cms.settings.view')
                        <li class="menu-header small text-uppercase"><span class="menu-header-text">{{ __('Pages') }}</span></li>
                        <li class="menu-item {{ request()->is('admin/cms/sections/homepage*') || (request()->is('admin/cms/sections/*/edit*') && request()->get('page') == 'home') ? 'active' : '' }}">
                            <a class="menu-link" href="{{ route('admin.cms.sections.homepage') }}">
                                <i class='bx bx-home-alt me-2'></i>{{ __('Homepage') }}
                            </a>
                        </li>
                        <li class="menu-item {{ request()->is('admin/cms/sections/about*') || (request()->is('admin/cms/sections/*/edit*') && request()->get('page') == 'about') ? 'active' : '' }}">
                            <a class="menu-link" href="{{ route('admin.cms.sections.about') }}">
                                <i class='bx bx-info-circle me-2'></i>{{ __('About Page') }}
                            </a>
                        </li>
                        <li class="menu-item {{ request()->is('admin/cms/sections/contact*') || (request()->is('admin/cms/sections/*/edit*') && request()->get('page') == 'contact') ? 'active' : '' }}">
                            <a class="menu-link" href="{{ route('admin.cms.sections.contact') }}">
                                <i class='bx bx-envelope me-2'></i>{{ __('Contact Page') }}
                            </a>
                        </li>
                        <li class="menu-item {{ request()->is('admin/cms/sections/menu') || (request()->is('admin/cms/sections/*/edit*') && request()->get('page') == 'menu') ? 'active' : '' }}">
                            <a class="menu-link" href="{{ route('admin.cms.sections.menu') }}">
                                <i class='bx bx-food-menu me-2'></i>{{ __('Menu Page') }}
                            </a>
                        </li>
                        <li class="menu-item {{ request()->is('admin/cms/sections/menu-detail*') || (request()->is('admin/cms/sections/*/edit*') && request()->get('page') == 'menu_detail') ? 'active' : '' }}">
                            <a class="menu-link" href="{{ route('admin.cms.sections.menu-detail') }}">
                                <i class='bx bx-detail me-2'></i>{{ __('Menu Detail Page') }}
                            </a>
                        </li>
                        <li class="menu-item {{ request()->is('admin/cms/sections/reservation*') || (request()->is('admin/cms/sections/*/edit*') && request()->get('page') == 'reservation') ? 'active' : '' }}">
                            <a class="menu-link" href="{{ route('admin.cms.sections.reservation') }}">
                                <i class='bx bx-calendar-check me-2'></i>{{ __('Reservation Page') }}
                            </a>
                        </li>
                        <li class="menu-item {{ request()->is('admin/cms/sections/service*') || (request()->is('admin/cms/sections/*/edit*') && request()->get('page') == 'service') ? 'active' : '' }}">
                            <a class="menu-link" href="{{ route('admin.cms.sections.service') }}">
                                <i class='bx bx-wrench me-2'></i>{{ __('Service Page') }}
                            </a>
                        </li>
                        @endadminCan

                        {{-- Data Management --}}
                        <li class="menu-header small text-uppercase"><span class="menu-header-text">{{ __('Data') }}</span></li>
                        @adminCan('cms.testimonials.view')
                        <li class="menu-item {{ request()->is('admin/cms/testimonials*') ? 'active' : '' }}">
                            <a class="menu-link" href="{{ route('admin.cms.testimonials.index') }}">
                                <i class='bx bx-message-square-dots me-2'></i>{{ __('Testimonials') }}
                            </a>
                        </li>
                        @endadminCan
                        @adminCan('cms.counters.view')
                        <li class="menu-item {{ request()->is('admin/cms/counters*') ? 'active' : '' }}">
                            <a class="menu-link" href="{{ route('admin.cms.counters.index') }}">
                                <i class='bx bx-bar-chart me-2'></i>{{ __('Counters') }}
                            </a>
                        </li>
                        @endadminCan
                        @adminCan('cms.gallery.view')
                        <li class="menu-item {{ request()->is('admin/cms/gallery*') ? 'active' : '' }}">
                            <a class="menu-link" href="{{ route('admin.cms.gallery.index') }}">
                                <i class='bx bx-images me-2'></i>{{ __('Gallery') }}
                            </a>
                        </li>
                        @endadminCan
                        @adminCan('cms.legal-pages.view')
                        <li class="menu-item {{ request()->is('admin/cms/legal-pages*') ? 'active' : '' }}">
                            <a class="menu-link" href="{{ route('admin.cms.legal-pages.index') }}">
                                <i class='bx bx-file me-2'></i>{{ __('Legal Pages') }}
                            </a>
                        </li>
                        @endadminCan
                    @endif

                    @if (Module::isEnabled('Website'))
                        {{-- Content --}}
                        <li class="menu-header small text-uppercase"><span class="menu-header-text">{{ __('Content') }}</span></li>
                        @adminCan('restaurant.blog.view')
                            <li class="menu-item {{ request()->is('admin/restaurant/blogs*') ? 'active' : '' }}">
                                <a class="menu-link" href="{{ route('admin.restaurant.blogs.index') }}">
                                    <i class='bx bx-news me-2'></i>{{ __('Blogs') }}
                                </a>
                            </li>
                        @endadminCan
                        @adminCan('restaurant.chef.view')
                            <li class="menu-item {{ request()->is('admin/restaurant/chefs*') ? 'active' : '' }}">
                                <a class="menu-link" href="{{ route('admin.restaurant.chefs.index') }}">
                                    <i class='bx bx-user-pin me-2'></i>{{ __('Chefs') }}
                                </a>
                            </li>
                        @endadminCan
                        @adminCan('restaurant.service.view')
                            <li class="menu-item {{ request()->is('admin/restaurant/website-services*') ? 'active' : '' }}">
                                <a class="menu-link" href="{{ route('admin.restaurant.website-services.index') }}">
                                    <i class='bx bx-server me-2'></i>{{ __('Services') }}
                                </a>
                            </li>
                        @endadminCan
                        @adminCan('restaurant.faq.view')
                            <li class="menu-item {{ request()->is('admin/restaurant/faqs*') ? 'active' : '' }}">
                                <a class="menu-link" href="{{ route('admin.restaurant.faqs.index') }}">
                                    <i class='bx bx-help-circle me-2'></i>{{ __('FAQs') }}
                                </a>
                            </li>
                        @endadminCan
                        @adminCan('restaurant.service.view')
                            <li class="menu-item {{ request()->is('admin/restaurant/service-faqs*') ? 'active' : '' }}">
                                <a class="menu-link" href="{{ route('admin.restaurant.service-faqs.index') }}">
                                    <i class='bx bx-question-mark me-2'></i>{{ __('Service FAQs') }}
                                </a>
                            </li>
                        @endadminCan

                        {{-- Messages & Orders --}}
                        <li class="menu-header small text-uppercase"><span class="menu-header-text">{{ __('Messages & Orders') }}</span></li>
                        @adminCan('restaurant.booking.view')
                            <li class="menu-item {{ request()->is('admin/restaurant/bookings*') ? 'active' : '' }}">
                                <a class="menu-link" href="{{ route('admin.restaurant.bookings.index') }}">
                                    <i class='bx bx-calendar-event me-2'></i>{{ __('Bookings') }}
                                </a>
                            </li>
                        @endadminCan
                        @adminCan('restaurant.service.view')
                            <li class="menu-item {{ request()->is('admin/restaurant/service-contacts*') ? 'active' : '' }}">
                                <a class="menu-link" href="{{ route('admin.restaurant.service-contacts.index') }}">
                                    <i class='bx bx-support me-2'></i>{{ __('Service Inquiries') }}
                                </a>
                            </li>
                        @endadminCan
                        @adminCan('restaurant.contact-message.view')
                        <li class="menu-item {{ request()->is('admin/restaurant/contact-messages*') ? 'active' : '' }}">
                            <a class="menu-link" href="{{ route('admin.restaurant.contact-messages.index') }}">
                                <i class='bx bx-message-dots me-2'></i>{{ __('Contact Messages') }}
                            </a>
                        </li>
                        @endadminCan
                        @adminCan('restaurant.website-order.view')
                        <li class="menu-item {{ request()->is('admin/restaurant/website-orders*') ? 'active' : '' }}">
                            <a class="menu-link" href="{{ route('admin.restaurant.website-orders.index') }}">
                                <i class='bx bx-cart me-2'></i>{{ __('Website Orders') }}
                            </a>
                        </li>
                        @endadminCan
                    @endif
                </ul>
            </li>
        @endif
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
                        <li class="{{ isRoute('admin.website-checkout-settings', 'active') }} menu-item ">
                            <a class="menu-link" href="{{ route('admin.website-checkout-settings') }}">
                                {{ __('Website Checkout') }}
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
                </ul>
            </li>

        @endif
        @endif
        <li class="mb-5"></li>
    </ul>
</aside>
