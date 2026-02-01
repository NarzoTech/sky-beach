@extends('admin.layouts.master')
@section('title', __('Rule Details') . ' - ' . $rule->name)
@section('content')
    <div class="card mb-5">
        <div class="card-header-tab card-header">
            <div class="card-header-title font-size-lg text-capitalize font-weight-normal">
                <h4 class="section_title">{{ __('Rule Details') }}: {{ $rule->name }}</h4>
            </div>
            <div class="btn-actions-pane-right actions-icon-btn">
                <a href="{{ route('membership.rules.edit', $rule) }}" class="btn btn-primary">
                    <i class="fa fa-edit"></i> {{ __('Edit') }}
                </a>
                <a href="{{ route('membership.rules.index', ['program_id' => $rule->loyalty_program_id]) }}" class="btn btn-secondary">
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
                            <td>{{ $rule->name }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Program') }}</th>
                            <td>{{ $rule->program->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Description') }}</th>
                            <td>{{ $rule->description ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Priority') }}</th>
                            <td>{{ $rule->priority }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Status') }}</th>
                            <td>
                                @if ($rule->is_active)
                                    <span class="badge bg-success">{{ __('Active') }}</span>
                                @else
                                    <span class="badge bg-danger">{{ __('Inactive') }}</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th>{{ __('Condition Type') }}</th>
                            <td>{{ ucfirst(str_replace('_', ' ', $rule->condition_type)) }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Action Type') }}</th>
                            <td>{{ ucfirst(str_replace('_', ' ', $rule->action_type)) }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Action Value') }}</th>
                            <td>{{ $rule->action_value }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Applies To') }}</th>
                            <td>{{ ucfirst(str_replace('_', ' ', $rule->applies_to)) }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Date Range') }}</th>
                            <td>
                                @if ($rule->start_date || $rule->end_date)
                                    {{ $rule->start_date ?? 'N/A' }} - {{ $rule->end_date ?? 'N/A' }}
                                @else
                                    {{ __('No date restrictions') }}
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
