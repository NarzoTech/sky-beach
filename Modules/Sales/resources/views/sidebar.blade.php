@adminCan('sales.view')
    <li class="menu-item {{ isRoute('admin.sales.index', 'active') }}">
        <a href="{{ route('admin.sales.index') }}" class="menu-link">
            <i class='menu-icon tf-icons bx bx-basket'></i>
            <div class="text-truncate" data-i18n="{{ __('Sales') }}">{{ __('Sales') }}</div>
        </a>
    </li>
@endadminCan
