@if (checkAdminHasPermission('customer.view') ||
        checkAdminHasPermission('customer.edit') ||
        checkAdminHasPermission('customer.due.receive') ||
        checkAdminHasPermission('customer.due.receive.list') ||
        checkAdminHasPermission('customer.status'))
@endif
<div class="btn-group" role="group">
    <button id="btnGroupDrop{{ $user->id }}" type="button" class="btn btn-primary dropdown-toggle"
        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        Action
    </button>
    <div class="dropdown-menu" aria-labelledby="btnGroupDrop{{ $user->id }}">
        @adminCan('customer.view')
            <a class="dropdown-item" href="javascript:;" data-bs-toggle="modal"
                data-bs-target="#showCustomer{{ $user->id }}">Show</a>
        @endadminCan
        @adminCan('customer.edit')
            <a class="dropdown-item" href="javascript:;" data-bs-toggle="modal"
                data-bs-target="#editCustomer{{ $user->id }}">Edit</a>
        @endadminCan

        @adminCan('customer.due.receive')
            @if ($user->total_due)
                <a class="dropdown-item" href="{{ route('admin.customer.due-receive') }}?customer={{ $user->id }}">Due
                    Receive</a>
            @endif
        @endadminCan
        @adminCan('customer.due.receive.list')
            <a class="dropdown-item" href="{{ route('admin.customers.due-receive.list') }}?customer={{ $user->id }}">Due
                Receive List</a>
        @endadminCan
        @adminCan('customer.due.receive')
            <a class="dropdown-item"
                href="{{ route('admin.customer.due-receive') }}?customer={{ $user->id }}">Dismiss</a>
        @endadminCan
        @adminCan('customer.status')
            <a class="dropdown-item" href="javascript:;" onclick="status('{{ $user->id }}')"
                data-status="{{ $user->id }}">
                {{ $user->status == 1 ? 'Deactivated' : 'Activate' }}
            </a>
        @endadminCan
        @adminCan('customer.sales.list')
            <a class="dropdown-item" href="{{ route('admin.sales.index') }}?customer={{ $user->id }}">Sales</a>
        @endadminCan
        @adminCan('customer.ledger')
            <a class="dropdown-item" href="{{ route('admin.customers.ledger', $user->id) }}">{{ __('Ledger') }}</a>
        @endadminCan
        @adminCan('customer.advance')
            <a class="dropdown-item" href="{{ route('admin.customers.advance', $user->id) }}">{{ __('Advance') }}</a>
        @endadminCan
        @adminCan('customer.delete')
            <a href="javascript:;" class="dropdown-item" onclick="deleteData({{ $user->id }})">
                Delete</a>
        @endadminCan
    </div>
</div>
