<div class="modal fade" id="categoryModal">
    <div class="modal-dialog">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">{{ __('Create Category') }}</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <!-- Modal body -->
            <div class="modal-body pt-0 pb-0">
                <form action="{{ route('admin.category.store') }}" method="post" id="categoryForm">
                    @csrf
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="name">{{ __('Name') }}<span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" id="name" required
                                    value="{{ old('name') }}">
                                @error('name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="slug">{{ __('Status') }}<span class="text-danger">*</span></label>
                                <select name="status" id="status" class="form-control">
                                    <option value="1">
                                        {{ __('Active') }}</option>
                                    <option value="0">
                                        {{ __('Inactive') }}</option>
                                </select>
                                @error('status')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="parent">{{ __('Parent Id') }}</label>
                                <select name="parent_id" id="parent" class="form-control select2"
                                    data-dropdown-parent="#categoryModal" data-control="select2">
                                    <option value="">{{ __('Select One') }}</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">
                                            {{ $category->name }}</option>
                                    @endforeach
                                </select>
                                @error('parent')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Modal footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">{{ __('Close') }}</button>
                <button type="submit" class="btn btn-primary" form="categoryForm"><i class="fa fa-save me-2"></i>
                    {{ __('Save') }}</button>

            </div>

        </div>
    </div>
</div>
