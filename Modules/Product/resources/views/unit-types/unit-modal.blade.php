<div class="modal fade" id="unitModal">
    <div class="modal-dialog">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">{{ __('Create Unit') }}</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <!-- Modal body -->
            <div class="modal-body pt-0 pb-0">
                <form action="javascript:;" method="post" enctype="multipart/form-data" id="unitForm">
                    @csrf
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label>{{ __('Name') }} <span class="text-danger">*</span></label>
                                <input type="text" id="unitName" class="form-control" name="name" required>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label>{{ __('Short Name') }} <span class="text-danger">*</span></label>
                                <input type="text" id="ShortName" class="form-control" name="ShortName" required>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label>{{ __('Base Unit') }}</label>
                                <select name="base_unit" id="base_unit" class="form-control">
                                    <option value="">{{ __('Select Base Unit') }}</option>
                                    @foreach ($parentUnits as $parentUnit)
                                        <option value="{{ $parentUnit->id }}">{{ $parentUnit->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-12 operator d-none">
                            <div class="form-group">
                                <label>{{ __('Operator') }}</label>
                                <select name="operator" id="operator" class="form-control">
                                    <option value="*">{{ __('Multiply') }} (*)</option>
                                    <option value="/">{{ __('Divide') }} (/)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 operator_value d-none">
                            <div class="form-group">
                                <label>{{ __('Operator Value') }} <span class="text-danger">*</span></label>
                                <input type="text" id="operator_value" class="form-control" name="operator_value"
                                    value="1">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label>{{ __('Status') }} </label>
                                <div class="d-flex flex-wrap gap-5 border rounded py-2 px-4">
                                    <div class="d-flex flex-wrap gap-2 align-items-center">
                                        <input id="active" type="radio" name='status' value="1" checked />
                                        <label for="active" class="mb-0">{{ __('Active') }} </label>
                                    </div>
                                    <div class="d-flex flex-wrap gap-2 align-items-center">
                                        <input id="inactive" type="radio" name='status' value="0" />
                                        <label for="inactive" class="mb-0">{{ __('Inactive') }} </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Modal footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">{{ __('Close') }}</button>
                <button type="submit" class="btn btn-primary" form="unitForm"><i class="fa fa-save me-2"></i>
                    {{ __('Save') }}</button>

            </div>

        </div>
    </div>
</div>
