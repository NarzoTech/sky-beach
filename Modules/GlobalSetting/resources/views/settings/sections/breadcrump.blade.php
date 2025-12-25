<div class="tab-pane fade" id="breadcrump_img_tab" role="tabpanel">
    <form action="{{ route('admin.update-breadcrumb') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="">{{ __('Existing Breadcrumb Image') }}</label>
            <div>
                <img class="w_200" src="{{ asset($setting->breadcrumb_image) }}" alt="" id="breadcrumpPreview">
            </div>
        </div>

        <div class="form-group">
            <label for="">{{ __('New Breadcrumb Image') }}</label>
            <input id="breadcrumpInput" type="file" name="breadcrumb_image" class="form-control-file">
        </div>


        <button class="btn btn-primary">{{ __('Update') }}</button>
    </form>
</div>
