@extends('admin.layouts.master')

@section('title')
    {{ __('Services') }}
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold">{{ __('Services') }}</h4>
            <a href="{{ route('admin.restaurant.website-services.create') }}" class="btn btn-primary">
                <i class="bx bx-plus"></i> {{ __('Add Service') }}
            </a>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="80">{{ __('Image') }}</th>
                                <th>{{ __('Title') }}</th>
                                <th>{{ __('Price') }}</th>
                                <th>{{ __('Duration') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($services as $service)
                                <tr>
                                    <td>
                                        <img src="{{ $service->image_url }}" alt="{{ $service->title }}" class="rounded" style="width: 60px; height: 60px; object-fit: cover;">
                                    </td>
                                    <td>
                                        <strong>{{ $service->title }}</strong>
                                        @if($service->is_featured)
                                            <span class="badge bg-warning">Featured</span>
                                        @endif
                                    </td>
                                    <td>{{ $service->price ? '$' . number_format($service->price, 2) : '-' }}</td>
                                    <td>{{ $service->duration ? $service->duration . ' min' : '-' }}</td>
                                    <td>
                                        @if($service->status)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.restaurant.website-services.edit', $service) }}" class="btn btn-sm btn-icon btn-primary">
                                            <i class="bx bx-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.restaurant.website-services.destroy', $service) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
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
                                    <td colspan="6" class="text-center py-4">{{ __('No services found') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">{{ $services->links() }}</div>
            </div>
        </div>
    </div>
@endsection
