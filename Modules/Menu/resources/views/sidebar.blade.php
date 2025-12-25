{{-- Menu Management Sidebar --}}
@if (checkAdminHasPermission('menu.category.view') ||
        checkAdminHasPermission('menu.item.view') ||
        checkAdminHasPermission('menu.addon.view') ||
        checkAdminHasPermission('menu.combo.view') ||
        checkAdminHasPermission('menu.branch.pricing') ||
        checkAdminHasPermission('menu.branch.availability'))
    <li
        class="menu-item {{ Route::is('admin.menu-category*') || Route::is('admin.menu-item*') || Route::is('admin.menu-addon*') || Route::is('admin.combo*') || Route::is('admin.branch-menu*') ? 'active open' : '' }}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class='menu-icon tf-icons bx bx-food-menu'></i>
            <div class="text-truncate" data-i18n="{{ __('Menu Management') }}">{{ __('Menu Management') }}</div>
        </a>

        <ul class="menu-sub">
            @adminCan('menu.category.view')
                <li class="menu-item {{ Route::is('admin.menu-category*') ? 'active' : '' }}">
                    <a class="menu-link" href="{{ route('admin.menu-category.index') }}">
                        {{ __('Menu Categories') }}
                    </a>
                </li>
            @endadminCan

            @adminCan('menu.item.view')
                <li class="menu-item {{ Route::is('admin.menu-item*') ? 'active' : '' }}">
                    <a class="menu-link" href="{{ route('admin.menu-item.index') }}">
                        {{ __('Menu Items') }}
                    </a>
                </li>
            @endadminCan

            @adminCan('menu.addon.view')
                <li class="menu-item {{ Route::is('admin.menu-addon*') ? 'active' : '' }}">
                    <a class="menu-link" href="{{ route('admin.menu-addon.index') }}">
                        {{ __('Menu Add-ons') }}
                    </a>
                </li>
            @endadminCan

            @adminCan('menu.combo.view')
                <li class="menu-item {{ Route::is('admin.combo*') ? 'active' : '' }}">
                    <a class="menu-link" href="{{ route('admin.combo.index') }}">
                        {{ __('Combo Deals') }}
                    </a>
                </li>
            @endadminCan

            @adminCan('menu.branch.pricing')
                <li class="menu-item {{ Route::is('admin.branch-menu.pricing*') ? 'active' : '' }}">
                    <a class="menu-link" href="{{ route('admin.branch-menu.pricing') }}">
                        {{ __('Branch Pricing') }}
                    </a>
                </li>
            @endadminCan

            @adminCan('menu.branch.availability')
                <li class="menu-item {{ Route::is('admin.branch-menu.availability*') ? 'active' : '' }}">
                    <a class="menu-link" href="{{ route('admin.branch-menu.availability') }}">
                        {{ __('Branch Availability') }}
                    </a>
                </li>
            @endadminCan
        </ul>
    </li>
@endif
