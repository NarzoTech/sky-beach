@extends('admin.layouts.master')

@section('title')
    {{ __('View Message') }}
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold">{{ __('View Message') }}</h4>
            <a href="{{ route('admin.restaurant.contact-messages.index') }}" class="btn btn-secondary">
                <i class="bx bx-arrow-back"></i> {{ __('Back') }}
            </a>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('Message Details') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{ __('Name') }}</label>
                                <p>{{ $contactMessage->name }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{ __('Date') }}</label>
                                <p>{{ $contactMessage->created_at->format('M d, Y H:i') }}</p>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{ __('Email') }}</label>
                                <p><a href="mailto:{{ $contactMessage->email }}">{{ $contactMessage->email }}</a></p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{ __('Phone') }}</label>
                                <p>
                                    @if($contactMessage->phone)
                                        <a href="tel:{{ $contactMessage->phone }}">{{ $contactMessage->phone }}</a>
                                    @else
                                        {{ __('Not provided') }}
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('Subject') }}</label>
                            <p>{{ $contactMessage->subject ?? 'No subject' }}</p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('Message') }}</label>
                            <div class="p-3 bg-light rounded">
                                {!! nl2br(e($contactMessage->message)) !!}
                            </div>
                        </div>

                        @if($contactMessage->newsletter)
                        <div class="mb-3">
                            <span class="badge bg-primary"><i class="bx bx-envelope me-1"></i> {{ __('Subscribed to newsletter') }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('Update Status') }}</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.restaurant.contact-messages.update-status', $contactMessage) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label class="form-label">{{ __('Status') }}</label>
                                <select name="status" class="form-select">
                                    <option value="unread" {{ $contactMessage->status === 'unread' ? 'selected' : '' }}>{{ __('Unread') }}</option>
                                    <option value="read" {{ $contactMessage->status === 'read' ? 'selected' : '' }}>{{ __('Read') }}</option>
                                    <option value="replied" {{ $contactMessage->status === 'replied' ? 'selected' : '' }}>{{ __('Replied') }}</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bx bx-save"></i> {{ __('Update Status') }}
                            </button>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('Quick Actions') }}</h5>
                    </div>
                    <div class="card-body">
                        <a href="mailto:{{ $contactMessage->email }}?subject=Re: {{ $contactMessage->subject }}" class="btn btn-info w-100 mb-2">
                            <i class="bx bx-envelope"></i> {{ __('Reply via Email') }}
                        </a>
                        @if($contactMessage->phone)
                        <a href="tel:{{ $contactMessage->phone }}" class="btn btn-success w-100 mb-2">
                            <i class="bx bx-phone"></i> {{ __('Call') }}
                        </a>
                        @endif
                        <form action="{{ route('admin.restaurant.contact-messages.destroy', $contactMessage) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this message?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="bx bx-trash"></i> {{ __('Delete Message') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
