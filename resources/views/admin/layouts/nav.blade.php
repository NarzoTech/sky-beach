<nav class="layout-navbar container-fluid navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
    id="layout-navbar">
    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-4 me-xl-0">
        <a class="nav_toggler_btn nav-link px-0 me-xl-6" href="javascript:void(0)">
            <i class="bx bx-menu bx-md"></i>
        </a>
    </div>
    <div class="navbar-nav-right d-flex flex-wrap align-items-center" id="navbar-collapse">
        <!-- Search -->
        <div class="navbar-nav align-items-center">
            <div class="nav-item d-flex align-items-center navbar_search position-relative">
                <i class="bx bx-search bx-md"></i>
                <input type="text" class="form-control border-0 shadow-none ps-1 ps-sm-2" placeholder="Search..."
                    aria-label="Search..." id="search_menu">

                <div id="admin_menu_list" class="d-flex flex-column position-absolute d-none rounded-2">
                    @foreach (routeList() as $route_item)
                        {{-- @if (checkAdminHasPermission($route_item?->permission) || empty($route_item?->permission)) --}}
                        <a class="border-bottom {{ isRoute('admin.' . $route_item?->route, 'active') }}"
                            href="{{ route('admin.' . $route_item?->route, $route_item?->param ?? []) }}{{ $route_item?->fragment ?? '' }}">{{ $route_item?->name }}</a>
                        {{-- @endif --}}
                    @endforeach
                </div>
            </div>
        </div>
        <!-- /Search -->


        <ul class="navbar-nav flex-wrap flex-row align-items-center ms-auto">

            @if(!$header_admin->hasRole('Waiter'))
            <!-- Website Link -->
            <li class="nav-item">
                <a href="{{ url('/') }}" target="_blank" class="nav-link nav-link-lg">
                    <i class="bx bx-globe"></i> {{ __('Website') }}
                </a>
            </li>

            <!-- Calculator Button -->
            <li class="nav-item me-2">
                <button type="button" class="btn btn-sm d-flex align-items-center" style="height: 32px; border-radius: 4px; background: #47c363; color: #fff; border-color: #47c363;" data-bs-toggle="modal"
                    data-bs-target="#calculatorModal">
                    <i class="bx bx-calculator me-1"></i> <span class="d-none d-md-inline">{{ __('Calculator') }}</span>
                </button>
            </li>

            @endif

            @adminCan('report.view')
                <li class="nav-item">
                    <a href="{{ route('admin.report.details-sale') }}" class="nav-link nav-link-lg">
                        <i class='bx bx-dollar-circle'></i> {{ __('Sale Report') }}</i>
                    </a>
                </li>
            @endadminCan

            @adminCan('stock.view')
                <li class="nav-item">
                    <a href="{{ route('admin.stock.index') }}" class="nav-link nav-link-lg">
                        <i class='bx bx-package'></i> {{ __('Stock') }}</i>
                    </a>
                </li>
            @endadminCan

            @adminCan('dts.view')
                <li class="nav-item">
                    <a href="{{ route('admin.report.dts') }}" class="nav-link nav-link-lg">
                        <i class='bx bx-line-chart'></i> {{ __('Today\'s Summary') }}</i>
                    </a>
                </li>
            @endadminCan
            @adminCan('pos.view')
                <li class="nav-item">
                    <a href="{{ route('admin.pos') }}" class="nav-link nav-link-lg">
                        <i class='bx bx-basket'></i> {{ __('POS') }}</i>
                    </a>
                </li>
            @endadminCan
            @if($header_admin->hasRole('Waiter'))
                <li class="nav-item me-2" style="display: block !important;">
                    <a href="{{ route('admin.waiter.select-table') }}" class="btn btn-primary btn-sm align-items-center" style="height: 32px; border-radius: 4px; white-space: nowrap; display: inline-flex !important;">
                        <i class='bx bx-plus me-1'></i> {{ __('New Order') }}
                    </a>
                </li>
            @endif
            <!-- User -->
            <li class="navbar-dropdown dropdown-user dropdown ms-3">
                <a class="nav-link dropdown-toggle hide-arrow p-0" href="javascript:void(0);" data-bs-toggle="dropdown">
                    <div class="avatar avatar-online">
                        <img src="{{ asset($header_admin->image_url) }}" alt class="w-px-40 h-auto rounded-circle">
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="#">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar avatar-online">
                                        <img src="{{ asset($header_admin->image_url) }}" alt
                                            class="w-px-40 h-auto rounded-circle">
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0">{{ $header_admin->name }}</h6>
                                    <small class="text-muted">{{ $header_admin->getRoleNames()->first() }}</small>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li>
                        <div class="dropdown-divider my-1"></div>
                    </li>
                    @adminCan(['admin.profile.view', 'admin.profile.edit'])
                        <li>
                            <a class="dropdown-item" href="{{ route('admin.edit-profile') }}">
                                <i class="bx bx-user bx-md me-3"></i><span>My Profile</span>
                            </a>
                        </li>
                        <li>
                            <div class="dropdown-divider my-1"></div>
                        </li>
                    @endadminCan
                    {{-- <li>
                        <a class="dropdown-item" href="#">
                            <i class="bx bx-cog bx-md me-3"></i><span>Settings</span>
                        </a>
                    </li> --}}

                    <li>
                        <a class="dropdown-item" href="javascript:void(0);"
                            onclick="event.preventDefault();
                                document.getElementById('admin-logout-form').submit();">
                            <i class="bx bx-power-off bx-md me-3"></i><span>Log Out</span>
                        </a>
                    </li>
                </ul>
            </li>
            <!--/ User -->
        </ul>
    </div>
</nav>
