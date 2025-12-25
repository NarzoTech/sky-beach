@extends('admin.layouts.master')

@section('title')
    {{ __('CMS Pages') }}
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold">{{ __('CMS Pages') }}</h4>
            <a href="{{ route('admin.restaurant.cms-pages.create') }}" class="btn btn-primary">
                <i class="bx bx-plus"></i> {{ __('Add Page') }}
            </a>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>{{ __('Title') }}</th>
                                <th>{{ __('Slug') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Updated') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pages as $page)
                                <tr>
                                    <td><strong>{{ $page->title }}</strong></td>
                                    <td><code>{{ $page->slug }}</code></td>
                                    <td>
                                        @if($page->status)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>{{ $page->updated_at->format('M d, Y') }}</td>
                                    <td>
                                        <a href="{{ route('admin.restaurant.cms-pages.edit', $page) }}" class="btn btn-sm btn-icon btn-primary">
                                            <i class="bx bx-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.restaurant.cms-pages.destroy', $page) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
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
                                    <td colspan="5" class="text-center py-4">{{ __('No pages found') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">{{ $pages->links() }}</div>
            </div>
        </div>
    </div>
@endsection
