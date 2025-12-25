@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Manage Addons') }}</title>
@endsection
@section('content')
    <!-- Main Content -->
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <div class="section-header-back">
                    <a href="{{ route('admin.settings') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
                </div>
                <h1>{{ __('Manage Addons') }}</h1>

            </div>

    </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>{{ __('Manage Addons') }}</h4>
                        <div class="card-header-action">
                            @adminCan('addon.install')
                                <a href="{{ route('admin.addons.install') }}" class="btn btn-success"><i
                                        class="fas fa-plus"></i>
                                    {{ __('Install New') }}</a>
                            @endadminCan
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach ($addons as $addon)
                                <div class="text-center col-lg-3 col-md-6 col-sm-12">
                                    <div class="p-1 border card">
                                        <div class="card-body">
                                            @if ($addon->icon)
                                                <img src="{{ $addon->icon }}" alt="">
                                            @endif
                                            <h4>{{ $addon->name }}</h4>
                                            <p class="card-text">{{ $addon->description }}</p>
                                            <p class="card-text">{{ __('version') }}: {{ $addon->version }}</p>
                                            <p class="card-text">{{ __('Update') }}: {{ $addon->last_update }}
                                            </p>
                                            @if ($addon->is_default)
                                                <button type="button" disabled
                                                    class="btn btn-success">{{ __('Installed') }}</a>
                                                @else
                                                    @if ($addon->status)
                                                        <a href="{{ route('admin.addons.update.status', $addon->slug) }}"
                                                            class="btn btn-warning">{{ __('Disable') }}</a>
                                                    @else
                                                        <a href="{{ route('admin.addons.update.status', $addon->slug) }}"
                                                            class="btn btn-success">{{ __('Enable') }}</a>
                                                    @endif
                                                    <a data-bs-toggle="modal" data-bs-target="#deleteModal"
                                                        href="{{ route('admin.addons.uninstall', $addon->slug) }}"
                                                        onclick="deleteData('{{ route('admin.addons.uninstall', $addon->slug) }}')"
                                                        class="btn btn-danger">{{ __('Uninstall') }}</a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    </section>
    </div>
@endsection

@push('js')
    <script>
        "use strict";

        function deleteData(url) {
            $("#deleteForm").attr("action", url)
        }
    </script>
@endpush
