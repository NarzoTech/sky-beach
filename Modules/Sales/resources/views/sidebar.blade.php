@if (checkAdminHasPermission('pos.view') ||
        checkAdminHasPermission('sales.view') ||
        checkAdminHasPermission('sales.return.list'))

    <li class="menu-item {{ isRoute(['admin.sales.index', 'admin.pos', 'admin.sales.return.list'], 'active open') }}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class='menu-icon tf-icons bx bx-basket'></i>
            <div class="text-truncate" data-i18n="{{ __('Sales') }}">{{ __('Sales') }}</div>
        </a>
        <ul class="menu-sub">
            @adminCan('pos.view')
                <li class="menu-item {{ isRoute('admin.pos', 'active') }}">
                    <a href="{{ route('admin.pos') }}" class="menu-link">
                        <div class="text-truncate" data-i18n="{{ __('POS') }}">{{ __('POS') }}</div>
                    </a>
                </li>
            @endadminCan
            @adminCan('sales.view')
                <li class="menu-item {{ isRoute('admin.sales.index', 'active') }}">
                    <a href="{{ route('admin.sales.index') }}" class="menu-link">
                        <div class="text-truncate" data-i18n="{{ __('Manage Sales') }}">{{ __('Manage Sales') }}</div>
                    </a>
                </li>
            @endadminCan
            @adminCan('sales.return.list')
                <li class="menu-item {{ isRoute('admin.sales.return.list', 'active') }}">
                    <a href="{{ route('admin.sales.return.list') }}" class="menu-link">
                        <div class="text-truncate" data-i18n="{{ __('Sales Return List') }}">{{ __('Sales Return List') }}
                        </div>
                    </a>
                </li>
            @endadminCan
        </ul>
    </li>
@endif
