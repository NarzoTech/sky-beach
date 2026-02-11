@if (checkAdminHasPermission('expense.create') ||
        checkAdminHasPermission('expense.view') ||
        checkAdminHasPermission('expense.type.view') ||
        checkAdminHasPermission('expense_supplier.view'))


    <li class="{{ isRoute(['admin.expense.*', 'admin.expense.type.index', 'admin.expense-suppliers.*'], 'active open') }} menu-item">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class='menu-icon tf-icons bx bx-wallet'></i>
            <div class="text-truncate" data-i18n="{{ __('Expenses') }}">{{ __('Expenses') }}</div>
        </a>

        <ul class="menu-sub">
            @adminCan('expense.create')
                <li class="{{ isRoute('admin.expense.create', 'active') }} menu-item">
                    <a href="{{ route('admin.expense.index') }}?type=new" class="menu-link">
                        {{ __('New Expense') }}
                    </a>
                </li>
            @endadminCan
            @adminCan('expense.view')
                <li class="{{ isRoute('admin.expense.index', 'active') }} menu-item">
                    <a href="{{ route('admin.expense.index') }}" class="menu-link">
                        {{ __('Expense List') }}
                    </a>
                </li>
            @endadminCan
            @adminCan('expense.type.view')
                <li class="{{ isRoute('admin.expense.type.index', 'active') }} menu-item">
                    <a href="{{ route('admin.expense.type.index') }}" class="menu-link">
                        {{ __('Expense Type') }}
                    </a>
                </li>
            @endadminCan
            @adminCan('expense_supplier.view')
                <li class="{{ isRoute('admin.expense-suppliers.index', 'active') }} menu-item">
                    <a href="{{ route('admin.expense-suppliers.index') }}" class="menu-link">
                        {{ __('Expense Suppliers') }}
                    </a>
                </li>
            @endadminCan
            @adminCan('expense_supplier.due_pay')
                <li class="{{ isRoute('admin.expense-suppliers.due-pay-history', 'active') }} menu-item">
                    <a href="{{ route('admin.expense-suppliers.due-pay-history') }}" class="menu-link">
                        {{ __('Due Pay History') }}
                    </a>
                </li>
            @endadminCan
        </ul>
    </li>
@endif
