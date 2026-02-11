@if (checkAdminHasPermission('purchase.view') ||
        checkAdminHasPermission('purchase.create') ||
        checkAdminHasPermission('purchase.return.view') ||
        checkAdminHasPermission('purchase.return.type.view'))


    <li class="menu-item {{ Route::is('admin.purchase.*') ? 'active open' : '' }}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class='menu-icon tf-icons bx bx-cart-add'></i>
            <div class="text-truncate" data-i18n="{{ __('Purchases') }}">{{ __('Purchases') }}</div>
        </a>
        <ul class="menu-sub">
            @adminCan('purchase.create')
                <li class="menu-item {{ Route::is('admin.purchase.create') ? 'active' : '' }}">
                    <a href="{{ route('admin.purchase.create') }}" class="menu-link">
                        <div class="text-truncate" data-i18n="{{ __('Add Purchase') }}">{{ __('Add Purchase') }}</div>
                    </a>
                </li>
            @endadminCan
            @adminCan('purchase.view')
                <li
                    class="menu-item {{ isRoute(['admin.purchase.index', 'admin.purchase.invoice', 'admin.purchase.edit', 'admin.purchase.return'], 'active') }}">
                    <a href="{{ route('admin.purchase.index') }}" class="menu-link">
                        <div class="text-truncate" data-i18n="{{ __('Purchase List') }}">{{ __('Purchase List') }}
                        </div>
                    </a>
                </li>
            @endadminCan
            @adminCan('purchase.return.view')
                <li class="menu-item {{ Route::is('admin.purchase.return.index') ? 'active' : '' }}">
                    <a href="{{ route('admin.purchase.return.index') }}" class="menu-link">
                        <div class="text-truncate" data-i18n="{{ __('Purchases Return List') }}">
                            {{ __('Purchases Return List') }}</div>
                    </a>
                </li>
            @endadminCan
            @adminCan('purchase.return.type.view')
                <li class="menu-item {{ Route::is('admin.purchase.return.type.list') ? 'active' : '' }}">
                    <a href="{{ route('admin.purchase.return.type.list') }}" class="menu-link">
                        <div class="text-truncate" data-i18n="{{ __('Purchases Return Type') }}">
                            {{ __('Purchases Return Type') }}</div>
                    </a>
                </li>
            @endadminCan
        </ul>
    </li>
@endif
