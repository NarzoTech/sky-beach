@extends('admin.layouts.master')

@section('title')
    {{ __('Service Inquiries') }}
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold">{{ __('Service Inquiries') }}</h4>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form action="{{ route('admin.restaurant.service-contacts.index') }}" method="GET" class="row g-3">
                    <div class="col-md-4">
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
                    <div class="col-md-4">
                        <label class="form-label">{{ __('Filter by Status') }}</label>
                        <select name="status" class="form-select">
                            <option value="">{{ __('All Status') }}</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                            <option value="read" {{ request('status') == 'read' ? 'selected' : '' }}>{{ __('Read') }}</option>
                            <option value="replied" {{ request('status') == 'replied' ? 'selected' : '' }}>{{ __('Replied') }}</option>
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">{{ __('Filter') }}</button>
                        <a href="{{ route('admin.restaurant.service-contacts.index') }}" class="btn btn-secondary">{{ __('Reset') }}</a>
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
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Email') }}</th>
                                <th>{{ __('Phone') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($contacts as $contact)
                                <tr>
                                    <td>{{ $contact->service->title ?? 'N/A' }}</td>
                                    <td><strong>{{ $contact->name }}</strong></td>
                                    <td>{{ $contact->email }}</td>
                                    <td>{{ $contact->phone ?? '-' }}</td>
                                    <td>
                                        @if($contact->status === 'pending')
                                            <span class="badge bg-warning">{{ __('Pending') }}</span>
                                        @elseif($contact->status === 'read')
                                            <span class="badge bg-info">{{ __('Read') }}</span>
                                        @else
                                            <span class="badge bg-success">{{ __('Replied') }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $contact->created_at->format('M d, Y H:i') }}</td>
                                    <td>
                                        <a href="{{ route('admin.restaurant.service-contacts.show', $contact) }}" class="btn btn-sm btn-icon btn-info">
                                            <i class="bx bx-show"></i>
                                        </a>
                                        <form action="{{ route('admin.restaurant.service-contacts.destroy', $contact) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
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
                                    <td colspan="7" class="text-center py-4">{{ __('No inquiries found') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">{{ $contacts->appends(request()->query())->links() }}</div>
            </div>
        </div>
    </div>
@endsection
