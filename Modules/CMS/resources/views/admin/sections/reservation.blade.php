@extends('admin.layouts.master')

@section('title')
    {{ __('Reservation Page Sections') }}
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">{{ __('Manage Reservation Page Sections') }}</h4>
    </div>

    <div class="row">
        <!-- Sections Navigation -->
        <div class="col-md-3">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('Sections') }}</h5>
                </div>
                <div class="list-group list-group-flush">
                    @php
                        $sectionLabels = [
                            'reservation_breadcrumb' => ['label' => 'Breadcrumb', 'icon' => 'bx-link'],
                            'reservation_form' => ['label' => 'Reservation Form', 'icon' => 'bx-calendar-check'],
                            'reservation_info' => ['label' => 'Info Cards', 'icon' => 'bx-info-circle'],
                            'reservation_map' => ['label' => 'Map', 'icon' => 'bx-map'],
                        ];
                    @endphp
                    @foreach($sections as $section)
                        @php
                            $info = $sectionLabels[$section] ?? ['label' => ucwords(str_replace('_', ' ', $section)), 'icon' => 'bx-section'];
                            $data = $sectionData[$section] ?? null;
                            $isActive = $data?->section_status ?? true;
                        @endphp
                        <a href="{{ route('admin.cms.sections.edit', ['section' => $section, 'page' => 'reservation']) }}"
                           class="list-group-item list-group-item-action d-flex justify-content-between align-items-center {{ $activeSection === $section ? 'active' : '' }}">
                            <span>
                                <i class="bx {{ $info['icon'] }} me-2"></i>
                                {{ __($info['label']) }}
                            </span>
                            <span class="badge bg-{{ $isActive ? 'success' : 'secondary' }}">
                                {{ $isActive ? __('ON') : __('OFF') }}
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Section Content -->
        <div class="col-md-9">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bx {{ $sectionLabels[$activeSection]['icon'] ?? 'bx-section' }} me-2"></i>
                        {{ __($sectionLabels[$activeSection]['label'] ?? ucwords(str_replace('_', ' ', $activeSection))) }}
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">
                        {{ __('Select a section from the left menu to edit its content.') }}
                    </p>
                    <a href="{{ route('admin.cms.sections.edit', ['section' => $activeSection, 'page' => 'reservation']) }}" class="btn btn-primary">
                        <i class="bx bx-edit me-1"></i> {{ __('Edit') }} {{ __($sectionLabels[$activeSection]['label'] ?? $activeSection) }}
                    </a>
                </div>
            </div>

            <!-- Quick Status Overview -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('Sections Status Overview') }}</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>{{ __('Section') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Last Updated') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sections as $section)
                                    @php
                                        $info = $sectionLabels[$section] ?? ['label' => ucwords(str_replace('_', ' ', $section)), 'icon' => 'bx-section'];
                                        $data = $sectionData[$section] ?? null;
                                    @endphp
                                    <tr>
                                        <td>
                                            <i class="bx {{ $info['icon'] }} me-2"></i>
                                            {{ __($info['label']) }}
                                        </td>
                                        <td>
                                            <div class="form-check form-switch">
                                                <input type="checkbox" class="form-check-input toggle-section-status"
                                                       data-section="{{ $section }}"
                                                       data-page="reservation"
                                                       {{ ($data?->section_status ?? true) ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                        <td>
                                            @if($data)
                                                {{ $data->updated_at->diffForHumans() }}
                                            @else
                                                <span class="text-muted">{{ __('Not configured') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.cms.sections.edit', ['section' => $section, 'page' => 'reservation']) }}"
                                               class="btn btn-sm btn-primary">
                                                <i class="bx bx-edit"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.toggle-section-status').forEach(function(checkbox) {
    checkbox.addEventListener('change', function() {
        const section = this.dataset.section;
        const page = this.dataset.page;

        fetch(`{{ url('admin/cms/sections') }}/${section}/toggle-status`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ page: page })
        })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                this.checked = !this.checked;
                alert(data.message || 'Failed to update status');
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
