@extends('admin.auth.app')
@section('title')
    <title>{{ __('Forgot Password') }}</title>
    <style>
        .input-group .input-group-text {
            border-color: #d9dee3;
            background-color: #fff;
        }
        .input-group .form-control {
            border-color: #d9dee3;
        }
        .input-group .form-control:focus {
            border-color: #696cff;
        }
        .input-group:focus-within .input-group-text {
            border-color: #696cff;
        }
    </style>
@endsection
@section('content')
    <div class="d-flex col-12 col-lg-5 col-xl-4 align-items-center authentication-bg p-sm-12 p-6">
        <div class="w-px-400 mx-auto mt-12 pt-5">
            <h4 class="mb-1">{{ __('Forgot Password') }}</h4>
            <p class="mb-4 text-muted">{{ __('Answer your security questions to reset your password') }}</p>

            <form action="{{ route('admin.verify-security-questions') }}" method="POST" id="securityForm">
                @csrf
                <div class="form-group mb-3">
                    <label for="security_email" class="form-label">{{ __('Email Address') }}</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bx bx-envelope"></i></span>
                        <input id="security_email" type="email" class="form-control" name="email"
                            placeholder="{{ __('Enter your email') }}" value="{{ old('email') }}" required>
                    </div>
                    <button type="button" class="btn btn-outline-primary w-100 mt-2" id="loadQuestionsBtn">
                        {{ __('Load Questions') }}
                    </button>
                </div>

                <div id="securityQuestionsContainer" style="display: none;">
                    <div class="form-group mb-3">
                        <label class="form-label" id="question1Label">{{ __('Security Question 1') }}</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bx bx-help-circle"></i></span>
                            <input type="text" class="form-control" name="security_answer_1"
                                placeholder="{{ __('Enter your answer') }}">
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label" id="question2Label">{{ __('Security Question 2') }}</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bx bx-help-circle"></i></span>
                            <input type="text" class="form-control" name="security_answer_2"
                                placeholder="{{ __('Enter your answer') }}">
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <button type="submit" class="btn btn-primary btn-lg w-100">
                            <i class="bx bx-check-circle me-1"></i> {{ __('Verify & Reset Password') }}
                        </button>
                    </div>
                </div>

                <div id="noQuestionsAlert" class="alert alert-warning" style="display: none;">
                    <i class="bx bx-info-circle me-2"></i>
                    {{ __('Security questions are not set up for this account. Please contact administrator.') }}
                </div>
            </form>

            <div class="form-group text-center mt-4">
                <a href="{{ route('admin.login') }}" class="text-primary">
                    <i class="bx bx-arrow-back me-1"></i> {{ __('Back to Login') }}
                </a>
            </div>
        </div>
    </div>
@endsection

@push('js')
<script>
    $(document).ready(function() {
        $('#loadQuestionsBtn').on('click', function() {
            const email = $('#security_email').val();
            if (!email) {
                toastr.error('{{ __("Please enter your email address") }}');
                return;
            }

            const btn = $(this);
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

            $.ajax({
                url: '{{ route("admin.get-security-questions") }}',
                method: 'POST',
                data: {
                    email: email,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success && response.questions) {
                        $('#question1Label').text(response.questions.question_1);
                        $('#question2Label').text(response.questions.question_2);
                        $('#securityQuestionsContainer').slideDown();
                        $('#noQuestionsAlert').hide();
                        $('input[name="security_answer_1"]').attr('required', true);
                        $('input[name="security_answer_2"]').attr('required', true);
                    } else {
                        $('#securityQuestionsContainer').hide();
                        $('#noQuestionsAlert').slideDown();
                    }
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON?.message || '{{ __("An error occurred") }}');
                    $('#securityQuestionsContainer').hide();
                    $('#noQuestionsAlert').hide();
                },
                complete: function() {
                    btn.prop('disabled', false).html('{{ __("Load Questions") }}');
                }
            });
        });
    });
</script>
@endpush
