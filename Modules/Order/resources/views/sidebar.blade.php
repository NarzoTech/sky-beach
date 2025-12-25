<li
    class="nav-item dropdown {{ Route::is('admin.orders') || Route::is('admin.order') || Route::is('admin.pending-payment') || Route::is('admin.rejected-payment') || Route::is('admin.pending-orders') ? 'active' : '' }}">
    <a href="#" class="nav-link has-dropdown" data-bs-toggle="dropdown"><i class="fas fa-shopping-bag"></i>
        <span>{{ __('Manage Order') }} </span>

    </a>
    <ul class="dropdown-menu">
        <li class="{{ Route::is('admin.orders') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.orders') }}">{{ __('Order History') }}</a>
        </li>

        <li class="{{ Route::is('admin.pending-orders') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.pending-orders') }}">{{ __('Pending Orders') }}
            </a>
        </li>
        <li class="{{ Route::is('admin.progress-order') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.progress-order') }}">{{ __('Progress Orders') }}
            </a>
        </li>

        <li class="{{ Route::is('admin.order.on-the-way') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.order.on-the-way') }}">{{ __('On The Way Orders') }}
            </a>
        </li>

        <li class="{{ Route::is('admin.order.delivered') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.order.delivered') }}">{{ __('Delivered Orders') }}
            </a>
        </li>
        <li class="{{ Route::is('admin.order.declined') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.order.declined') }}">{{ __('Declined Orders') }}
            </a>
        </li>
        <li class="{{ Route::is('admin.order.cash-on-delivery') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.order.cash-on-delivery') }}">{{ __('Cash On Delivery') }}
            </a>
        </li>


        <li class="{{ Route::is('admin.pending-payment') ? 'active' : '' }}"><a class="nav-link"
                href="{{ route('admin.pending-payment') }}">{{ __('Pending Payment') }}</a></li>

        <li class="{{ Route::is('admin.rejected-payment') ? 'active' : '' }}"><a class="nav-link"
                href="{{ route('admin.rejected-payment') }}">{{ __('Rejected Payment') }}</a></li>



    </ul>
</li>
