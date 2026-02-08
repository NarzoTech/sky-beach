@if (checkAdminHasPermission('cash.flow.view') ||
        checkAdminHasPermission('account.view') ||
        checkAdminHasPermission('account.create') ||
        checkAdminHasPermission('deposit.withdraw.view') ||
        checkAdminHasPermission('balance.transfer.view') ||
        checkAdminHasPermission('bank.view'))



    <li
        class="menu-item  {{ isRoute(['admin.accounts.*', 'admin.bank.index', 'admin.cashflow', 'admin.opening-balance', 'admin.balance.transfer'], 'active open') }}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class='menu-icon tf-icons bx bx-transfer-alt'></i>
            <div class="text-truncate" data-i18n="{{ __('Manage Accounts') }}">{{ __('Manage Accounts') }}</div>
        </a>

        <ul class="menu-sub">
            @adminCan('cash.flow.view')
                <li class="{{ isRoute('admin.cashflow', 'active') }} menu-item">
                    <a href="{{ route('admin.cashflow') }}" class="menu-link">
                        {{ __('Cash Flow') }}
                    </a>
                </li>
            @endadminCan
            @adminCan('account.create')
                <li class="{{ isRoute('admin.accounts.create', 'active') }} menu-item">
                    <a href="{{ route('admin.accounts.create') }}" class="menu-link">
                        {{ __('Create Account') }}
                    </a>
                </li>
            @endadminCan
            @adminCan('account.view')
                <li class="{{ isRoute('admin.accounts.index', 'active') }} menu-item">
                    <a href="{{ route('admin.accounts.index') }}" class="menu-link">
                        {{ __('Account List') }}
                    </a>
                </li>
            @endadminCan
            @adminCan('balance.transfer.view')
                <li class="{{ isRoute('admin.balance.transfer', 'active') }} menu-item">
                    <a href="{{ route('admin.balance.transfer') }}" class="menu-link">
                        {{ __('Balance Transfer') }}
                    </a>
                </li>
            @endadminCan
            @adminCan('deposit.withdraw.view')
                <li class="{{ isRoute('admin.opening-balance', 'active') }} menu-item">
                    <a href="{{ route('admin.opening-balance') }}" class="menu-link">
                        {{ __('Deposit') }}/{{ __('Withdraw') }}
                    </a>
                </li>
            @endadminCan
            @adminCan('bank.view')
                <li class="{{ isRoute('admin.bank.index', 'active') }} menu-item">
                    <a href="{{ route('admin.bank.index') }}" class="menu-link">
                        {{ __('Bank') }}
                    </a>
                </li>
            @endadminCan
        </ul>
    </li>
@endif
