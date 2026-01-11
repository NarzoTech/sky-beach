@if (checkAdminHasPermission('pos.view'))
    <li class="menu-item {{ request()->is('admin/pos*') || request()->is('admin/tables*') || request()->is('admin/reservations*') ? 'active open' : '' }}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class='menu-icon tf-icons bx bx-store'></i>
            <div class="text-truncate" data-i18n="{{ __('POS Management') }}">{{ __('POS Management') }}</div>
        </a>

        <ul class="menu-sub">
            {{-- POS --}}
            <li class="menu-item {{ Route::is('admin.pos') ? 'active' : '' }}">
                <a class="menu-link" href="{{ route('admin.pos') }}">
                    <div class="text-truncate">{{ __('Point of Sale') }}</div>
                </a>
            </li>

            {{-- POS Settings --}}
            <li class="menu-item {{ Route::is('admin.pos.settings*') ? 'active' : '' }}">
                <a class="menu-link" href="{{ route('admin.pos.settings') }}">
                    <div class="text-truncate">{{ __('POS Settings') }}</div>
                </a>
            </li>

            {{-- Waiter Management --}}
            <li class="menu-item {{ Route::is('admin.pos.waiters*') ? 'active' : '' }}">
                <a class="menu-link" href="{{ route('admin.pos.waiters.index') }}">
                    <div class="text-truncate">{{ __('Waiters') }}</div>
                </a>
            </li>

            @if (Module::isEnabled('TableManagement'))
                {{-- Table Management --}}
                <li class="menu-item {{ Route::is('admin.tables*') ? 'active' : '' }}">
                    <a class="menu-link" href="{{ route('admin.tables.index') }}">
                        <div class="text-truncate">{{ __('Tables') }}</div>
                    </a>
                </li>

                {{-- Reservations --}}
                <li class="menu-item {{ Route::is('admin.reservations*') ? 'active' : '' }}">
                    <a class="menu-link" href="{{ route('admin.reservations.index') }}">
                        <div class="text-truncate">{{ __('Reservations') }}</div>
                    </a>
                </li>
            @endif
        </ul>
    </li>
@endif
