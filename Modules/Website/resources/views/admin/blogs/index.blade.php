@extends('admin.layouts.master')

@section('title')
    {{ __('Blogs') }}
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold">{{ __('Blogs') }}</h4>
            <a href="{{ route('admin.restaurant.blogs.create') }}" class="btn btn-primary">
                <i class="bx bx-plus"></i> {{ __('Add Blog') }}
            </a>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>{{ __('Title') }}</th>
                                <th>{{ __('Author') }}</th>
                                <th>{{ __('Published') }}</th>
                                <th>{{ __('Views') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($blogs as $blog)
                                <tr>
                                    <td>
                                        <strong>{{ $blog->title }}</strong>
                                        @if($blog->is_featured)
                                            <span class="badge bg-warning">Featured</span>
                                        @endif
                                    </td>
                                    <td>{{ $blog->author ?? '-' }}</td>
                                    <td>{{ $blog->published_at ? $blog->published_at->format('M d, Y') : '-' }}</td>
                                    <td>{{ $blog->views }}</td>
                                    <td>
                                        @if($blog->status)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.restaurant.blogs.edit', $blog) }}" class="btn btn-sm btn-icon btn-primary">
                                            <i class="bx bx-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.restaurant.blogs.destroy', $blog) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
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
                                    <td colspan="6" class="text-center py-4">{{ __('No blogs found') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">{{ $blogs->links() }}</div>
            </div>
        </div>
    </div>
@endsection
