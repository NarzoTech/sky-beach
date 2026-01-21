@extends('admin.layouts.master')

@section('title')
    {{ __('Gallery Images') }}
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold">{{ __('Gallery Images') }}</h4>
            <a href="{{ route('admin.cms.gallery.create') }}" class="btn btn-primary">
                <i class="bx bx-plus"></i> {{ __('Add Image') }}
            </a>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-3">
                        <select name="category" class="form-select">
                            <option value="">All Categories</option>
                            @foreach($categories ?? [] as $cat)
                                <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $cat)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="page" class="form-select">
                            <option value="">All Pages</option>
                            @foreach($pages ?? [] as $p)
                                <option value="{{ $p }}" {{ request('page') == $p ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $p)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('admin.cms.gallery.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="row">
                    @forelse($images as $image)
                        <div class="col-md-3 col-sm-6 mb-4">
                            <div class="card h-100">
                                <div class="position-relative">
                                    <img src="{{ $image->image_url }}" alt="{{ $image->alt }}" class="card-img-top" style="height: 150px; object-fit: cover;">
                                    <div class="position-absolute top-0 end-0 p-2">
                                        <div class="form-check form-switch">
                                            <input type="checkbox" class="form-check-input toggle-status" data-id="{{ $image->id }}" {{ $image->is_active ? 'checked' : '' }}>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body p-2">
                                    <h6 class="card-title mb-1">{{ Str::limit($image->title, 20) ?: 'Untitled' }}</h6>
                                    <small class="text-muted">
                                        @if($image->category)
                                            <span class="badge bg-label-info">{{ $image->category }}</span>
                                        @endif
                                        @if($image->page)
                                            <span class="badge bg-label-secondary">{{ $image->page }}</span>
                                        @endif
                                    </small>
                                </div>
                                <div class="card-footer p-2 d-flex justify-content-end gap-1">
                                    <a href="{{ route('admin.cms.gallery.edit', $image) }}" class="btn btn-sm btn-icon btn-primary">
                                        <i class="bx bx-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.cms.gallery.destroy', $image) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-icon btn-danger">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="alert alert-info text-center">{{ __('No gallery images found') }}</div>
                        </div>
                    @endforelse
                </div>
                @if(isset($images) && method_exists($images, 'links'))
                    <div class="mt-3">{{ $images->links() }}</div>
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
        fetch(`{{ url('admin/cms/gallery') }}/${id}/toggle-status`, {
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
