@adminCan('sales.view')
    <li class="menu-item {{ isRoute('admin.sales.index', 'active') }}">
        <a href="{{ route('admin.sales.index') }}" class="menu-link">
            <i class='menu-icon tf-icons bx bx-basket'></i>
            <div class="text-truncate" data-i18n="{{ __('Sales') }}">{{ __('Sales') }}</div>
        </a>
    </li>
@endadminCan
@adminCan('restaurant.website-order.view')
    <li class="menu-item {{ request()->is('admin/restaurant/website-orders*') ? 'active' : '' }}">
        <a href="{{ route('admin.restaurant.website-orders.index') }}" class="menu-link">
            <i class='menu-icon tf-icons bx bx-cart'></i>
            <div class="text-truncate" data-i18n="{{ __('Website Orders') }}">{{ __('Website Orders') }}</div>
        </a>
    </li>
@endadminCan
