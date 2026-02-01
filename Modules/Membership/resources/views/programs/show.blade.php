@extends('admin.layouts.master')
@section('title', __('Program Details') . ' - ' . $program->name)
@section('content')
    <div class="card mb-5">
        <div class="card-header-tab card-header">
            <div class="card-header-title font-size-lg text-capitalize font-weight-normal">
                <h4 class="section_title">{{ __('Program Details') }}: {{ $program->name }}</h4>
            </div>
            <div class="btn-actions-pane-right actions-icon-btn">
                <a href="{{ route('membership.programs.edit', $program) }}" class="btn btn-primary">
                    <i class="fa fa-edit"></i> {{ __('Edit') }}
                </a>
                <a href="{{ route('membership.rules.index', ['program_id' => $program->id]) }}" class="btn btn-info">
                    <i class="fa fa-list"></i> {{ __('Manage Rules') }}
                </a>
                <a href="{{ route('membership.programs.index') }}" class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i> {{ __('Back') }}
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th>{{ __('Name') }}</th>
                            <td>{{ $program->name }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Description') }}</th>
                            <td>{{ $program->description ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Status') }}</th>
                            <td>
                                @if ($program->is_active)
                                    <span class="badge bg-success">{{ __('Active') }}</span>
                                @else
                                    <span class="badge bg-danger">{{ __('Inactive') }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>{{ __('Created By') }}</th>
                            <td>{{ $program->createdBy->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Created At') }}</th>
                            <td>{{ $program->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th>{{ __('Earning Type') }}</th>
                            <td>{{ ucfirst(str_replace('_', ' ', $program->earning_type)) }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Earning Rate') }}</th>
                            <td>{{ $program->earning_rate }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Min Transaction Amount') }}</th>
                            <td>{{ $program->min_transaction_amount ?? 0 }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Redemption Type') }}</th>
                            <td>{{ ucfirst(str_replace('_', ' ', $program->redemption_type)) }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Points Per Unit') }}</th>
                            <td>{{ $program->points_per_unit }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            @if ($program->rules->count() > 0)
                <h5 class="mt-4">{{ __('Associated Rules') }}</h5>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Condition Type') }}</th>
                                <th>{{ __('Action Type') }}</th>
                                <th>{{ __('Status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($program->rules as $rule)
                                <tr>
                                    <td>{{ $rule->name }}</td>
                                    <td>{{ ucfirst(str_replace('_', ' ', $rule->condition_type)) }}</td>
                                    <td>{{ ucfirst(str_replace('_', ' ', $rule->action_type)) }}</td>
                                    <td>
                                        @if ($rule->is_active)
                                            <span class="badge bg-success">{{ __('Active') }}</span>
                                        @else
                                            <span class="badge bg-danger">{{ __('Inactive') }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection
