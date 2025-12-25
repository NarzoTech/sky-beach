@if (Module::isEnabled('Attendance') && Route::has('admin.attendance.index'))
    @if (checkAdminHasPermission('attendance.view') || checkAdminHasPermission('attendance.setting.view'))

        <li class="menu-item {{ Route::is('admin.attendance.*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class='menu-icon tf-icons bx bx-detail'></i>
                <div class="text-truncate" data-i18n="{{ __('Employee Attendance') }}">{{ __('Employee Attendance') }}
                </div>
            </a>
            <ul class="menu-sub">
                @adminCan('attendance.setting.view')
                    <li class="menu-item">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <div class="text-truncate" data-i18n="Settings">{{ __('Settings') }}</div>
                        </a>
                        <ul class="menu-sub">
                            <li
                                class="menu-item {{ Route::is('admin.attendance.settings.holidays.index') ? 'active' : '' }}">
                                <a href="{{ route('admin.attendance.settings.holidays.index') }}" class="menu-link">
                                    <div class="text-truncate" data-i18n="Holiday Setup">{{ __('Holiday Setup') }}</div>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endadminCan
                @adminCan('attendance.view')
                    <li class="menu-item {{ Route::is('admin.attendance.index') ? 'active' : '' }}">
                        <a href="{{ route('admin.attendance.index') }}" class="menu-link">
                            <div class="text-truncate" data-i18n="{{ __('Attendance Sheet') }}">
                                {{ __('Attendance Sheet') }}
                            </div>
                        </a>
                    </li>
                @endadminCan
            </ul>
        </li>

    @endif
@endif
