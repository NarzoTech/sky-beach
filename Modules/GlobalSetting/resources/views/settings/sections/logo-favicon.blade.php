<div class="tab-pane fade" id="logo_favicon_tab" role="tabpanel">
    <form action="{{ route('admin.update-logo-favicon') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="">{{ __('Existing Logo') }}</label>
            <div>
                <img src="{{ !empty($setting->logo) ? asset($setting->logo) : '' }}" id="logoPreview" width="200px">
            </div>
        </div>
        <div class="form-group">
            <label for="">{{ __('New Logo') }}</label>
            <input id="logoInput" type="file" name="logo" class="form-control-file">
        </div>

        <div class="form-group">
            <label for="">{{ __('Existing Favicon') }}</label>
            <div>
                <img src="{{ asset($setting->favicon) }}" alt="" width="50px" id="favPreview">
            </div>
        </div>

        <div class="form-group">
            <label for="">{{ __('New Favicon') }}</label>
            <input id="favInput" type="file" name="favicon" class="form-control-file">
        </div>

        <button class="btn btn-primary">{{ __('Update') }}</button>
    </form>
</div>
