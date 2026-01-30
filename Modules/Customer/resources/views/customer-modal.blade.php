<div class="modal fade add_customer_modal" id="addCustomer">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header card-header">
                <h4 class="section_title">{{ __('Add Customer') }}</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <!-- Modal body -->
            <div class="modal-body pt-0 pb-0">
                <form action="{{ route('admin.customers.store') }}" method="POST" id="add-customer-form">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">{{ __('Customer Name') }}<b class="text-danger">*</b></label>
                                <input type="text" class="form-control" id="name" name="name">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="group_id">{{ __('Customer Group') }}</label>
                                <select name="group_id" id="group_id" class="form-control">
                                    <option value="">{{ __('Select Group') }}</option>
                                    @foreach ($groups as $group)
                                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="due">{{ __('Initial Due') }}</label>
                                <input type="number" step="0.01" class="form-control" id="due" name="due" placeholder="0.00">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="initial_advance">{{ __('Initial Advance') }}</label>
                                <input type="number" step="0.01" class="form-control" id="initial_advance" name="initial_advance" placeholder="0.00">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="phone">{{ __('Phone') }}</label>
                                <input type="text" class="form-control" id="phone" name="phone">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email">{{ __('Email') }}</label>
                                <input type="email" class="form-control" id="email" name="email">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="area_id">{{ __('Area') }}</label>
                                <select name="area_id" id="area_id" class="form-control">
                                    <option value="">{{ __('Select Area') }}</option>
                                    @foreach ($areaList as $list)
                                        <option value="{{ $list->id }}">{{ $list->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>


                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="plate_number">{{ __('Plate Number') }}</label>
                                <input type="text" class="form-control" id="plate_number" name="plate_number">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="membership">{{ __('Membership') }}</label>
                                <input type="text" class="form-control" id="membership" name="membership">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="date">{{ __('Date') }}</label>
                                <input type="text" class="form-control datepicker" id="date" name="date"
                                    autocomplete="off">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status">{{ __('Status') }}</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="1">{{ __('Active') }}</option>
                                    <option value="0">{{ __('Inactive') }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-0">
                                <div class="guest_customer_check">
                                    <label class="switch switch-square">
                                        <input type="checkbox" name="guest" class="switch-input" />
                                        <span class="switch-toggle-slider">
                                            <span class="switch-on"><i class="bx bx-check"></i></span>
                                            <span class="switch-off"><i class="bx bx-x"></i></span>
                                        </span>
                                        <span class="switch-label">{{ __('Guest Customer') }}</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="address">{{ __('Address') }}</label>
                                <textarea name="address" id="address" class="form-control" rows="4"></textarea>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Modal footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary" form="add-customer-form">Save</button>
            </div>

        </div>
    </div>
</div>
