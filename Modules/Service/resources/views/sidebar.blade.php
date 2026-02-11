@if (checkAdminHasPermission('service.view') || checkAdminHasPermission('service.category.view'))


    <li class="menu-item {{ isRoute(['admin.serviceCategory.*', 'admin.service.*'], 'active open') }}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class='menu-icon tf-icons bx bx-layer'></i>
            <div class="text-truncate" data-i18n="{{ __('Services') }}">{{ __('Services') }}</div>
        </a>
        <ul class="menu-sub">
            @adminCan('service.view')
                <li class="menu-item {{ isRoute('admin.service.index', 'active') }}">
                    <a href="{{ route('admin.service.index') }}" class="menu-link">
                        <div class="text-truncate" data-i18n="{{ __('Service List') }}">{{ __('Service List') }}</div>
                    </a>
                </li>
            @endadminCan
            @adminCan('service.category.view')
                <li class="menu-item {{ isRoute('admin.serviceCategory.index', 'active') }}">
                    <a href="{{ route('admin.serviceCategory.index') }}" class="menu-link">
                        <div class="text-truncate" data-i18n="{{ __('Service Category') }}">{{ __('Service Category') }}
                        </div>
                    </a>
                </li>
            @endadminCan
        </ul>
    </li>
@endif
