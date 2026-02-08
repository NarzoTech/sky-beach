@extends('admin.layouts.master')

@section('title', __('Edit Printer'))

@section('content')
<div class="main-content">
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h3 class="page-title mb-0">
                        <i class="fas fa-print me-2"></i>{{ __('Edit Printer') }}: {{ $printer->name }}
                    </h3>
                </div>
                <div class="col-md-6 text-end">
                    <a href="{{ route('admin.pos.printers.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>{{ __('Back') }}
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.pos.printers.update', $printer->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('Printer Name') }} <span class="text-danger">*</span></label>
                                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                               value="{{ old('name', $printer->name) }}" required>
                                        @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('Location Name') }}</label>
                                        <input type="text" name="location_name" class="form-control"
                                               value="{{ old('location_name', $printer->location_name) }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('Printer Type') }} <span class="text-danger">*</span></label>
                                        <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                                            <option value="cash_counter" {{ old('type', $printer->type) === 'cash_counter' ? 'selected' : '' }}>
                                                {{ __('Cash Counter (Receipt Printer)') }}
                                            </option>
                                            <option value="kitchen" {{ old('type', $printer->type) === 'kitchen' ? 'selected' : '' }}>
                                                {{ __('Kitchen (Order Ticket Printer)') }}
                                            </option>
                                        </select>
                                        @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('Connection Type') }} <span class="text-danger">*</span></label>
                                        <select name="connection_type" class="form-select @error('connection_type') is-invalid @enderror"
                                                id="connection_type" onchange="toggleConnectionFields()" required>
                                            <option value="network" {{ old('connection_type', $printer->connection_type) === 'network' ? 'selected' : '' }}>
                                                {{ __('Network') }}
                                            </option>
                                            <option value="windows" {{ old('connection_type', $printer->connection_type) === 'windows' ? 'selected' : '' }}>
                                                {{ __('Windows') }}
                                            </option>
                                            <option value="linux" {{ old('connection_type', $printer->connection_type) === 'linux' ? 'selected' : '' }}>
                                                {{ __('Linux') }}
                                            </option>
                                            <option value="browser" {{ old('connection_type', $printer->connection_type) === 'browser' ? 'selected' : '' }}>
                                                {{ __('Browser Print (Opens print dialog)') }}
                                            </option>
                                        </select>
                                        @error('connection_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('Capability Profile') }} <span class="text-danger">*</span></label>
                                        <select name="capability_profile" class="form-select @error('capability_profile') is-invalid @enderror" required>
                                            <option value="default" {{ old('capability_profile', $printer->capability_profile) === 'default' ? 'selected' : '' }}>{{ __('Default') }}</option>
                                            <option value="simple" {{ old('capability_profile', $printer->capability_profile) === 'simple' ? 'selected' : '' }}>{{ __('Simple') }}</option>
                                            <option value="SP2000" {{ old('capability_profile', $printer->capability_profile) === 'SP2000' ? 'selected' : '' }}>{{ __('Star Branded') }}</option>
                                            <option value="TEP-200M" {{ old('capability_profile', $printer->capability_profile) === 'TEP-200M' ? 'selected' : '' }}>{{ __('Epson TEP') }}</option>
                                            <option value="P822D" {{ old('capability_profile', $printer->capability_profile) === 'P822D' ? 'selected' : '' }}>{{ __('P822D') }}</option>
                                        </select>
                                        <small class="text-muted">{{ __("If you're not sure, use the 'Default' profile") }}</small>
                                        @error('capability_profile')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('Characters per Line') }} <span class="text-danger">*</span></label>
                                        <input type="number" name="char_per_line" class="form-control @error('char_per_line') is-invalid @enderror"
                                               value="{{ old('char_per_line', $printer->char_per_line) }}" placeholder="42" required>
                                        @error('char_per_line')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div id="network-fields" style="display: none;">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">{{ __('IP Address') }} <span class="text-danger">*</span></label>
                                            <input type="text" name="ip_address" class="form-control"
                                                   value="{{ old('ip_address', $printer->ip_address) }}" placeholder="192.168.1.100">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">{{ __('Port') }} <span class="text-danger">*</span></label>
                                            <input type="number" name="port" class="form-control"
                                                   value="{{ old('port', $printer->port ?? 9100) }}" placeholder="9100">
                                            <small class="text-muted">{{ __('Most printers work on port 9100') }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="path-fields" style="display: none;">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">{{ __('Path') }} <span class="text-danger">*</span></label>
                                            <input type="text" name="path" class="form-control"
                                                   value="{{ old('path', $printer->path) }}" placeholder="">
                                            <small class="text-muted" id="path-help"></small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('Paper Width') }} <span class="text-danger">*</span></label>
                                        <select name="paper_width" class="form-select @error('paper_width') is-invalid @enderror" required>
                                            <option value="80" {{ old('paper_width', $printer->paper_width) == 80 ? 'selected' : '' }}>80mm ({{ __('Standard') }})</option>
                                            <option value="58" {{ old('paper_width', $printer->paper_width) == 58 ? 'selected' : '' }}>58mm ({{ __('Compact') }})</option>
                                        </select>
                                        @error('paper_width')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('Status') }}</label>
                                        <div class="form-check form-switch mt-2">
                                            <input class="form-check-input" type="checkbox" name="is_active"
                                                   id="is_active" {{ old('is_active', $printer->is_active) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_active">{{ __('Active') }}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.pos.printers.index') }}" class="btn btn-secondary">
                                    {{ __('Cancel') }}
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i>{{ __('Update Printer') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>{{ __('Printer Info') }}</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tr>
                                <td><strong>{{ __('Created') }}:</strong></td>
                                <td>{{ $printer->created_at->format('d M Y, H:i') }}</td>
                            </tr>
                            <tr>
                                <td><strong>{{ __('Updated') }}:</strong></td>
                                <td>{{ $printer->updated_at->format('d M Y, H:i') }}</td>
                            </tr>
                        </table>

                        <a href="{{ route('admin.pos.printers.test', $printer->id) }}" class="btn btn-outline-primary w-100 mt-3" target="_blank">
                            <i class="fas fa-print me-1"></i>{{ __('Test Print') }}
                        </a>

                        <hr>

                        <h6>{{ __('Connection Types') }}</h6>
                        <ul class="small">
                            <li><strong>{{ __('Network') }}:</strong> {{ __('Direct printing via IP address.') }}</li>
                            <li><strong>{{ __('Windows') }}:</strong> {{ __('Device files like LPT1 or COM1.') }}</li>
                            <li><strong>{{ __('Linux') }}:</strong> {{ __('Device files like /dev/lp0, /dev/usb/lp1.') }}</li>
                            <li><strong>{{ __('Browser Print') }}:</strong> {{ __('Opens browser print dialog.') }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
