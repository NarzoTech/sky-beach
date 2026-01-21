@extends('admin.layouts.master')

@section('title')
    {{ __('Counters') }}
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold">{{ __('Counters') }}</h4>
            <a href="{{ route('admin.cms.counters.create') }}" class="btn btn-primary">
                <i class="bx bx-plus"></i> {{ __('Add Counter') }}
            </a>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Icon</th>
                                <th>Label</th>
                                <th>Value</th>
                                <th>Suffix</th>
                                <th>Order</th>
                                <th>Status</th>
                                <th width="120">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($counters as $counter)
                                <tr>
                                    <td><i class="{{ $counter->icon ?? 'bx bx-star' }}" style="font-size: 24px;"></i></td>
                                    <td><strong>{{ $counter->label }}</strong></td>
                                    <td>{{ number_format($counter->value) }}</td>
                                    <td>{{ $counter->suffix ?? '-' }}</td>
                                    <td>{{ $counter->sort_order }}</td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input type="checkbox" class="form-check-input toggle-status" data-id="{{ $counter->id }}" {{ $counter->is_active ? 'checked' : '' }}>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.cms.counters.edit', $counter) }}" class="btn btn-sm btn-icon btn-primary">
                                            <i class="bx bx-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.cms.counters.destroy', $counter) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-icon btn-danger">
                                                <i class="bx bx-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">{{ __('No counters found') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if(isset($counters) && method_exists($counters, 'links'))
                    <div class="mt-3">{{ $counters->links() }}</div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.toggle-status').forEach(function(checkbox) {
    checkbox.addEventListener('change', function() {
        const id = this.dataset.id;
        fetch(`{{ url('admin/cms/counters') }}/${id}/toggle-status`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Status toggled successfully
            }
        })
        .catch(error => {
            console.error('Error:', error);
            this.checked = !this.checked;
        });
    });
});
</script>
@endpush
