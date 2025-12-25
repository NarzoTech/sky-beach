<div class="tab-pane fade active show" id="general_tab" role="tabpanel">
    <form action="{{ route('admin.update-general-setting') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="">{{ __('App Name') }}</label>
            <input type="text" name="app_name" class="form-control" value="{{ $setting->app_name }}">
        </div>

        <div class="form-group">
            <label for="">{{ __('Timezone') }}</label>
            <select name="timezone" id="" class="form-control select2">
                @foreach ($all_timezones as $timezone)
                    <option value="{{ $timezone->name }}" @selected($setting->timezone == $timezone->name)>
                        {{ $timezone->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="is_queable">{{ __('Send Mails In Queue') }}</label>
            <select name="is_queable" id="is_queable" class="form-control">
                <option {{ $setting->is_queable == 'active' ? 'selected' : '' }} value="active">{{ __('Enable') }}
                </option>
                <option {{ $setting->is_queable == 'inactive' ? 'selected' : '' }} value="inactive">
                    {{ __('Disable') }}
                </option>
            </select>
            @if ($setting->is_queable == 'active')
                <div class="pt-1 text-info"><span class="text-success ">{{ __('Copy and Run This Command') }}:
                    </span>
                    <strong id="copyCronText" onclick="copyText()" title="{{ __('Click to copy') }}"
                        onmouseover="this.style.cursor='pointer'">php artisan schedule:run >>
                        /dev/null
                        2>&1</strong>
                </div>
                <div class="pt-1 text-warning">
                    <b>{{ __('If enabled, you must setup cron job in your server. otherwise it will not work and no mail will
                                        be sent') }}</b>
                </div>
            @endif
        </div>

        <button class="btn btn-primary" type="submit">{{ __('Update') }}</button>

    </form>
</div>
