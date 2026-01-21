@extends('admin.layouts.master')

@section('title')
    {{ __('Add Setting') }}
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold">{{ __('Add Setting') }}</h4>
            <a href="{{ route('admin.cms.site-settings.index') }}" class="btn btn-secondary">
                <i class="bx bx-arrow-back"></i> {{ __('Back') }}
            </a>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.cms.site-settings.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">{{ __('Key') }} <span class="text-danger">*</span></label>
                        <input type="text" name="key" value="{{ old('key') }}" class="form-control @error('key') is-invalid @enderror" required>
                        <small class="text-muted">Use lowercase with underscores (e.g., contact_email)</small>
                        @error('key')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('Label') }} <span class="text-danger">*</span></label>
                        <input type="text" name="label" value="{{ old('label') }}" class="form-control @error('label') is-invalid @enderror" required>
                        @error('label')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('Value') }}</label>
                        <input type="text" name="value" value="{{ old('value') }}" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('Group') }} <span class="text-danger">*</span></label>
                        <select name="group" class="form-select @error('group') is-invalid @enderror" id="group-select">
                            @foreach($groups as $g)
                                <option value="{{ $g }}" {{ old('group') == $g ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $g)) }}</option>
                            @endforeach
                            <option value="__new__">-- Add New Group --</option>
                        </select>
                        <input type="text" name="new_group" id="new-group-input" class="form-control mt-2 d-none" placeholder="Enter new group name">
                        @error('group')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('Type') }} <span class="text-danger">*</span></label>
                        <select name="type" class="form-select @error('type') is-invalid @enderror">
                            <option value="text" {{ old('type') == 'text' ? 'selected' : '' }}>Text</option>
                            <option value="textarea" {{ old('type') == 'textarea' ? 'selected' : '' }}>Textarea</option>
                            <option value="editor" {{ old('type') == 'editor' ? 'selected' : '' }}>Rich Editor</option>
                            <option value="image" {{ old('type') == 'image' ? 'selected' : '' }}>Image</option>
                            <option value="email" {{ old('type') == 'email' ? 'selected' : '' }}>Email</option>
                            <option value="url" {{ old('type') == 'url' ? 'selected' : '' }}>URL</option>
                            <option value="number" {{ old('type') == 'number' ? 'selected' : '' }}>Number</option>
                            <option value="color" {{ old('type') == 'color' ? 'selected' : '' }}>Color</option>
                            <option value="boolean" {{ old('type') == 'boolean' ? 'selected' : '' }}>Boolean (Yes/No)</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('Sort Order') }}</label>
                        <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" class="form-control" min="0">
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-save"></i> {{ __('Create Setting') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.getElementById('group-select').addEventListener('change', function() {
    const newGroupInput = document.getElementById('new-group-input');
    if (this.value === '__new__') {
        newGroupInput.classList.remove('d-none');
        newGroupInput.required = true;
    } else {
        newGroupInput.classList.add('d-none');
        newGroupInput.required = false;
    }
});
</script>
@endpush
