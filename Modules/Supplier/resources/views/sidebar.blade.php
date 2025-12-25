@if (checkAdminHasPermission('supplier.view') ||
        checkAdminHasPermission('supplier.group') ||
        checkAdminHasPermission('supplier.due.pay.list'))
    <li
        class="menu-item {{ isRoute(['admin.suppliers.*', 'admin.supplierGroup*', 'admin.supplier*']) ? 'active open' : '' }}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class="menu-icon tf-icons bx bx-file"></i>
            <div class="text-truncate" data-i18n="Layouts">{{ __('Manage Suppliers') }}</div>
        </a>

        <ul class="menu-sub">
            @adminCan('supplier.view')
                <li
                    class="menu-item {{ isRoute(['admin.suppliers.index', 'admin.suppliers.ledger', 'admin.suppliers.advance', 'admin.supplier.advance.pay', 'admin.suppliers.ledger-details', 'admin.suppliers.due-pay'], 'active') }}">
                    <a href="{{ route('admin.suppliers.index') }}" class="menu-link">
                        <div class="text-truncate" data-i18n="Without menu">{{ __('Supplier List') }}</div>
                    </a>
                </li>
            @endadminCan
            @adminCan('supplier.due.pay.list')
                <li class="menu-item {{ isRoute('admin.suppliers.due-pay-history', 'active') }}">
                    <a href="{{ route('admin.suppliers.due-pay-history') }}" class="menu-link">
                        <div class="text-truncate" data-i18n="Without navbar">{{ __('Supplier Due Paid List') }}</div>
                    </a>
                </li>
            @endadminCan
            @adminCan('supplier.group')
                <li class="menu-item {{ isRoute('admin.supplierGroup.index', 'active') }}">
                    <a href="{{ route('admin.supplierGroup.index') }}" class="menu-link">
                        <div class="text-truncate" data-i18n="Fluid">{{ __('Supplier Group') }}</div>
                    </a>
                </li>
            @endadminCan
        </ul>
    </li>
@endif
