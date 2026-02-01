@extends('admin.layouts.master')

@section('title')
    {{ __('Chefs') }}
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold">{{ __('Chefs') }}</h4>
            <a href="{{ route('admin.restaurant.chefs.create') }}" class="btn btn-primary">
                <i class="bx bx-plus"></i> {{ __('Add Chef') }}
            </a>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>{{ __('Image') }}</th>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Designation') }}</th>
                                <th>{{ __('Specialization') }}</th>
                                <th>{{ __('Experience') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($chefs as $chef)
                                <tr>
                                    <td>
                                        @if($chef->image)
                                            <img src="{{ $chef->image_url }}" alt="{{ $chef->name }}" 
                                                 style="width: 50px; height: 50px; object-fit: cover; border-radius: 50%;">
                                        @else
                                            <div style="width: 50px; height: 50px; background: #f0f0f0; border-radius: 50%;"></div>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ $chef->name }}</strong>
                                        @if($chef->is_featured)
                                            <span class="badge bg-warning">Featured</span>
                                        @endif
                                    </td>
                                    <td>{{ $chef->designation }}</td>
                                    <td>{{ $chef->specialization ?? '-' }}</td>
                                    <td>{{ $chef->experience_years ? $chef->experience_years . ' years' : '-' }}</td>
                                    <td>
                                        @if($chef->status)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.restaurant.chefs.edit', $chef) }}" class="btn btn-sm btn-icon btn-primary">
                                            <i class="bx bx-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.restaurant.chefs.destroy', $chef) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
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
                                    <td colspan="7" class="text-center py-4">{{ __('No chefs found') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">{{ $chefs->links() }}</div>
            </div>
        </div>
    </div>
@endsection
