@extends('admin.layouts.master')

@section('title', __('POS Printers'))

@section('content')
<div class="main-content">
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h3 class="page-title mb-0">
                        <i class="fas fa-print me-2"></i>{{ __('POS Printers') }}
                    </h3>
                </div>
                <div class="col-md-6 text-end">
                    <a href="{{ route('admin.pos.printers.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>{{ __('Add Printer') }}
                    </a>
                </div>
            </div>
        </div>

        <!-- Printers List -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th width="60">{{ __('SL') }}</th>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Type') }}</th>
                                <th>{{ __('Connection') }}</th>
                                <th>{{ __('Capability Profile') }}</th>
                                <th>{{ __('Char/Line') }}</th>
                                <th>{{ __('IP/Address') }}</th>
                                <th>{{ __('Paper Width') }}</th>
                                <th width="100">{{ __('Status') }}</th>
                                <th width="150">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($printers as $key => $printer)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>
                                    <strong>{{ $printer->name }}</strong>
                                    @if($printer->location_name)
                                    <br><small class="text-muted">{{ $printer->location_name }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($printer->type === 'kitchen')
                                    <span class="badge bg-warning text-dark">
                                        <i class="fas fa-utensils me-1"></i>{{ __('Kitchen') }}
                                    </span>
                                    @else
                                    <span class="badge bg-info">
                                        <i class="fas fa-cash-register me-1"></i>{{ __('Cash Counter') }}
                                    </span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ ucfirst($printer->connection_type) }}</span>
                                </td>
                                <td>{{ ucfirst($printer->capability_profile ?? 'default') }}</td>
                                <td>{{ $printer->char_per_line ?? 42 }}</td>
                                <td>
                                    @if($printer->connection_type === 'network' && $printer->ip_address)
                                    {{ $printer->ip_address }}{{ $printer->port ? ':' . $printer->port : '' }}
                                    @elseif(in_array($printer->connection_type, ['windows', 'linux']) && $printer->path)
                                    <code>{{ $printer->path }}</code>
                                    @else
                                    <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>{{ $printer->paper_width }}mm</td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-{{ $printer->is_active ? 'success' : 'danger' }}"
                                            onclick="toggleStatus({{ $printer->id }})">
                                        {{ $printer->is_active ? __('Active') : __('Inactive') }}
                                    </button>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.pos.printers.test', $printer->id) }}"
                                           class="btn btn-outline-secondary" title="{{ __('Test Print') }}" target="_blank">
                                            <i class="fas fa-print"></i>
                                        </a>
                                        <a href="{{ route('admin.pos.printers.edit', $printer->id) }}"
                                           class="btn btn-info" title="{{ __('Edit') }}">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-danger"
                                                onclick="confirmDelete({{ $printer->id }})" title="{{ __('Delete') }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    <form id="delete-form-{{ $printer->id }}"
                                          action="{{ route('admin.pos.printers.destroy', $printer->id) }}"
                                          method="POST" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="10" class="text-center py-4">
                                    <i class="fas fa-print fa-3x text-muted mb-3"></i>
                                    <p class="text-muted mb-0">{{ __('No printers configured') }}</p>
                                    <a href="{{ route('admin.pos.printers.create') }}" class="btn btn-primary btn-sm mt-2">
                                        <i class="fas fa-plus me-1"></i>{{ __('Add First Printer') }}
                                    </a>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Info Card -->
        <div class="card mt-3">
            <div class="card-body">
                <h5><i class="fas fa-info-circle me-2"></i>{{ __('Printer Types') }}</h5>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="d-flex align-items-start">
                            <span class="badge bg-warning text-dark me-3 p-2"><i class="fas fa-utensils"></i></span>
                            <div>
                                <strong>{{ __('Kitchen Printer') }}</strong>
                                <p class="text-muted mb-0 small">
                                    {{ __('Prints order tickets for kitchen staff. Shows items without prices in large format.') }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-start">
                            <span class="badge bg-info me-3 p-2"><i class="fas fa-cash-register"></i></span>
                            <div>
                                <strong>{{ __('Cash Counter Printer') }}</strong>
                                <p class="text-muted mb-0 small">
                                    {{ __('Prints order slips and receipts with prices for cashier and customer.') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    function confirmDelete(id) {
        Swal.fire({
            title: "{{ __('Are you sure?') }}",
            text: "{{ __('You will not be able to recover this printer!') }}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: "{{ __('Yes, delete it!') }}",
            cancelButtonText: "{{ __('Cancel') }}"
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        });
    }

    function toggleStatus(id) {
        $.ajax({
            url: "{{ url('admin/pos/printers') }}/" + id + "/toggle-status",
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {
                location.reload();
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to update printer status.'
                });
            }
        });
    }
</script>
@endpush
