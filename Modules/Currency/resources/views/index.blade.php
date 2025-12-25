@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Currency List') }}</title>
@endsection
@section('content')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <div class="section-header-back">
                    <a href="{{ route('admin.settings') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
                </div>


            </div>

    </div>
    </div>
    <div class="section-body">
        <a href="{{ route('admin.currency.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i>
            {{ __('Add New') }}</a>
        <div class="row mt-4">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive table-invoice">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>{{ __('SN') }}</th>
                                        <th>{{ __('Currency') }}</th>
                                        <th>{{ __('Country Code') }}</th>
                                        <th>{{ __('Currency Code') }}</th>
                                        <th>{{ __('Currency Icon') }}</th>
                                        <th>{{ __('Currency Rate') }}</th>
                                        <th>{{ __('Default') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($currencies as $index => $currency)
                                        <tr>
                                            <td>{{ ++$index }}</td>
                                            <td>{{ $currency->currency_name }}</td>
                                            <td>{{ $currency->country_code }}</td>
                                            <td>{{ $currency->currency_code }}</td>
                                            <td>{{ $currency->currency_icon }}</td>
                                            <td>{{ $currency->currency_rate }}</td>

                                            <td>
                                                @if ($currency->is_default == 'yes')
                                                    <span class="badge badge-success">{{ __('Default') }}</span>
                                                @else
                                                    <span class="badge badge-danger">{{ __('No') }}</span>
                                                @endif
                                            </td>

                                            <td>
                                                @if ($currency->status == 'active')
                                                    <span class="badge badge-success">{{ __('Active') }}</span>
                                                @else
                                                    <span class="badge badge-danger">{{ __('Inactive') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.currency.edit', $currency->id) }}"
                                                    class="btn btn-primary btn-sm"><i class="fa fa-edit"
                                                        aria-hidden="true"></i></a>

                                                @if ($currency->id != 1)
                                                    <a href="javascript:;" class="btn btn-danger btn-sm"
                                                        onclick="deleteData({{ $currency->id }})" disabled><i
                                                            class="fa fa-trash" aria-hidden="true"></i></a>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <x-empty-table :name="__('Currency')" route="admin.currency.create" create="yes"
                                            :message="__('No data found!')" colspan="9"></x-empty-table>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </section>
    </div>

    <script>
        "use strict";

        function deleteData(id) {
            let url = "{{ route('admin.currency.destroy', ':id') }}"
            url = url.replace(':id', id);
            $("#deleteForm").attr("action", url);
            $('#deleteModal').modal('show');
        }
    </script>
@endsection
