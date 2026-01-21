@extends('admin.layouts.master')

@section('title')
    {{ __('Edit Service FAQ') }}
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold">{{ __('Edit Service FAQ') }}</h4>
            <a href="{{ route('admin.restaurant.service-faqs.index') }}" class="btn btn-secondary">
                <i class="bx bx-arrow-back"></i> {{ __('Back') }}
            </a>
        </div>

        <form action="{{ route('admin.restaurant.service-faqs.update', $serviceFaq) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-8">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">{{ __('FAQ Information') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label" for="service_id">{{ __('Service') }} <span class="text-danger">*</span></label>
                                <select class="form-select @error('service_id') is-invalid @enderror" id="service_id" name="service_id" required>
                                    <option value="">{{ __('Select Service') }}</option>
                                    @foreach($services as $service)
                                        <option value="{{ $service->id }}" {{ old('service_id', $serviceFaq->service_id) == $service->id ? 'selected' : '' }}>
                                            {{ $service->title }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('service_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="question">{{ __('Question') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('question') is-invalid @enderror"
                                       id="question" name="question" value="{{ old('question', $serviceFaq->question) }}" required>
                                @error('question')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="answer">{{ __('Answer') }} <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('answer') is-invalid @enderror"
                                          id="answer" name="answer" rows="6" required>{{ old('answer', $serviceFaq->answer) }}</textarea>
                                @error('answer')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">{{ __('Settings') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label" for="order">{{ __('Display Order') }}</label>
                                <input type="number" class="form-control @error('order') is-invalid @enderror"
                                       id="order" name="order" value="{{ old('order', $serviceFaq->order) }}" min="0">
                                @error('order')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="status"
                                           name="status" value="1" {{ old('status', $serviceFaq->status) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="status">
                                        {{ __('Active') }}
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bx bx-save"></i> {{ __('Update FAQ') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
