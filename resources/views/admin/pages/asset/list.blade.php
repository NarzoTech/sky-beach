@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Asset List') }}</title>
@endsection


@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body pb-0">
                    <form class="search_form" action="" method="GET">
                        <div class="row">
                            <div class="col-xxl-4 col-md-6">
                                <div class="form-group search-wrapper">
                                    <input type="text" name="keyword" value="{{ request()->get('keyword') }}"
                                        class="form-control" placeholder="Search..." autocomplete="off">
                                    <button type="submit">
                                        <i class='bx bx-search'></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-xxl-2 col-md-6">
                                <div class="form-group">
                                    <select name="order_type" id="order_type" class="form-control">
                                        <option value="id" {{ request('order_type') == 'id' ? 'selected' : '' }}>
                                            {{ __('Serial') }}</option>
                                        <option value="name" {{ request('order_type') == 'name' ? 'selected' : '' }}>
                                            {{ __('Name') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xxl-2 col-md-6">
                                <div class="form-group">
                                    <select name="order_by" id="order_by" class="form-control">
                                        <option value="">{{ __('Order By') }}</option>
                                        <option value="asc" {{ request('order_by') == 'asc' ? 'selected' : '' }}>
                                            {{ __('ASC') }}
                                        </option>
                                        <option value="desc" {{ request('order_by') == 'desc' ? 'selected' : '' }}>
                                            {{ __('DESC') }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xxl-2 col-md-6">
                                <div class="form-group">
                                    <select name="par-page" id="par-page" class="form-control">
                                        <option value="">{{ __('Per Page') }}</option>
                                        <option value="10" {{ '10' == request('par-page') ? 'selected' : '' }}>
                                            {{ __('10') }}
                                        </option>
                                        <option value="50" {{ '50' == request('par-page') ? 'selected' : '' }}>
                                            {{ __('50') }}
                                        </option>
                                        <option value="100" {{ '100' == request('par-page') ? 'selected' : '' }}>
                                            {{ __('100') }}
                                        </option>
                                        <option value="all" {{ 'all' == request('par-page') ? 'selected' : '' }}>
                                            {{ __('All') }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xxl-2 col-md-6">
                                <div class="form-group">
                                    <button type="button" class="btn bg-danger form-reset">Reset</button>
                                    <button type="submit" class="btn bg-label-primary">Search</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card mt-5">
                <div class="card-header">
                    <div class="card-header-title font-size-lg text-capitalize font-weight-normal">
                        <h4 class="section_title"> {{ __('Asset List') }}</h4>
                    </div>
                    <div class="btn-actions-pane-right actions-icon-btn">
                        @adminCan('asset.create')
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#addAssetType"
                                class="btn btn-primary"><i class="fa fa-plus"></i>
                                {{ __('Add Asset') }}</a>
                        @endadminCan
                        <button type="button" class="btn bg-label-success export"><i class="fa fa-file-excel"></i>
                            Excel</button>
                        <button type="button" class="btn bg-label-warning export-pdf"><i class="fa fa-file-pdf"></i>
                            PDF</button>

                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive list_table">
                        <table style="width: 100%;" class="table">
                            <thead>
                                <tr>
                                    <th title="Sl">{{ __('Sl') }}</th>
                                    <th title="Date">{{ __('Name') }}</th>
                                    <th title="Date">{{ __('Date') }}</th>
                                    <th title="Category">{{ __('Type') }}</th>
                                    <th title="Pay By">{{ __('Pay By') }}</th>
                                    <th title="Note">{{ __('Note') }}</th>
                                    <th title="Amount">{{ __('Amount') }}</th>
                                    <th title="Action">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($lists as $index => $type)
                                    <tr>
                                        <td>{{ $loop->first + $index }}</td>
                                        <td>{{ $type->name }}</td>
                                        <td>
                                            {{ formatDate($type->date) }}
                                        </td>
                                        <td>{{ $type->type->name }}</td>
                                        <td>{{ $type->account->account_type }}</td>
                                        <td>
                                            {{ $type->note }}
                                        </td>
                                        <td>
                                            {{ currency($type->amount) }}
                                        </td>
                                        <td>
                                            @if (checkAdminHasPermission('asset.edit') || checkAdminHasPermission('asset.delete'))
                                                <div class="btn-group" role="group">
                                                    <button id="btnGroupDrop{{ $type->id }}" type="button"
                                                        class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown"
                                                        aria-haspopup="true"
                                                        aria-expanded="false">{{ __('Action') }}</button>
                                                    <div class="dropdown-menu"
                                                        aria-labelledby="btnGroupDrop{{ $type->id }}">
                                                        @adminCan('asset.edit')
                                                            <a class="dropdown-item" href="javascript:;" data-bs-toggle="modal"
                                                                data-bs-target="#editType{{ $type->id }}">{{ __('Edit') }}</a>
                                                        @endadminCan
                                                        @adminCan('asset.delete')
                                                            <a href="javascript:;" class="dropdown-item"
                                                                onclick="deleteData({{ $type->id }})">{{ __('Delete') }}</a>
                                                        @endadminCan
                                                    </div>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <x-empty-table :name="__('Asset')" route="" create="no" :message="__('No data found!')"
                                        colspan="8"></x-empty-table>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if (request()->get('par-page') !== 'all')
                        <div class="float-right">
                            {{ $lists->onEachSide(0)->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- add Asset --}}
    <div class="modal fade" id="addAssetType">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">{{ __('Add Asset') }}</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <!-- Modal body -->
                <div class="modal-body py-0">
                    <form action="{{ route('admin.assets.store') }}" method="POST" id="add-asset-form">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">{{ __('Name') }}<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">{{ __('Date') }}</label>
                                    <input type="text" name="date" value="{{ formatDate(now()) }}"
                                        class="form-control datepicker" required autocomplete="off">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="">{{ __('Asset Category') }}</label>
                                    <select name="type_id" class="form-control" required>
                                        <option value="">{{ __('select') }}</option>
                                        @foreach ($types as $type)
                                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-12" style="display: none;">
                                <div class="form-group">
                                    <label for="">{{ __('Branch') }}</label>
                                    <select name="branch_id" class="form-control" id="branch_id">
                                    </select>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <div>
                                        <label for="" class="mt-2">{{ __('Payment Type') }}</label>
                                    </div>
                                    <div>
                                        <select name="payment_type" id="" class="form-control">
                                            <option value="">{{ __('Payment Type') }}</option>
                                            @foreach (accountList() as $key => $list)
                                                <option value="{{ $key }}">
                                                    {{ $list }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="accounts">

                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="">{{ __('Amount') }}</label>
                                    <input type="number" name="amount" class="form-control" required step="0.01">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="">{{ __('Note') }}</label>
                                    <textarea name="note" rows="5" class="form-control"></textarea>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-primary" form="add-asset-form">{{ __('Save') }}</button>
                </div>

            </div>
        </div>
    </div>

    {{-- edit Asset --}}
    @foreach ($lists as $index => $type)
        <div class="modal fade" id="editType{{ $type->id }}">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title">{{ __('Edit Asset') }}</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body py-0">
                        <form action="{{ route('admin.assets.update', $type->id) }}" method="POST"
                            id="edit-type-form{{ $type->id }}">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">{{ __('Name') }}<span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="name" name="name"
                                            value="{{ $type->name }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">{{ __('Date') }}</label>
                                        <input type="text" name="date" value="{{ formatDate($type->date) }}"
                                            class="form-control datepicker" required autocomplete="off">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="">{{ __('Asset Category') }}</label>
                                        <select name="type_id" class="form-control" required>
                                            <option value="">{{ __('select') }}</option>
                                            @foreach ($types as $cat)
                                                <option value="{{ $cat->id }}"
                                                    {{ $cat->id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="name">{{ __('Payment Type') }}<span
                                                class="text-danger">*</span></label>
                                        <select name="payment_type" id="" class="form-control">
                                            <option value="">{{ __('Payment Type') }}</option>
                                            @foreach (accountList() as $key => $list)
                                                <option value="{{ $key }}"
                                                    {{ $key == $type->payment_type ? 'selected' : '' }}>
                                                    {{ $list }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group accounts">
                                        <input type="hidden" name="account_id" value="{{ $type->account_id }}">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="">{{ __('Amount') }}</label>
                                        <input type="number" name="amount" class="form-control" required
                                            value="{{ $type->amount }}" step="0.01">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="">{{ __('Note') }}</label>
                                        <textarea name="note" rows="3" class="form-control">{{ $type->note }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger"
                            data-bs-dismiss="modal">{{ __('Close') }}</button>
                        <button type="submit" class="btn btn-primary"
                            form="edit-type-form{{ $type->id }}">{{ __('Update') }}</button>
                    </div>

                </div>
            </div>
        </div>
    @endforeach
@endsection
@push('js')
    <script>
        $(document).ready(function() {

            let accounts = @json($accounts);
            $('select[name="payment_type"]').on('change', function() {
                const paymentType = $(this).val();
                let html = `<label for="account_id">{{ __('Select Account') }}<span class="text-danger">*</span></label>
                    <select name="account_id" id="" class="form-control form-group">`;
                const filterAccount = accounts.filter(account => account.account_type === paymentType);
                html = accountsType(filterAccount, html, paymentType);
                $('.accounts').html(html);

                if ($(this).val() == 'cash' || $(this).val() == 'advance') {
                    const cash =
                        `<input type="hidden" name="account_id" class="form-control" value="${$(this).val()}" readonly>`;
                    $('.accounts').html(cash);
                }
            });
        });

        function deleteData(id) {
            let url = "{{ route('admin.assets.destroy', ':id') }}"
            url = url.replace(':id', id);
            $("#deleteForm").attr("action", url);
            $('#deleteModal').modal('show');
        }
    </script>
@endpush
