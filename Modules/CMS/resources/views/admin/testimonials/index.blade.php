@extends('admin.layouts.master')

@section('title')
    {{ __('Testimonials') }}
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold">{{ __('Testimonials') }}</h4>
            <a href="{{ route('admin.cms.testimonials.create') }}" class="btn btn-primary">
                <i class="bx bx-plus"></i> {{ __('Add Testimonial') }}
            </a>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-4">
                        <input type="text" name="keyword" value="{{ request('keyword') }}" class="form-control" placeholder="Search by name...">
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('admin.cms.testimonials.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="60">Image</th>
                                <th>Name</th>
                                <th>Position</th>
                                <th>Rating</th>
                                <th>Order</th>
                                <th>Featured</th>
                                <th>Status</th>
                                <th width="120">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($testimonials as $testimonial)
                                <tr>
                                    <td>
                                        <img src="{{ $testimonial->image_url }}" alt="{{ $testimonial->name }}" class="rounded-circle" width="40" height="40" style="object-fit: cover;">
                                    </td>
                                    <td><strong>{{ $testimonial->name }}</strong></td>
                                    <td>{{ $testimonial->position ?? '-' }}<br><small class="text-muted">{{ $testimonial->company }}</small></td>
                                    <td>
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="bx bxs-star {{ $i <= $testimonial->rating ? 'text-warning' : 'text-muted' }}"></i>
                                        @endfor
                                    </td>
                                    <td>{{ $testimonial->sort_order }}</td>
                                    <td>
                                        @if($testimonial->is_featured)
                                            <span class="badge bg-info">Featured</span>
                                        @else
                                            <span class="badge bg-secondary">No</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input type="checkbox" class="form-check-input toggle-status" data-id="{{ $testimonial->id }}" {{ $testimonial->is_active ? 'checked' : '' }}>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.cms.testimonials.edit', $testimonial) }}" class="btn btn-sm btn-icon btn-primary">
                                            <i class="bx bx-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.cms.testimonials.destroy', $testimonial) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
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
                                    <td colspan="8" class="text-center py-4">{{ __('No testimonials found') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">{{ $testimonials->links() }}</div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.toggle-status').forEach(function(checkbox) {
    checkbox.addEventListener('change', function() {
        const id = this.dataset.id;
        fetch(`{{ url('admin/cms/testimonials') }}/${id}/toggle-status`, {
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
            this.checked = !this.checked; // Revert on error
        });
    });
});
</script>
@endpush
