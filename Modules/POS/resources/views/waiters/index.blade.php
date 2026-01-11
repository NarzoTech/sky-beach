@extends('admin.layouts.master')

@section('title')
    <title>{{ __('Waiters') }}</title>
@endsection

@section('content')
    <div class="main-content">
        <div class="container-fluid">
            <!-- Page Header -->
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h3 class="page-title mb-0">
                            <i class="fas fa-user-tie me-2"></i>{{ __('Waiters Management') }}
                        </h3>
                    </div>
                    <div class="col-md-6 text-end">
                        <a href="{{ route('admin.pos.waiters.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>{{ __('Add Waiter') }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- Search & Filter -->
            <div class="card mb-3">
                <div class="card-body py-2">
                    <form action="{{ route('admin.pos.waiters.index') }}" method="GET" class="row g-2 align-items-center">
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" name="keyword" class="form-control"
                                       placeholder="{{ __('Search by name, mobile, email...') }}"
                                       value="{{ request('keyword') }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-filter me-1"></i>{{ __('Filter') }}
                            </button>
                        </div>
                        @if(request('keyword'))
                        <div class="col-md-2">
                            <a href="{{ route('admin.pos.waiters.index') }}" class="btn btn-outline-secondary w-100">
                                <i class="fas fa-times me-1"></i>{{ __('Clear') }}
                            </a>
                        </div>
                        @endif
                    </form>
                </div>
            </div>

            <!-- Waiters List -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered">
                            <thead class="table-dark">
                                <tr>
                                    <th width="60">{{ __('SL') }}</th>
                                    <th width="80">{{ __('Image') }}</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Mobile') }}</th>
                                    <th>{{ __('Designation') }}</th>
                                    <th>{{ __('Join Date') }}</th>
                                    <th width="100">{{ __('Status') }}</th>
                                    <th width="150">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($waiters as $key => $waiter)
                                    <tr>
                                        <td>{{ $waiters->firstItem() + $key }}</td>
                                        <td class="text-center">
                                            @if($waiter->image)
                                                <img src="{{ asset($waiter->image) }}" alt="{{ $waiter->name }}"
                                                     class="rounded-circle" width="40" height="40" style="object-fit: cover;">
                                            @else
                                                <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center"
                                                     style="width: 40px; height: 40px; font-size: 16px;">
                                                    {{ strtoupper(substr($waiter->name, 0, 1)) }}
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ $waiter->name }}</strong>
                                            @if($waiter->email)
                                                <br><small class="text-muted">{{ $waiter->email }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $waiter->mobile }}</td>
                                        <td>{{ $waiter->designation ?? 'Waiter' }}</td>
                                        <td>{{ $waiter->join_date ? \Carbon\Carbon::parse($waiter->join_date)->format('d M, Y') : '-' }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('admin.pos.waiters.status', $waiter->id) }}"
                                               class="badge bg-{{ $waiter->status ? 'success' : 'danger' }}"
                                               style="cursor: pointer;">
                                                {{ $waiter->status ? __('Active') : __('Inactive') }}
                                            </a>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('admin.pos.waiters.edit', $waiter->id) }}"
                                                   class="btn btn-info" title="{{ __('Edit') }}">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-danger"
                                                        onclick="confirmDelete({{ $waiter->id }})" title="{{ __('Delete') }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                            <form id="delete-form-{{ $waiter->id }}"
                                                  action="{{ route('admin.pos.waiters.destroy', $waiter->id) }}"
                                                  method="POST" style="display: none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <i class="fas fa-user-tie fa-3x text-muted mb-3"></i>
                                            <p class="text-muted mb-0">{{ __('No waiters found') }}</p>
                                            <a href="{{ route('admin.pos.waiters.create') }}" class="btn btn-primary btn-sm mt-2">
                                                <i class="fas fa-plus me-1"></i>{{ __('Add First Waiter') }}
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($waiters->hasPages())
                        <div class="d-flex justify-content-center mt-3">
                            {{ $waiters->links() }}
                        </div>
                    @endif
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
            text: "{{ __('You will not be able to recover this waiter!') }}",
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
</script>
@endpush
