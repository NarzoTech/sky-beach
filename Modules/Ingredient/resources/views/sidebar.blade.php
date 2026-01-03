@if (checkAdminHasPermission('ingredient.view') ||
        checkAdminHasPermission('ingredient.unit.view') ||
        checkAdminHasPermission('ingredient.category.view') ||
        checkAdminHasPermission('ingredient.brand.view') ||
        checkAdminHasPermission('ingredient.create'))


    <li
        class="menu-item {{ Route::is('admin.ingredient.*') || Route::is('admin.unit.*') || Route::is('admin.category.*') || Route::is('admin.brand.*') ? 'active open' : '' }}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class='menu-icon tf-icons bx bx-grid'></i>
            <div class="text-truncate" data-i18n="Front Pages">{{ __('Manage Ingredient') }}</div>
        </a>
        <ul class="menu-sub">
            @adminCan('ingredient.view')
                <li
                    class="menu-item {{ Route::is('admin.ingredient.index') || Route::is('admin.ingredient.edit') || Route::is('admin.ingredient.show') ? 'active' : '' }}">
                    <a href="{{ route('admin.ingredient.index') }}" class="menu-link">
                        <div class="text-truncate" data-i18n="{{ __('Ingredient List') }}">{{ __('Ingredient List') }}</div>
                    </a>
                </li>
            @endadminCan
            @adminCan('ingredient.create')
                <li class="menu-item {{ Route::is('admin.ingredient.create') ? 'active' : '' }}">
                    <a href="{{ route('admin.ingredient.create') }}" class="menu-link">
                        <div class="text-truncate" data-i18n="Pricing">{{ __('Add Ingredient') }}</div>
                    </a>
                </li>
            @endadminCan
            @adminCan('ingredient.unit.view')
                <li class="menu-item {{ Route::is('admin.unit.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.unit.index') }}" class="menu-link">
                        <div class="text-truncate" data-i18n="Payment">{{ __('Unit Type') }}</div>
                    </a>
                </li>
            @endadminCan
            @adminCan('ingredient.category.view')
                <li class="menu-item {{ Route::is('admin.category.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.category.index') }}" class="menu-link">
                        <div class="text-truncate" data-i18n="Checkout">{{ __('Category') }}</div>
                    </a>
                </li>
            @endadminCan
            @adminCan('ingredient.brand.view')
                <li class="menu-item {{ Route::is('admin.brand.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.brand.index') }}" class="menu-link">
                        <div class="text-truncate" data-i18n="Help Center">{{ __('Brand') }}</div>
                    </a>
                </li>
            @endadminCan

        </ul>
    </li>
@endif
