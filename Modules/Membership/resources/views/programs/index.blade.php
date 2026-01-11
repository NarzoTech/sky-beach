@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Loyalty Programs') }}</title>
@endsection
@section('content')
    <div class="card mb-5">
        <div class="card-header-tab card-header">
            <div class="card-header-title font-size-lg text-capitalize font-weight-normal">
                <h4 class="section_title">{{ __('Loyalty Programs') }}</h4>
            </div>
            <div class="btn-actions-pane-right actions-icon-btn">
                <a href="{{ route('membership.programs.create') }}" class="btn btn-primary">
                    <i class="fa fa-plus"></i> {{ __('Add Program') }}
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table mb-5">
                    <thead>
                        <tr>
                            <th>{{ __('SN') }}</th>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Earning Type') }}</th>
                            <th>{{ __('Earning Rate') }}</th>
                            <th>{{ __('Redemption Type') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($programs as $index => $program)
                            <tr>
                                <td>{{ $programs->firstItem() + $index }}</td>
                                <td>{{ $program->name }}</td>
                                <td>{{ ucfirst(str_replace('_', ' ', $program->earning_type)) }}</td>
                                <td>{{ $program->earning_rate }}</td>
                                <td>{{ ucfirst(str_replace('_', ' ', $program->redemption_type)) }}</td>
                                <td>
                                    @if ($program->is_active)
                                        <span class="badge bg-success">{{ __('Active') }}</span>
                                    @else
                                        <span class="badge bg-danger">{{ __('Inactive') }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-primary dropdown-toggle"
                                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            {{ __('Action') }}
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="{{ route('membership.programs.show', $program) }}">
                                                {{ __('View') }}
                                            </a>
                                            <a class="dropdown-item" href="{{ route('membership.programs.edit', $program) }}">
                                                {{ __('Edit') }}
                                            </a>
                                            <a class="dropdown-item" href="{{ route('membership.rules.index', ['program_id' => $program->id]) }}">
                                                {{ __('Manage Rules') }}
                                            </a>
                                            <form action="{{ route('membership.programs.destroy', $program) }}" method="POST"
                                                onsubmit="return confirm('Are you sure?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger">
                                                    {{ __('Delete') }}
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">{{ __('No programs found') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="float-right">
                {{ $programs->links() }}
            </div>
        </div>
    </div>
@endsection
