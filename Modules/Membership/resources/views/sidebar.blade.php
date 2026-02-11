@if (checkAdminHasPermission('membership.view'))
    <li class="menu-item {{ isRoute(['membership.*'], 'active open') }}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class='menu-icon tf-icons bx bx-id-card'></i>
            <div class="text-truncate" data-i18n="{{ __('Membership') }}">{{ __('Membership') }}</div>
        </a>
        <ul class="menu-sub">
            <li class="menu-item {{ isRoute('membership.index', 'active') }}">
                <a href="{{ route('membership.index') }}" class="menu-link">
                    <div class="text-truncate" data-i18n="{{ __('Dashboard') }}">{{ __('Dashboard') }}</div>
                </a>
            </li>
            <li class="menu-item {{ isRoute('membership.programs.*', 'active') }}">
                <a href="{{ route('membership.programs.index') }}" class="menu-link">
                    <div class="text-truncate" data-i18n="{{ __('Programs') }}">{{ __('Programs') }}</div>
                </a>
            </li>
            <li class="menu-item {{ isRoute('membership.customers.*', 'active') }}">
                <a href="{{ route('membership.customers.index') }}" class="menu-link">
                    <div class="text-truncate" data-i18n="{{ __('Customers') }}">{{ __('Customers') }}</div>
                </a>
            </li>
            <li class="menu-item {{ isRoute('membership.transactions.*', 'active') }}">
                <a href="{{ route('membership.transactions.index') }}" class="menu-link">
                    <div class="text-truncate" data-i18n="{{ __('Transactions') }}">{{ __('Transactions') }}</div>
                </a>
            </li>
        </ul>
    </li>
@endif
