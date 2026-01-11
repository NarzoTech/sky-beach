@extends('admin.layouts.master')

@section('title')
    <title>{{ __('Edit Waiter') }}</title>
@endsection

@section('content')
    <div class="main-content">
        <div class="container-fluid">
            <!-- Page Header -->
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h3 class="page-title mb-0">
                            <i class="fas fa-user-edit me-2"></i>{{ __('Edit Waiter') }}
                        </h3>
                    </div>
                    <div class="col-md-6 text-end">
                        <a href="{{ route('admin.pos.waiters.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>{{ __('Back to List') }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.pos.waiters.update', $waiter->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Left Column -->
                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="name" class="form-label">{{ __('Name') }} <span class="text-danger">*</span></label>
                                        <input type="text" name="name" id="name"
                                               class="form-control @error('name') is-invalid @enderror"
                                               value="{{ old('name', $waiter->name) }}" required placeholder="{{ __('Enter waiter name') }}">
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="mobile" class="form-label">{{ __('Mobile') }} <span class="text-danger">*</span></label>
                                        <input type="text" name="mobile" id="mobile"
                                               class="form-control @error('mobile') is-invalid @enderror"
                                               value="{{ old('mobile', $waiter->mobile) }}" required placeholder="{{ __('Enter mobile number') }}">
                                        @error('mobile')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">{{ __('Email') }}</label>
                                        <input type="email" name="email" id="email"
                                               class="form-control @error('email') is-invalid @enderror"
                                               value="{{ old('email', $waiter->email) }}" placeholder="{{ __('Enter email address') }}">
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="designation" class="form-label">{{ __('Designation') }}</label>
                                        <input type="text" name="designation" id="designation"
                                               class="form-control @error('designation') is-invalid @enderror"
                                               value="{{ old('designation', $waiter->designation ?? 'Waiter') }}" placeholder="{{ __('e.g., Senior Waiter, Head Waiter') }}">
                                        @error('designation')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="nid" class="form-label">{{ __('NID / ID Number') }}</label>
                                        <input type="text" name="nid" id="nid"
                                               class="form-control @error('nid') is-invalid @enderror"
                                               value="{{ old('nid', $waiter->nid) }}" placeholder="{{ __('Enter ID number') }}">
                                        @error('nid')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="join_date" class="form-label">{{ __('Join Date') }}</label>
                                        <input type="date" name="join_date" id="join_date"
                                               class="form-control @error('join_date') is-invalid @enderror"
                                               value="{{ old('join_date', $waiter->join_date ? \Carbon\Carbon::parse($waiter->join_date)->format('Y-m-d') : '') }}">
                                        @error('join_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="salary" class="form-label">{{ __('Salary') }}</label>
                                        <div class="input-group">
                                            <span class="input-group-text">{{ currency_icon() }}</span>
                                            <input type="number" name="salary" id="salary" step="0.01"
                                                   class="form-control @error('salary') is-invalid @enderror"
                                                   value="{{ old('salary', $waiter->salary) }}" placeholder="0.00">
                                        </div>
                                        @error('salary')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="yearly_leaves" class="form-label">{{ __('Yearly Leaves') }}</label>
                                        <input type="number" name="yearly_leaves" id="yearly_leaves"
                                               class="form-control @error('yearly_leaves') is-invalid @enderror"
                                               value="{{ old('yearly_leaves', $waiter->yearly_leaves ?? 12) }}" placeholder="12">
                                        @error('yearly_leaves')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12 mb-3">
                                        <label for="address" class="form-label">{{ __('Address') }}</label>
                                        <textarea name="address" id="address" rows="2"
                                                  class="form-control @error('address') is-invalid @enderror"
                                                  placeholder="{{ __('Enter address') }}">{{ old('address', $waiter->address) }}</textarea>
                                        @error('address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column - Image -->
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <label class="form-label">{{ __('Profile Photo') }}</label>
                                        <div class="mb-3">
                                            <div id="image-preview" class="mx-auto mb-3"
                                                 style="width: 150px; height: 150px; border: 2px dashed #ccc; border-radius: 50%; display: flex; align-items: center; justify-content: center; overflow: hidden; background: #f8f9fa;">
                                                @if($waiter->image)
                                                    <img src="{{ asset($waiter->image) }}" style="width: 100%; height: 100%; object-fit: cover;">
                                                @else
                                                    <i class="fas fa-user fa-4x text-muted"></i>
                                                @endif
                                            </div>
                                            <input type="file" name="image" id="image" class="form-control"
                                                   accept="image/*" onchange="previewImage(this)">
                                            <small class="text-muted">{{ __('Max: 2MB. Formats: JPG, PNG, GIF') }}</small>
                                        </div>

                                        <!-- Status Badge -->
                                        <div class="mt-3">
                                            <span class="badge bg-{{ $waiter->status ? 'success' : 'danger' }} fs-6">
                                                <i class="fas fa-{{ $waiter->status ? 'check-circle' : 'times-circle' }} me-1"></i>
                                                {{ $waiter->status ? __('Active') : __('Inactive') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.pos.waiters.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i>{{ __('Cancel') }}
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>{{ __('Update Waiter') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
<script>
    function previewImage(input) {
        const preview = document.getElementById('image-preview');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.innerHTML = '<img src="' + e.target.result + '" style="width: 100%; height: 100%; object-fit: cover;">';
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endpush
