@extends('admin.layouts.master')

@section('title', __('Add Printer'))

@section('content')
<div class="main-content">
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h3 class="page-title mb-0">
                        <i class="fas fa-print me-2"></i>{{ __('Add New Printer') }}
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
                        <form action="{{ route('admin.pos.printers.store') }}" method="POST">
                            @csrf

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('Printer Name') }} <span class="text-danger">*</span></label>
                                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                               value="{{ old('name') }}" placeholder="{{ __('e.g., Kitchen Printer 1') }}" required>
                                        @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('Location Name') }}</label>
                                        <input type="text" name="location_name" class="form-control"
                                               value="{{ old('location_name') }}" placeholder="{{ __('e.g., Main Kitchen') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('Printer Type') }} <span class="text-danger">*</span></label>
                                        <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                                            <option value="cash_counter" {{ old('type') === 'cash_counter' ? 'selected' : '' }}>
                                                {{ __('Cash Counter (Receipt Printer)') }}
                                            </option>
                                            <option value="kitchen" {{ old('type') === 'kitchen' ? 'selected' : '' }}>
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
                                            <option value="browser" {{ old('connection_type', 'browser') === 'browser' ? 'selected' : '' }}>
                                                {{ __('Browser Print (Opens print dialog)') }}
                                            </option>
                                            <option value="network" {{ old('connection_type') === 'network' ? 'selected' : '' }}>
                                                {{ __('Network (IP/Port)') }}
                                            </option>
                                            <option value="usb" {{ old('connection_type') === 'usb' ? 'selected' : '' }}>
                                                {{ __('USB (Direct)') }}
                                            </option>
                                        </select>
                                        @error('connection_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div id="network-fields" style="display: none;">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">{{ __('IP Address') }}</label>
                                            <input type="text" name="ip_address" class="form-control"
                                                   value="{{ old('ip_address') }}" placeholder="192.168.1.100">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">{{ __('Port') }}</label>
                                            <input type="number" name="port" class="form-control"
                                                   value="{{ old('port', 9100) }}" placeholder="9100">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('Paper Width') }} <span class="text-danger">*</span></label>
                                        <select name="paper_width" class="form-select @error('paper_width') is-invalid @enderror" required>
                                            <option value="80" {{ old('paper_width', 80) == 80 ? 'selected' : '' }}>80mm (Standard)</option>
                                            <option value="58" {{ old('paper_width') == 58 ? 'selected' : '' }}>58mm (Compact)</option>
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
                                                   id="is_active" {{ old('is_active', true) ? 'checked' : '' }}>
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
                                    <i class="fas fa-save me-1"></i>{{ __('Save Printer') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>{{ __('Help') }}</h5>
                    </div>
                    <div class="card-body">
                        <h6>{{ __('Connection Types') }}</h6>
                        <ul class="small">
                            <li><strong>Browser Print:</strong> Opens browser print dialog. Works with any printer.</li>
                            <li><strong>Network:</strong> Direct printing via IP address. Requires ESC/POS compatible printer.</li>
                            <li><strong>USB:</strong> Direct USB connection (requires additional setup).</li>
                        </ul>

                        <h6 class="mt-3">{{ __('Recommended Setup') }}</h6>
                        <p class="small text-muted">
                            For easiest setup, use "Browser Print" mode. The system will open a print dialog
                            when orders are placed, allowing you to print to any connected printer.
                        </p>
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
        const connectionType = document.getElementById('connection_type').value;
        const networkFields = document.getElementById('network-fields');

        if (connectionType === 'network') {
            networkFields.style.display = 'block';
        } else {
            networkFields.style.display = 'none';
        }
    }

    // Initialize on page load
    toggleConnectionFields();
</script>
@endpush
