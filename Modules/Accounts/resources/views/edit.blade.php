@extends('admin.layouts.master')
@section('title', __('Edit Account'))


@section('content')
    <div class="mt-4 row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h4>{{ __('Edit Account') }}</h4>
                    <div>
                        <a href="{{ route('admin.accounts.index') }}" class="btn btn-primary"><i
                                class="fas fa-arrow-left"></i>{{ __('Back') }}</a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.accounts.update', $account->id) }}" method="post" id="accountForm">
                        @csrf
                        @method('PUT')
                        <div class="row">

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="account_type">{{ __('Account Type') }}<span
                                            class="text-danger">*</span></label>
                                    <select name="account_type" id="account_type" class="form-control">
                                        <option value="">{{ __('Select Account Type') }}</option>
                                        @foreach (accountList() as $key => $list)
                                            <option value="{{ $key }}"
                                                {{ old('account_type', $account->account_type) == $key ? 'selected' : '' }}>
                                                {{ $list }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            {{-- for mobile banking --}}

                            <div
                                class="col-12 row mobile_section {{ $account->account_type == 'mobile_banking' ? '' : 'd-none' }}">
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label for="mobile_bank_name">{{ __('Mobile Bank Name') }}<span
                                                class="text-danger">*</span></label>
                                        <select name="mobile_bank_name" id="mobile_bank_name" class="form-control" disabled>
                                            <option value="">{{ __('Select Mobile Bank Name') }}</option>
                                            @foreach (mobileBankList() as $key => $list)
                                                <option value="{{ $key }}"
                                                    {{ old('mobile_bank_name', $account->mobile_bank_name) == $key ? 'selected' : '' }}>
                                                    {{ $list }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label for="mobile_number">{{ __('Mobile Number') }}<span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="mobile_number" id="mobile_number" class="form-control"
                                            placeholder="{{ __('Mobile Number') }}" disabled
                                            value="{{ $account->mobile_number }}">
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label for="service_charge">{{ __('Service Charge') }}(%)</label>
                                        <input type="text" name="service_charge" id="service_charge" class="form-control"
                                            placeholder="{{ __('Service Charge') }}" disabled
                                            value="{{ $account->service_charge }}">
                                    </div>
                                </div>
                            </div>

                            {{-- for card --}}

                            <div class="col-12 row bank-card {{ $account->account_type == 'card' ? '' : 'd-none' }}">
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label for="card_type">{{ __('Card Type') }}<span
                                                class="text-danger">*</span></label>
                                        <select name="card_type" id="card_type" class="form-control" disabled>
                                            <option value="">{{ __('Select Mobile Bank Name') }}</option>
                                            @foreach (cardTypeList() as $key => $list)
                                                <option value="{{ $key }}"
                                                    {{ $key == $account->card_type ? 'selected' : '' }}>
                                                    {{ $list }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label for="bank_id">{{ __('Bank Name') }}<span
                                                class="text-danger">*</span></label>
                                        <select name="bank_id" id="bank_id" class="form-control select2" disabled>
                                            <option value="">{{ __('Select Bank') }}</option>
                                            @foreach ($accounts as $bank)
                                                <option value="{{ $bank->id }}"
                                                    {{ $bank->id == $account->bank_id ? 'selected' : '' }}>
                                                    {{ $bank->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label for="card_holder_name">{{ __('Card Holder Name') }}<span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="card_holder_name" id="card_holder_name"
                                            class="form-control" placeholder="{{ __('Card Holder Name') }}" disabled
                                            value="{{ $account->card_holder_name }}">
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label for="card_number">{{ __('Card Number') }}<span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="card_number" id="card_number" class="form-control"
                                            placeholder="{{ __('Card Number') }}" disabled
                                            value="{{ $account->card_number }}">
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label for="service_charge">{{ __('Service Charge') }}(%)</label>
                                        <input type="text" name="service_charge" id="service_charge" class="form-control"
                                            placeholder="{{ __('Service Charge') }}" disabled
                                            value="{{ $account->service_charge }}">
                                    </div>
                                </div>
                            </div>

                            {{-- for bank --}}

                            <div class="col-12 row bank {{ $account->account_type == 'bank' ? '' : 'd-none' }}">
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label for="bank_id">{{ __('Bank Name') }}<span
                                                class="text-danger">*</span></label>
                                        <select name="bank_id" id="bank_id" class="form-control select2" disabled>
                                            <option value="">{{ __('Select Bank') }}</option>
                                            @foreach ($accounts as $bank)
                                                <option value="{{ $bank->id }}"
                                                    {{ $bank->id == $account->bank_id ? 'selected' : '' }}>
                                                    {{ $bank->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label for="bank_account_type">{{ __('Bank Account Type') }}<span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="bank_account_type" id="bank_account_type"
                                            class="form-control" placeholder="{{ __('Bank Account Type') }}"disabled
                                            value="{{ $account->bank_account_type }}">
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label for="bank_account_name">{{ __('Bank Account Name') }}<span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="bank_account_name" id="bank_account_name"
                                            class="form-control" placeholder="{{ __('Bank Account Name') }}" disabled
                                            value="{{ $account->bank_account_name }}">
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label for="bank_account_number">{{ __('Bank Account Number') }}<span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="bank_account_number" id="bank_account_number"
                                            class="form-control" placeholder="{{ __('Bank Account Number') }}" disabled
                                            value="{{ $account->bank_account_number }}">
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label for="bank_account_branch">{{ __('Bank Account Branch') }}<span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="bank_account_branch" id="bank_account_branch"
                                            class="form-control" placeholder="{{ __('Bank Account Branch') }}" disabled
                                            value="{{ $account->bank_account_branch }}">
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label for="service_charge">{{ __('Service Charge') }}(%)</label>
                                        <input type="text" name="service_charge" id="service_charge"
                                            class="form-control" placeholder="{{ __('Service Charge') }}" disabled
                                            value="{{ $account->service_charge }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="text-center offset-md-2 col-md-8">
                                <x-admin.save-button :text="__('Save')">
                                </x-admin.save-button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('js')
    <script>
        $(document).ready(function() {
            handleAccount('{{ $account->account_type }}');

            $('#account_type').on('change', function() {
                var account_type = $(this).val();
                handleAccount(account_type);
            });
        });

        function removeDisabled(selector) {
            // remove all disabled attribute 

            $('.mobile_section').find('input, select').each(function() {
                $(this).attr('disabled', true);
            });

            $('.bank-card').find('input, select').each(function() {
                $(this).attr('disabled', true);
            });

            $('.bank').find('input, select').each(function() {
                $(this).attr('disabled', true);
            });

            // remove disabled attribute in side the selector
            $(selector).find('input, select').each(function() {
                $(this).removeAttr('disabled');

                // destroy nice select
                $(this).niceSelect('destroy');

                // check if tag is select
                if ($(this).is('select')) {
                    $(this).niceSelect();
                }
            });
        }

        function handleAccount(account_type) {
            if (account_type == 'mobile_banking') {
                $('.mobile_section').removeClass('d-none');
                $('.bank-card').addClass('d-none');
                $('.bank').addClass('d-none');

                // disabled others field
                removeDisabled('.mobile_section');

            } else if (account_type == 'card') {
                $('.bank-card').removeClass('d-none');
                $('.mobile_section').addClass('d-none');
                $('.bank').addClass('d-none');

                removeDisabled('.bank-card');
            } else if (account_type == 'bank') {
                $('.bank').removeClass('d-none');
                $('.mobile_section').addClass('d-none');
                $('.bank-card').addClass('d-none');

                removeDisabled('.bank');
            } else {
                $('.mobile_section').addClass('d-none');
                $('.bank-card').addClass('d-none');
                $('.bank').addClass('d-none');
            }
        }
    </script>
@endpush
