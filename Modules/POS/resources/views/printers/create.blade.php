@extends('admin.layouts.master')
@section('title', __('Add Printer'))

@section('content')
    <div class="main-content">
        <section class="section">
            <div class="section-body">
                <div class="card mb-3 page-title-card">
                    <div class="card-header d-flex justify-content-between">
                        <h4 class="section_title">{{ __('Add New Printer') }}</h4>
                        <a href="{{ route('admin.pos.printers.index') }}" class="btn btn-primary">
                            <i class="bx bx-arrow-back"></i> {{ __('Back') }}
                        </a>
                    </div>
                </div>

                <form action="{{ route('admin.pos.printers.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="card">
                                <div class="card-header">
                                    <h5>{{ __('Printer Configuration') }}</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>{{ __('Printer Name') }} <span class="text-danger">*</span></label>
                                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                                       value="{{ old('name') }}" placeholder="{{ __('Short descriptive name to recognize printer') }}" required>
                                                @error('name')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>{{ __('Location Name') }}</label>
                                                <input type="text" name="location_name" class="form-control"
                                                       value="{{ old('location_name') }}" placeholder="{{ __('e.g., Main Kitchen') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>{{ __('Printer Type') }} <span class="text-danger">*</span></label>
                                                <select name="type" class="form-control @error('type') is-invalid @enderror" required>
                                                    <option value="cash_counter" {{ old('type') === 'cash_counter' ? 'selected' : '' }}>
                                                        {{ __('Cash Counter (Receipt Printer)') }}
                                                    </option>
                                                    <option value="kitchen" {{ old('type') === 'kitchen' ? 'selected' : '' }}>
                                                        {{ __('Kitchen (Order Ticket Printer)') }}
                                                    </option>
                                                </select>
                                                @error('type')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>{{ __('Connection Type') }} <span class="text-danger">*</span></label>
                                                <select name="connection_type" class="form-control @error('connection_type') is-invalid @enderror"
                                                        id="connection_type" onchange="toggleConnectionFields()" required>
                                                    <option value="network" {{ old('connection_type') === 'network' ? 'selected' : '' }}>
                                                        {{ __('Network') }}
                                                    </option>
                                                    <option value="windows" {{ old('connection_type') === 'windows' ? 'selected' : '' }}>
                                                        {{ __('Windows') }}
                                                    </option>
                                                    <option value="linux" {{ old('connection_type') === 'linux' ? 'selected' : '' }}>
                                                        {{ __('Linux') }}
                                                    </option>
                                                    <option value="browser" {{ old('connection_type', 'browser') === 'browser' ? 'selected' : '' }}>
                                                        {{ __('Browser Print (Opens print dialog)') }}
                                                    </option>
                                                </select>
                                                @error('connection_type')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>{{ __('Capability Profile') }} <span class="text-danger">*</span></label>
                                                <select name="capability_profile" class="form-control @error('capability_profile') is-invalid @enderror" required>
                                                    <option value="default" {{ old('capability_profile', 'default') === 'default' ? 'selected' : '' }}>{{ __('Default') }}</option>
                                                    <option value="simple" {{ old('capability_profile') === 'simple' ? 'selected' : '' }}>{{ __('Simple') }}</option>
                                                    <option value="SP2000" {{ old('capability_profile') === 'SP2000' ? 'selected' : '' }}>{{ __('Star Branded') }}</option>
                                                    <option value="TEP-200M" {{ old('capability_profile') === 'TEP-200M' ? 'selected' : '' }}>{{ __('Epson TEP') }}</option>
                                                    <option value="P822D" {{ old('capability_profile') === 'P822D' ? 'selected' : '' }}>{{ __('P822D') }}</option>
                                                </select>
                                                <small class="text-muted">{{ __("If you're not sure, use the 'Default' profile") }}</small>
                                                @error('capability_profile')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>{{ __('Characters per Line') }} <span class="text-danger">*</span></label>
                                                <input type="number" name="char_per_line" class="form-control @error('char_per_line') is-invalid @enderror"
                                                       value="{{ old('char_per_line', 42) }}" placeholder="42" required>
                                                @error('char_per_line')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div id="network-fields" style="display: none;">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>{{ __('IP Address') }} <span class="text-danger">*</span></label>
                                                    <input type="text" name="ip_address" class="form-control"
                                                           value="{{ old('ip_address') }}" placeholder="192.168.1.100">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>{{ __('Port') }} <span class="text-danger">*</span></label>
                                                    <input type="number" name="port" class="form-control"
                                                           value="{{ old('port', 9100) }}" placeholder="9100">
                                                    <small class="text-muted">{{ __('Most printers work on port 9100') }}</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="path-fields" style="display: none;">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>{{ __('Path') }} <span class="text-danger">*</span></label>
                                                    <input type="text" name="path" class="form-control"
                                                           value="{{ old('path') }}" placeholder="">
                                                    <small class="text-muted" id="path-help"></small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>{{ __('Paper Width') }} <span class="text-danger">*</span></label>
                                                <select name="paper_width" class="form-control @error('paper_width') is-invalid @enderror" required>
                                                    <option value="80" {{ old('paper_width', 80) == 80 ? 'selected' : '' }}>80mm ({{ __('Standard') }})</option>
                                                    <option value="58" {{ old('paper_width') == 58 ? 'selected' : '' }}>58mm ({{ __('Compact') }})</option>
                                                </select>
                                                @error('paper_width')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>{{ __('Status') }}</label>
                                                <div class="form-check form-switch mt-2">
                                                    <input class="form-check-input" type="checkbox" name="is_active"
                                                           id="is_active" {{ old('is_active', true) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="is_active">{{ __('Active') }}</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit -->
                            <div class="card mt-3">
                                <div class="card-body d-flex justify-content-end gap-2">
                                    <a href="{{ route('admin.pos.printers.index') }}" class="btn bg-danger text-white">
                                        {{ __('Cancel') }}
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bx bx-save me-1"></i>{{ __('Save Printer') }}
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5>{{ __('Help') }}</h5>
                                </div>
                                <div class="card-body">
                                    <h6>{{ __('Connection Types') }}</h6>
                                    <ul class="small">
                                        <li><strong>{{ __('Network') }}:</strong> {{ __('Direct printing via IP address. Requires ESC/POS compatible printer.') }}</li>
                                        <li><strong>{{ __('Windows') }}:</strong> {{ __('Device files like LPT1 (parallel) or COM1 (serial).') }}</li>
                                        <li><strong>{{ __('Linux') }}:</strong> {{ __('Device files like /dev/lp0 (parallel), /dev/usb/lp1 (USB), /dev/ttyUSB0 (USB-Serial).') }}</li>
                                        <li><strong>{{ __('Browser Print') }}:</strong> {{ __('Opens browser print dialog. Works with any printer.') }}</li>
                                    </ul>

                                    <h6 class="mt-3">{{ __('Capability Profile') }}</h6>
                                    <p class="small text-muted mb-0">
                                        {{ __("Support for commands and code pages varies between printer vendors and models. If you're not sure, use the 'Default' or 'Simple' profile.") }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </section>
    </div>
@endsection

@push('js')
<script>
    function toggleConnectionFields() {
        var connectionType = document.getElementById('connection_type').value;
        var networkFields = document.getElementById('network-fields');
        var pathFields = document.getElementById('path-fields');
        var pathHelp = document.getElementById('path-help');

        networkFields.style.display = 'none';
        pathFields.style.display = 'none';

        if (connectionType === 'network') {
            networkFields.style.display = 'block';
        } else if (connectionType === 'windows') {
            pathFields.style.display = 'block';
            pathHelp.innerHTML = '<b>{{ __("Windows") }}:</b> {{ __("The device files will be along the lines of") }} <code>LPT1</code> ({{ __("parallel") }}) / <code>COM1</code> ({{ __("serial") }})';
        } else if (connectionType === 'linux') {
            pathFields.style.display = 'block';
            pathHelp.innerHTML = '<b>{{ __("Linux") }}:</b> {{ __("Your printer device file will be somewhere like") }} <code>/dev/lp0</code> ({{ __("parallel") }}), <code>/dev/usb/lp1</code> ({{ __("USB") }}), <code>/dev/ttyUSB0</code> ({{ __("USB-Serial") }}), <code>/dev/ttyS0</code> ({{ __("serial") }})';
        }
    }

    // Initialize on page load
    toggleConnectionFields();
</script>
@endpush
