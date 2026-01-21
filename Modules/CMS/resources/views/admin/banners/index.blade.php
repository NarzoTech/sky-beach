@extends('admin.layouts.master')

@section('title')
    {{ __('Promotional Banners') }}
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold">{{ __('Promotional Banners') }}</h4>
            <a href="{{ route('admin.cms.banners.create') }}" class="btn btn-primary">
                <i class="bx bx-plus"></i> {{ __('Add Banner') }}
            </a>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-4">
                        <select name="position" class="form-select">
                            <option value="">All Positions</option>
                            @foreach($positions as $pos)
                                <option value="{{ $pos }}" {{ request('position') == $pos ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $pos)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('admin.cms.banners.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
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
                                <th width="100">Preview</th>
                                <th>Title</th>
                                <th>Position</th>
                                <th>Schedule</th>
                                <th>Order</th>
                                <th>Status</th>
                                <th width="120">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($banners as $banner)
                                <tr>
                                    <td>
                                        @if($banner->image)
                                            <img src="{{ asset($banner->image) }}" alt="{{ $banner->title }}" class="img-thumbnail" style="max-height: 50px;">
                                        @else
                                            <span class="text-muted">No image</span>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ $banner->title ?? 'Untitled' }}</strong>
                                        @if($banner->subtitle)
                                            <br><small class="text-muted">{{ Str::limit($banner->subtitle, 30) }}</small>
                                        @endif
                                    </td>
                                    <td><span class="badge bg-label-info">{{ ucfirst(str_replace('_', ' ', $banner->position)) }}</span></td>
                                    <td>
                                        @if($banner->start_date || $banner->end_date)
                                            <small>
                                                {{ $banner->start_date ? $banner->start_date->format('M d, Y') : 'Always' }}
                                                -
                                                {{ $banner->end_date ? $banner->end_date->format('M d, Y') : 'Forever' }}
                                            </small>
                                        @else
                                            <small class="text-muted">Always active</small>
                                        @endif
                                    </td>
                                    <td>{{ $banner->sort_order }}</td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input type="checkbox" class="form-check-input toggle-status" data-id="{{ $banner->id }}" {{ $banner->is_active ? 'checked' : '' }}>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.cms.banners.edit', $banner) }}" class="btn btn-sm btn-icon btn-primary">
                                            <i class="bx bx-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.cms.banners.destroy', $banner) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
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
                                    <td colspan="7" class="text-center py-4">{{ __('No banners found') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">{{ $banners->links() }}</div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.toggle-status').forEach(function(checkbox) {
    checkbox.addEventListener('change', function() {
        const id = this.dataset.id;
        fetch(`{{ url('admin/cms/banners') }}/${id}/toggle-status`, {
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
