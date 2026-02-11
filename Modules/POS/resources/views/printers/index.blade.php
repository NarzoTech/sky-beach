@extends('admin.layouts.master')
@section('title', __('POS Printers'))

@section('content')
    <div class="card mt-5">
        <div class="card-header-tab card-header">
            <div class="card-header-title font-size-lg text-capitalize font-weight-normal">
                <h4 class="section_title">{{ __('POS Printers') }}</h4>
            </div>
            <div class="btn-actions-pane-right actions-icon-btn">
                <a href="{{ route('admin.pos.printers.create') }}" class="btn btn-primary">
                    <i class="bx bx-plus"></i> {{ __('Add Printer') }}
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table style="width: 100%;" class="table">
                    <thead>
                        <tr>
                            <th>{{ __('SL.') }}</th>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Type') }}</th>
                            <th>{{ __('Connection') }}</th>
                            <th>{{ __('Capability Profile') }}</th>
                            <th>{{ __('Char/Line') }}</th>
                            <th>{{ __('IP/Address') }}</th>
                            <th>{{ __('Paper Width') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Action') }}</th>
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
                                <span class="badge bg-label-warning">{{ __('Kitchen') }}</span>
                                @else
                                <span class="badge bg-label-info">{{ __('Cash Counter') }}</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-label-secondary">{{ ucfirst($printer->connection_type) }}</span>
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
                            <td>
                                <button class="btn btn-sm bg-label-{{ $printer->is_active ? 'success' : 'danger' }}"
                                        onclick="toggleStatus({{ $printer->id }})">
                                    {{ $printer->is_active ? __('Active') : __('Inactive') }}
                                </button>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn bg-label-primary dropdown-toggle"
                                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        {{ __('Action') }}
                                    </button>
                                    <div class="dropdown-menu">
                                        <a href="{{ route('admin.pos.printers.test', $printer->id) }}"
                                           class="dropdown-item" target="_blank">
                                            <i class="bx bx-printer me-1"></i> {{ __('Test Print') }}
                                        </a>
                                        <a href="{{ route('admin.pos.printers.edit', $printer->id) }}"
                                           class="dropdown-item">
                                            <i class="bx bx-edit me-1"></i> {{ __('Edit') }}
                                        </a>
                                        <a href="javascript:void(0)" class="dropdown-item text-danger"
                                           onclick="confirmDelete({{ $printer->id }})">
                                            <i class="bx bx-trash me-1"></i> {{ __('Delete') }}
                                        </a>
                                    </div>
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
                                <i class="bx bx-printer bx-lg text-muted mb-3 d-block"></i>
                                <p class="text-muted mb-2">{{ __('No printers configured') }}</p>
                                <a href="{{ route('admin.pos.printers.create') }}" class="btn btn-primary btn-sm">
                                    <i class="bx bx-plus me-1"></i>{{ __('Add First Printer') }}
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
        <div class="card-header">
            <h5>{{ __('Printer Types') }}</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="d-flex align-items-start">
                        <span class="badge bg-label-warning me-3 p-2"><i class="bx bx-food-menu"></i></span>
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
                        <span class="badge bg-label-info me-3 p-2"><i class="bx bx-receipt"></i></span>
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
