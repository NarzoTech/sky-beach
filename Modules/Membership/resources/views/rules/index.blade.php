@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Loyalty Rules') }}</title>
@endsection
@section('content')
    <div class="card mb-3">
        <div class="card-body">
            <form action="" method="GET" class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="program_id">{{ __('Filter by Program') }}</label>
                        <select name="program_id" id="program_id" class="form-control">
                            <option value="">{{ __('All Programs') }}</option>
                            @foreach ($programs as $program)
                                <option value="{{ $program->id }}" {{ $selectedProgramId == $program->id ? 'selected' : '' }}>
                                    {{ $program->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">{{ __('Filter') }}</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card mb-5">
        <div class="card-header-tab card-header">
            <div class="card-header-title font-size-lg text-capitalize font-weight-normal">
                <h4 class="section_title">{{ __('Loyalty Rules') }}</h4>
            </div>
            <div class="btn-actions-pane-right actions-icon-btn">
                @if ($selectedProgramId)
                    <a href="{{ route('membership.rules.create', ['program_id' => $selectedProgramId]) }}" class="btn btn-primary">
                        <i class="fa fa-plus"></i> {{ __('Add Rule') }}
                    </a>
                @endif
                <a href="{{ route('membership.programs.index') }}" class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i> {{ __('Back to Programs') }}
                </a>
            </div>
        </div>
        <div class="card-body">
            @if (!$selectedProgramId)
                <div class="alert alert-info">{{ __('Please select a program to add rules') }}</div>
            @endif
            <div class="table-responsive">
                <table class="table mb-5">
                    <thead>
                        <tr>
                            <th>{{ __('Priority') }}</th>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Program') }}</th>
                            <th>{{ __('Condition') }}</th>
                            <th>{{ __('Action') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rules as $rule)
                            <tr>
                                <td>{{ $rule->priority }}</td>
                                <td>{{ $rule->name }}</td>
                                <td>{{ $rule->program->name ?? 'N/A' }}</td>
                                <td>{{ ucfirst(str_replace('_', ' ', $rule->condition_type)) }}</td>
                                <td>{{ ucfirst(str_replace('_', ' ', $rule->action_type)) }} ({{ $rule->action_value }})</td>
                                <td>
                                    @if ($rule->is_active)
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
                                            <a class="dropdown-item" href="{{ route('membership.rules.show', $rule) }}">
                                                {{ __('View') }}
                                            </a>
                                            <a class="dropdown-item" href="{{ route('membership.rules.edit', $rule) }}">
                                                {{ __('Edit') }}
                                            </a>
                                            <form action="{{ route('membership.rules.destroy', $rule) }}" method="POST"
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
                                <td colspan="7" class="text-center">{{ __('No rules found') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="float-right">
                {{ $rules->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
@endsection
