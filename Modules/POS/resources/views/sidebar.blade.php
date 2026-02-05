@if (checkAdminHasPermission('pos.view') ||
        checkAdminHasPermission('pos.settings.view') ||
        checkAdminHasPermission('printer.view') ||
        checkAdminHasPermission('table.view') ||
        checkAdminHasPermission('reservation.view'))
    @php
        $posMenuActive = request()->is('admin/pos*') || request()->is('admin/tables*') || request()->is('admin/reservations*');
        // For non-waiter users, also activate on waiter pages
        if (!auth('admin')->user()->hasRole('Waiter')) {
            $posMenuActive = $posMenuActive || request()->is('admin/waiter*');
        }
    @endphp
    <li class="menu-item {{ $posMenuActive ? 'active open' : '' }}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class='menu-icon tf-icons bx bx-store'></i>
            <div class="text-truncate" data-i18n="{{ __('POS Management') }}">{{ __('POS Management') }}</div>
        </a>

        <ul class="menu-sub">
            {{-- POS --}}
            @adminCan('pos.view')
                <li class="menu-item {{ Route::is('admin.pos') ? 'active' : '' }}">
                    <a class="menu-link" href="{{ route('admin.pos') }}">
                        <div class="text-truncate">{{ __('Point of Sale') }}</div>
                    </a>
                </li>
            @endadminCan



            {{-- POS Settings --}}
            @adminCan('pos.settings.view')
                <li class="menu-item {{ Route::is('admin.pos.settings*') ? 'active' : '' }}">
                    <a class="menu-link" href="{{ route('admin.pos.settings') }}">
                        <div class="text-truncate">{{ __('POS Settings') }}</div>
                    </a>
                </li>
            @endadminCan


            {{-- Printer Management --}}
            @adminCan('printer.view')
                <li class="menu-item {{ Route::is('admin.pos.printers*') ? 'active' : '' }}">
                    <a class="menu-link" href="{{ route('admin.pos.printers.index') }}">
                        <div class="text-truncate">{{ __('Printers') }}</div>
                    </a>
                </li>
            @endadminCan

            {{-- Print Station --}}
            @adminCan('printer.view')
                <li class="menu-item {{ Route::is('admin.pos.print-station*') ? 'active' : '' }}">
                    <a class="menu-link" href="{{ route('admin.pos.print-station.index') }}" target="_blank">
                        <div class="text-truncate">{{ __('Print Station') }}</div>
                    </a>
                </li>
            @endadminCan

            @if (Module::isEnabled('TableManagement'))
                {{-- Table Management --}}
                @adminCan('table.view')
                    <li class="menu-item {{ Route::is('admin.tables*') ? 'active' : '' }}">
                        <a class="menu-link" href="{{ route('admin.tables.index') }}">
                            <div class="text-truncate">{{ __('Tables') }}</div>
                        </a>
                    </li>
                @endadminCan

                {{-- Reservations --}}
                @adminCan('reservation.view')
                    <li class="menu-item {{ Route::is('admin.reservations*') ? 'active' : '' }}">
                        <a class="menu-link" href="{{ route('admin.reservations.index') }}">
                            <div class="text-truncate">{{ __('Reservations') }}</div>
                        </a>
                    </li>
                @endadminCan
            @endif
        </ul>
    </li>
@endif
