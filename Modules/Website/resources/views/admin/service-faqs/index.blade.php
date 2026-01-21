@extends('admin.layouts.master')

@section('title')
    {{ __('Service FAQs') }}
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold">{{ __('Service FAQs') }}</h4>
            <a href="{{ route('admin.restaurant.service-faqs.create') }}" class="btn btn-primary">
                <i class="bx bx-plus"></i> {{ __('Add Service FAQ') }}
            </a>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form action="{{ route('admin.restaurant.service-faqs.index') }}" method="GET" class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">{{ __('Filter by Service') }}</label>
                        <select name="service_id" class="form-select">
                            <option value="">{{ __('All Services') }}</option>
                            @foreach($services as $service)
                                <option value="{{ $service->id }}" {{ request('service_id') == $service->id ? 'selected' : '' }}>
                                    {{ $service->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">{{ __('Filter') }}</button>
                        <a href="{{ route('admin.restaurant.service-faqs.index') }}" class="btn btn-secondary">{{ __('Reset') }}</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>{{ __('Service') }}</th>
                                <th>{{ __('Question') }}</th>
                                <th>{{ __('Order') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($faqs as $faq)
                                <tr>
                                    <td>{{ $faq->service->title ?? 'N/A' }}</td>
                                    <td><strong>{{ Str::limit($faq->question, 50) }}</strong></td>
                                    <td>{{ $faq->order }}</td>
                                    <td>
                                        @if($faq->status)
                                            <span class="badge bg-success">{{ __('Active') }}</span>
                                        @else
                                            <span class="badge bg-danger">{{ __('Inactive') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.restaurant.service-faqs.edit', $faq) }}" class="btn btn-sm btn-icon btn-primary">
                                            <i class="bx bx-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.restaurant.service-faqs.destroy', $faq) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-icon btn-danger">
                                                <i class="bx bx-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">{{ __('No Service FAQs found') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">{{ $faqs->appends(request()->query())->links() }}</div>
            </div>
        </div>
    </div>
@endsection
