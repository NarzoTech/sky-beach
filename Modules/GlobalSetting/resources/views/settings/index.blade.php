@extends('admin.layouts.master')
@section('title', __('General Setting'))
@section('content')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <div class="section-header-back">
                    <a href="{{ route('admin.settings') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
                </div>

                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active">
                        <a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a>
                    </div>
                    <div class="breadcrumb-item active">
                        <a href="{{ route('admin.settings') }}">{{ __('Settings') }}</a>
                    </div>

                </div>
            </div>
            <div class="section-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <ul class="nav nav-pills flex-column" id="generalTab" role="tablist">
                                    @include('globalsetting::settings.tabs.navbar')
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-body">
                                <div class="tab-content" id="myTabContent2">
                                    @include('globalsetting::settings.sections.general')
                                    @include('globalsetting::settings.sections.logo-favicon')
                                    @include('globalsetting::settings.sections.default-avatar')
                                    @include('globalsetting::settings.sections.copyright')
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
        //Tab active setup locally
        $(document).ready(function() {
            "use strict";
            var activeTab = localStorage.getItem("activeTab");
            if (activeTab) {
                $('#generalTab a[href="#' + activeTab + '"]').tab("show");
            } else {
                $("#generalTab a:first").tab("show");
            }

            $('a[data-bs-toggle="tab"]').on("shown.bs.tab", function(e) {
                var newTab = $(e.target).attr("href").substring(1);
                localStorage.setItem("activeTab", newTab);
            });
        });
        //input image preview
        $(document).ready(function() {
            setupImagePreview('logoInput', 'logoPreview');
            setupImagePreview('favInput', 'favPreview');
            setupImagePreview('defaultAvatarInput', 'defaultAvatarPreview');
            setupImagePreview('breadcrumpInput', 'breadcrumpPreview');
        });
        //Maintenance mode toggler
        function changeMaintenanceModeStatus() {
            var isDemo = "{{ strtoupper(config('app.app_mode')) ?? 'LIVE' }}"
            if (isDemo == 'DEMO') {
                toastr.error("{{ __('This Is Demo Version. You Can Not Change Anything') }}");
                return;
            }
            $.ajax({
                type: "put",
                data: {
                    _token: '{{ csrf_token() }}',
                },
                url: "{{ url('/admin/update-maintenance-mode-status') }}",
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                    } else {
                        toastr.warning(response.message);
                    }
                },
                error: function(err) {
                    console.log(err);

                }
            })
        }
    </script>

    <script>
        function copyText() {
            var textToCopy = document.getElementById("copyCronText");
            var range = document.createRange();
            range.selectNode(textToCopy);
            window.getSelection().removeAllRanges();
            window.getSelection().addRange(range);
            document.execCommand("copy");

            toastr.success("{{ __('Copied to clipboard') }}");
        }
    </script>
@endpush
