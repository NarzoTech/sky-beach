@extends('admin.layouts.master')

@section('title')
    {{ __('Contact Messages') }}
@endsection

@section('content')
    <div class="card">
        <div class="card-body pb-0">
            <form class="search_form" action="{{ route('admin.restaurant.contact-messages.index') }}" method="GET">
                <div class="row">
                    <div class="col-xxl-2 col-md-6 col-lg-4">
                        <div class="form-group">
                            <select name="status" class="form-control">
                                <option value="">{{ __('All Status') }}</option>
                                <option value="unread" {{ request('status') == 'unread' ? 'selected' : '' }}>{{ __('Unread') }}</option>
                                <option value="read" {{ request('status') == 'read' ? 'selected' : '' }}>{{ __('Read') }}</option>
                                <option value="replied" {{ request('status') == 'replied' ? 'selected' : '' }}>{{ __('Replied') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-xxl-2 col-md-6 col-lg-4">
                        <div class="form-group">
                            <button type="button" class="btn bg-danger form-reset">{{ __('Reset') }}</button>
                            <button type="submit" class="btn bg-label-primary">{{ __('Search') }}</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card mt-5">
        <div class="card-header">
            <h4 class="section_title">{{ __('Contact Messages') }}</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Email') }}</th>
                            <th>{{ __('Subject') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Date') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($messages as $message)
                            <tr class="{{ $message->status === 'unread' ? 'table-warning' : '' }}">
                                <td>
                                    <strong>{{ $message->name }}</strong>
                                    @if($message->newsletter)
                                        <br><small class="text-muted"><i class="bx bx-envelope"></i> {{ __('Subscribed') }}</small>
                                    @endif
                                </td>
                                <td><a href="mailto:{{ $message->email }}">{{ $message->email }}</a></td>
                                <td>{{ Str::limit($message->subject, 30) }}</td>
                                <td>
                                    @if($message->status === 'unread')
                                        <span class="badge bg-warning">{{ __('Unread') }}</span>
                                    @elseif($message->status === 'read')
                                        <span class="badge bg-info">{{ __('Read') }}</span>
                                    @else
                                        <span class="badge bg-success">{{ __('Replied') }}</span>
                                    @endif
                                </td>
                                <td>{{ $message->created_at->format('M d, Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('admin.restaurant.contact-messages.show', $message) }}" class="btn btn-sm btn-icon btn-info">
                                        <i class="bx bx-show"></i>
                                    </a>
                                    <form action="{{ route('admin.restaurant.contact-messages.destroy', $message) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
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
                                <td colspan="6" class="text-center py-4">{{ __('No contact messages found') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">{{ $messages->appends(request()->query())->links() }}</div>
        </div>
    </div>
@endsection
