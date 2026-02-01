@extends('admin.layouts.master')

@section('title')
    {{ __('Restaurant Menu Items') }}
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold">{{ __('Restaurant Menu Items') }}</h4>
            <a href="{{ route('admin.restaurant.menu-items.create') }}" class="btn btn-primary">
                <i class="bx bx-plus"></i> {{ __('Add Menu Item') }}
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
                                <th>{{ __('Category') }}</th>
                                <th>{{ __('Price') }}</th>
                                <th>{{ __('POS') }}</th>
                                <th>{{ __('Website') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($menuItems as $item)
                                <tr>
                                    <td>
                                        @if($item->image)
                                            <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}" 
                                                 style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">
                                        @else
                                            <div style="width: 50px; height: 50px; background: #f0f0f0; border-radius: 5px;"></div>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ $item->name }}</strong>
                                        @if($item->is_featured)
                                            <span class="badge bg-warning">Featured</span>
                                        @endif
                                        @if($item->is_new)
                                            <span class="badge bg-success">New</span>
                                        @endif
                                        @if($item->is_popular)
                                            <span class="badge bg-info">Popular</span>
                                        @endif
                                    </td>
                                    <td>{{ $item->category ?? '-' }}</td>
                                    <td>
                                        @if($item->discount_price)
                                            <del class="text-muted">{{ currency($item->price) }}</del>
                                            <strong class="text-success">{{ currency($item->discount_price) }}</strong>
                                        @else
                                            {{ currency($item->price) }}
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->available_in_pos)
                                            <span class="badge bg-success">Yes</span>
                                        @else
                                            <span class="badge bg-secondary">No</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->available_in_website)
                                            <span class="badge bg-success">Yes</span>
                                        @else
                                            <span class="badge bg-secondary">No</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->status)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button type="button" class="btn btn-sm btn-icon dropdown-toggle hide-arrow" 
                                                    data-bs-toggle="dropdown">
                                                <i class="bx bx-dots-vertical-rounded"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item" href="{{ route('admin.restaurant.menu-items.edit', $item) }}">
                                                    <i class="bx bx-edit me-1"></i> {{ __('Edit') }}
                                                </a>
                                                <form action="{{ route('admin.restaurant.menu-items.destroy', $item) }}" 
                                                      method="POST" onsubmit="return confirm('Are you sure?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="bx bx-trash me-1"></i> {{ __('Delete') }}
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        {{ __('No menu items found') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $menuItems->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
