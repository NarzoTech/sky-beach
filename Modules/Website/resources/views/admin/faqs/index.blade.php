@extends('admin.layouts.master')

@section('title')
    {{ __('FAQs') }}
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold">{{ __('FAQs') }}</h4>
            <a href="{{ route('admin.restaurant.faqs.create') }}" class="btn btn-primary">
                <i class="bx bx-plus"></i> {{ __('Add FAQ') }}
            </a>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>{{ __('Question') }}</th>
                                <th>{{ __('Category') }}</th>
                                <th>{{ __('Order') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($faqs as $faq)
                                <tr>
                                    <td><strong>{{ $faq->question }}</strong></td>
                                    <td>{{ $faq->category ?? '-' }}</td>
                                    <td>{{ $faq->order }}</td>
                                    <td>
                                        @if($faq->status)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.restaurant.faqs.edit', $faq) }}" class="btn btn-sm btn-icon btn-primary">
                                            <i class="bx bx-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.restaurant.faqs.destroy', $faq) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
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
                                    <td colspan="5" class="text-center py-4">{{ __('No FAQs found') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">{{ $faqs->links() }}</div>
            </div>
        </div>
    </div>
@endsection
