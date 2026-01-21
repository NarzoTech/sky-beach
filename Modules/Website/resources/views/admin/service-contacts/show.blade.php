@extends('admin.layouts.master')

@section('title')
    {{ __('View Inquiry') }}
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold">{{ __('View Inquiry') }}</h4>
            <a href="{{ route('admin.restaurant.service-contacts.index') }}" class="btn btn-secondary">
                <i class="bx bx-arrow-back"></i> {{ __('Back') }}
            </a>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('Inquiry Details') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{ __('Service') }}</label>
                                <p>{{ $serviceContact->service->title ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{ __('Date') }}</label>
                                <p>{{ $serviceContact->created_at->format('M d, Y H:i') }}</p>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{ __('Name') }}</label>
                                <p>{{ $serviceContact->name }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{ __('Email') }}</label>
                                <p><a href="mailto:{{ $serviceContact->email }}">{{ $serviceContact->email }}</a></p>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{ __('Phone') }}</label>
                                <p>{{ $serviceContact->phone ?? 'Not provided' }}</p>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('Message') }}</label>
                            <div class="p-3 bg-light rounded">
                                {!! nl2br(e($serviceContact->message)) !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('Update Status') }}</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.restaurant.service-contacts.update-status', $serviceContact) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label class="form-label">{{ __('Status') }}</label>
                                <select name="status" class="form-select">
                                    <option value="pending" {{ $serviceContact->status === 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                                    <option value="read" {{ $serviceContact->status === 'read' ? 'selected' : '' }}>{{ __('Read') }}</option>
                                    <option value="replied" {{ $serviceContact->status === 'replied' ? 'selected' : '' }}>{{ __('Replied') }}</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">{{ __('Admin Notes') }}</label>
                                <textarea name="admin_notes" class="form-control" rows="4">{{ $serviceContact->admin_notes }}</textarea>
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
                        <a href="mailto:{{ $serviceContact->email }}" class="btn btn-info w-100 mb-2">
                            <i class="bx bx-envelope"></i> {{ __('Send Email') }}
                        </a>
                        @if($serviceContact->phone)
                        <a href="tel:{{ $serviceContact->phone }}" class="btn btn-success w-100">
                            <i class="bx bx-phone"></i> {{ __('Call') }}
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
