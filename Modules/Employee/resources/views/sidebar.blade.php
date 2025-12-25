@if (checkAdminHasPermission('employee.view') ||
        checkAdminHasPermission('employee.create') ||
        checkAdminHasPermission('employee.view.payment'))


    <li class="{{ isRoute(['admin.employee.*', 'admin.salary.*'], 'active open') }} menu-item">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class='menu-icon tf-icons bx bx-user'></i>
            <div class="text-truncate" data-i18n="{{ __('Employees') }}">{{ __('Employees') }}</div>
        </a>
        <ul class="menu-sub">
            @adminCan('employee.view')
                <li
                    class="{{ isRoute(['admin.employee.index', 'admin.employee.salary.view', 'admin.employee.salary.edit', 'admin.employee.salary.create'], 'active') }} menu-item">
                    <a href="{{ route('admin.employee.index') }}" class="menu-link">
                        {{ __('Employee List') }}
                    </a>
                </li>
            @endadminCan
            @adminCan('employee.create')
                <li class="{{ isRoute('admin.employee.create', 'active') }} menu-item">
                    <a href="{{ route('admin.employee.create') }}" class="menu-link">
                        {{ __('Add New Employee') }}
                    </a>
                </li>
            @endadminCan
            @adminCan('employee.view.payment')
                <li class="{{ isRoute('admin.salary.list', 'active') }} menu-item">
                    <a href="{{ route('admin.salary.list') }}" class="menu-link">
                        {{ __('All Paid Salary') }}
                    </a>
                </li>
            @endadminCan
        </ul>
    </li>
@endif
