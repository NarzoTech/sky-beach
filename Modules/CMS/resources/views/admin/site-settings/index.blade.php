@extends('admin.layouts.master')

@section('title')
    {{ __('Site Settings') }}
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold">{{ __('Site Settings') }}</h4>
            <a href="{{ route('admin.cms.site-settings.create') }}" class="btn btn-primary">
                <i class="bx bx-plus"></i> {{ __('Add Setting') }}
            </a>
        </div>

        <!-- Group Tabs -->
        <div class="card mb-4">
            <div class="card-body">
                <ul class="nav nav-pills mb-3">
                    @foreach($groups as $g)
                        <li class="nav-item">
                            <a class="nav-link {{ $group == $g ? 'active' : '' }}"
                               href="{{ route('admin.cms.site-settings.index', ['group' => $g]) }}">
                                {{ ucfirst(str_replace('_', ' ', $g)) }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5>{{ ucfirst(str_replace('_', ' ', $group)) }} Settings</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.cms.site-settings.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="group" value="{{ $group }}">

                    @forelse($settings as $setting)
                        <div class="mb-3">
                            <label class="form-label">{{ $setting->label }}</label>

                            @switch($setting->type)
                                @case('textarea')
                                    <textarea name="{{ $setting->key }}" class="form-control" rows="3">{{ $setting->value }}</textarea>
                                    @break
                                @case('image')
                                    @if($setting->value)
                                        <div class="mb-2">
                                            <img src="{{ asset($setting->value) }}" alt="{{ $setting->label }}" class="img-thumbnail" style="max-height: 100px;">
                                        </div>
                                    @endif
                                    <input type="file" name="{{ $setting->key }}" class="form-control" accept="image/*">
                                    @break
                                @case('select')
                                    <select name="{{ $setting->key }}" class="form-select">
                                        @foreach(json_decode($setting->options ?? '[]', true) as $optKey => $optLabel)
                                            <option value="{{ $optKey }}" {{ $setting->value == $optKey ? 'selected' : '' }}>{{ $optLabel }}</option>
                                        @endforeach
                                    </select>
                                    @break
                                @case('boolean')
                                    <div class="form-check form-switch">
                                        <input type="hidden" name="{{ $setting->key }}" value="0">
                                        <input type="checkbox" name="{{ $setting->key }}" value="1" class="form-check-input" {{ $setting->value ? 'checked' : '' }}>
                                    </div>
                                    @break
                                @case('email')
                                    <input type="email" name="{{ $setting->key }}" value="{{ $setting->value }}" class="form-control">
                                    @break
                                @case('url')
                                    <input type="url" name="{{ $setting->key }}" value="{{ $setting->value }}" class="form-control">
                                    @break
                                @case('number')
                                    <input type="number" name="{{ $setting->key }}" value="{{ $setting->value }}" class="form-control">
                                    @break
                                @case('color')
                                    <input type="color" name="{{ $setting->key }}" value="{{ $setting->value }}" class="form-control form-control-color">
                                    @break
                                @case('editor')
                                    <textarea name="{{ $setting->key }}" class="form-control editor" rows="5">{{ $setting->value }}</textarea>
                                    @break
                                @default
                                    <input type="text" name="{{ $setting->key }}" value="{{ $setting->value }}" class="form-control">
                            @endswitch

                            <small class="text-muted">Key: {{ $setting->key }}</small>

                            <button type="button" class="btn btn-sm btn-link text-danger float-end"
                                    onclick="deleteSetting({{ $setting->id }})">
                                <i class="bx bx-trash"></i>
                            </button>
                        </div>
                    @empty
                        <div class="alert alert-info">
                            {{ __('No settings found for this group.') }}
                        </div>
                    @endforelse

                    @if($settings->count() > 0)
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save"></i> {{ __('Save Settings') }}
                            </button>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Form -->
    <form id="delete-form" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
@endsection

@push('scripts')
<script>
function deleteSetting(id) {
    if (confirm('Are you sure you want to delete this setting?')) {
        const form = document.getElementById('delete-form');
        form.action = '{{ route("admin.cms.site-settings.destroy", "") }}/' + id;
        form.submit();
    }
}
</script>
@endpush
