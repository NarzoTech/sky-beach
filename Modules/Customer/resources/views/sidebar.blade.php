@if (checkAdminHasPermission('customer.view') ||
        checkAdminHasPermission('customer.due.receive.list') ||
        checkAdminHasPermission('customer.group.view') ||
        checkAdminHasPermission('customer.area.view'))
    <li
        class="menu-item {{ isRoute(['admin.customers.*', 'admin.area.index', 'admin.customerGroup.index', 'admin.customers.due-receive.list'], 'active open') }}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class='menu-icon tf-icons bx bx-user'></i>
            <div class="text-truncate" data-i18n="{{ __('Manage Customer') }}">{{ __('Manage Customer') }}</div>
        </a>
        <ul class="menu-sub">
            @adminCan('customer.view')
                <li
                    class="menu-item {{ isRoute(['admin.customers.index', 'admin.customers.advance', 'admin.customers.import'], 'active') }}">
                    <a href="{{ route('admin.customers.index') }}" class="menu-link">
                        <div class="text-truncate" data-i18n="Landing">{{ __('Customers') }}</div>
                    </a>
                </li>
            @endadminCan
            @adminCan('customer.due.receive.list')
                <li class="menu-item {{ isRoute('admin.customers.due-receive.list', 'active') }}">
                    <a href="{{ route('admin.customers.due-receive.list') }}" class="menu-link">
                        <div class="text-truncate" data-i18n="{{ __('Due Receive List') }}">{{ __('Due Receive List') }}
                        </div>
                    </a>
                </li>
            @endadminCan
            @adminCan('customer.group.view')
                <li class="menu-item {{ isRoute('admin.customerGroup.index', 'active') }}">
                    <a href="{{ route('admin.customerGroup.index') }}" class="menu-link">
                        <div class="text-truncate" data-i18n="{{ __('Customer Group') }}">{{ __('Customer Group') }}</div>
                    </a>
                </li>
            @endadminCan
            @adminCan('customer.area.view')
                <li class="menu-item {{ isRoute('admin.area.index', 'active') }}">
                    <a href="{{ route('admin.area.index') }}" class="menu-link">
                        <div class="text-truncate" data-i18n="Help Center">{{ __('Area List') }}</div>
                    </a>
                </li>
            @endadminCan
        </ul>
    </li>
@endif
