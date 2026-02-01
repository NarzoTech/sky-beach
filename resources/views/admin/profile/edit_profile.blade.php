@extends('admin.layouts.master')
@section('title', __('Edit Profile'))
@section('content')
    <div class="row">
        {{-- Profile Card --}}
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body text-center">
                    <div class="profile-image-wrapper position-relative d-inline-block mb-3">
                        <img alt="Profile Image" id="profileImgPreview" src="{{ $admin->image_url }}"
                            class="rounded-circle" style="width: 150px; height: 150px; object-fit: cover; border: 4px solid #f0f0f0;">
                        <label for="profileImgInput" class="profile-image-overlay position-absolute rounded-circle d-flex align-items-center justify-content-center"
                            style="width: 150px; height: 150px; top: 0; left: 50%; transform: translateX(-50%); background: rgba(0,0,0,0.5); opacity: 0; cursor: pointer; transition: opacity 0.3s;">
                            <i class="bx bx-camera text-white" style="font-size: 24px;"></i>
                        </label>
                    </div>
                    <h4 class="mb-1">{{ $admin->name }}</h4>
                    <p class="text-muted mb-3">{{ $admin->email }}</p>
                    @if($admin->roles->count() > 0)
                        <div class="d-flex justify-content-center gap-2 flex-wrap">
                            @foreach($admin->roles as $role)
                                <span class="badge bg-primary">{{ $role->name }}</span>
                            @endforeach
                        </div>
                    @endif
                    <hr class="my-4">
                    <div class="text-start">
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0">
                                <span class="avatar avatar-sm bg-label-primary rounded">
                                    <i class="bx bx-calendar"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <small class="text-muted d-block">{{ __('Member Since') }}</small>
                                <span>{{ $admin->created_at->format('M d, Y') }}</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <span class="avatar avatar-sm bg-label-success rounded">
                                    <i class="bx bx-check-circle"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <small class="text-muted d-block">{{ __('Status') }}</small>
                                <span class="badge bg-{{ $admin->status == 'active' ? 'success' : 'danger' }}">
                                    {{ ucfirst($admin->status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Edit Forms --}}
        <div class="col-lg-8">
            {{-- Profile Information --}}
            <div class="card mb-4">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <i class="bx bx-user me-2 text-primary" style="font-size: 20px;"></i>
                        <h5 class="mb-0">{{ __('Profile Information') }}</h5>
                    </div>
                </div>
                <div class="card-body">
                    <form @adminCan('admin.profile.edit') action="{{ route('admin.profile-update') }}" @endadminCan
                        enctype="multipart/form-data" method="POST">
                        @csrf
                        @method('PUT')

                        <input id="profileImgInput" type="file" class="d-none" name="image" accept="image/*">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Full Name') }} <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bx bx-user"></i></span>
                                    <input type="text" class="form-control" value="{{ $admin->name }}" name="name" placeholder="{{ __('Enter your name') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Email Address') }} <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bx bx-envelope"></i></span>
                                    <input type="email" class="form-control" value="{{ $admin->email }}" name="email" placeholder="{{ __('Enter your email') }}" required>
                                </div>
                            </div>
                        </div>

                        @adminCan('admin.profile.edit')
                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bx bx-save me-1"></i> {{ __('Save Changes') }}
                                </button>
                            </div>
                        @endadminCan
                    </form>
                </div>
            </div>

            {{-- Change Password --}}
            <div class="card mb-4">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <i class="bx bx-lock-alt me-2 text-warning" style="font-size: 20px;"></i>
                        <h5 class="mb-0">{{ __('Change Password') }}</h5>
                    </div>
                </div>
                <div class="card-body">
                    <form @adminCan('admin.profile.edit') action="{{ route('admin.update-password') }}" @endadminCan method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">{{ __('Current Password') }} <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bx bx-key"></i></span>
                                    <input type="password" class="form-control" name="current_password" id="currentPassword" placeholder="{{ __('Enter current password') }}" required>
                                    <button class="btn btn-outline-secondary toggle-password" type="button" data-target="currentPassword">
                                        <i class="bx bx-hide"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('New Password') }} <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bx bx-lock"></i></span>
                                    <input type="password" class="form-control" name="password" id="newPassword" placeholder="{{ __('Enter new password') }}" required>
                                    <button class="btn btn-outline-secondary toggle-password" type="button" data-target="newPassword">
                                        <i class="bx bx-hide"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Confirm Password') }} <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bx bx-lock-open"></i></span>
                                    <input type="password" class="form-control" name="password_confirmation" id="confirmPassword" placeholder="{{ __('Confirm new password') }}" required>
                                    <button class="btn btn-outline-secondary toggle-password" type="button" data-target="confirmPassword">
                                        <i class="bx bx-hide"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-warning d-flex align-items-center mt-3 mb-0" role="alert">
                            <i class="bx bx-info-circle me-2" style="font-size: 20px;"></i>
                            <div class="small">
                                {{ __('Make sure your password is at least 8 characters long and includes a mix of letters and numbers for better security.') }}
                            </div>
                        </div>

                        @adminCan('admin.profile.edit')
                            <div class="mt-4">
                                <button type="submit" class="btn btn-warning">
                                    <i class="bx bx-refresh me-1"></i> {{ __('Update Password') }}
                                </button>
                            </div>
                        @endadminCan
                    </form>
                </div>
            </div>

            {{-- Security Questions --}}
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <i class="bx bx-shield-quarter me-2 text-info" style="font-size: 20px;"></i>
                        <h5 class="mb-0">{{ __('Security Questions') }}</h5>
                    </div>
                </div>
                <div class="card-body">
                    <div class="alert alert-info d-flex align-items-center mb-4" role="alert">
                        <i class="bx bx-info-circle me-2" style="font-size: 20px;"></i>
                        <div class="small">
                            {{ __('Security questions help you recover your account if you forget your password. Please choose questions only you know the answers to.') }}
                        </div>
                    </div>

                    @if($admin->security_question_1)
                        <div class="alert alert-success d-flex align-items-center mb-4" role="alert">
                            <i class="bx bx-check-circle me-2" style="font-size: 20px;"></i>
                            <div class="small">
                                {{ __('Security questions are already set up. You can update them below.') }}
                            </div>
                        </div>
                    @endif

                    <form @adminCan('admin.profile.edit') action="{{ route('admin.update-security-questions') }}" @endadminCan method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            {{-- Question 1 --}}
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Security Question 1') }} <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bx bx-help-circle"></i></span>
                                    <input type="text" class="form-control" name="security_question_1"
                                        placeholder="{{ __('Enter your security question') }}"
                                        value="{{ $admin->security_question_1 }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Answer 1') }} <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bx bx-message-rounded-dots"></i></span>
                                    <input type="text" class="form-control" name="security_answer_1"
                                        placeholder="{{ __('Enter your answer') }}" required>
                                </div>
                                <small class="text-muted">{{ __('Answers are case-insensitive') }}</small>
                            </div>

                            {{-- Question 2 --}}
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Security Question 2') }} <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bx bx-help-circle"></i></span>
                                    <input type="text" class="form-control" name="security_question_2"
                                        placeholder="{{ __('Enter your security question') }}"
                                        value="{{ $admin->security_question_2 }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Answer 2') }} <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bx bx-message-rounded-dots"></i></span>
                                    <input type="text" class="form-control" name="security_answer_2"
                                        placeholder="{{ __('Enter your answer') }}" required>
                                </div>
                                <small class="text-muted">{{ __('Answers are case-insensitive') }}</small>
                            </div>
                        </div>

                        @adminCan('admin.profile.edit')
                            <div class="mt-4">
                                <button type="submit" class="btn btn-info">
                                    <i class="bx bx-shield-quarter me-1"></i> {{ __('Save Security Questions') }}
                                </button>
                            </div>
                        @endadminCan
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
<style>
    .profile-image-wrapper:hover .profile-image-overlay {
        opacity: 1 !important;
    }
    .avatar {
        width: 38px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .avatar i {
        font-size: 18px;
    }
    .bg-label-primary {
        background-color: rgba(105, 108, 255, 0.16) !important;
        color: #696cff !important;
    }
    .bg-label-success {
        background-color: rgba(113, 221, 55, 0.16) !important;
        color: #71dd37 !important;
    }
    .toggle-password {
        border-left: 0;
    }
    .toggle-password:hover {
        background-color: #f5f5f9;
    }
</style>
@endpush

@push('js')
<script>
    "use strict";
    $(document).ready(function() {
        // Image preview
        setupImagePreview('profileImgInput', 'profileImgPreview');

        // Toggle password visibility
        $('.toggle-password').on('click', function() {
            const targetId = $(this).data('target');
            const input = $('#' + targetId);
            const icon = $(this).find('i');

            if (input.attr('type') === 'password') {
                input.attr('type', 'text');
                icon.removeClass('bx-hide').addClass('bx-show');
            } else {
                input.attr('type', 'password');
                icon.removeClass('bx-show').addClass('bx-hide');
            }
        });
    });
</script>
@endpush
