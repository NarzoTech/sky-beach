@extends('admin.layouts.master')
@section('title', __('Edit Menu Add-on'))
@section('content')
    <div class="main-content">
        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between">
                                <h4 class="section_title">{{ __('Edit Menu Add-on') }}: {{ $addon->name }}</h4>
                                <div>
                                    <a href="{{ route('admin.menu-addon.index') }}" class="btn btn-primary">
                                        <i class="fa fa-arrow-left"></i> {{ __('Back') }}
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('admin.menu-addon.update', $addon->id) }}" method="post" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <div class="row">
                                        <div class="col-lg-8">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5>{{ __('Add-on Information') }}</h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-8">
                                                            <div class="form-group">
                                                                <label for="name">{{ __('Name') }}<span class="text-danger">*</span></label>
                                                                <input type="text" name="name" class="form-control" id="name" required value="{{ old('name', $addon->name) }}">
                                                                @error('name')
                                                                    <span class="text-danger">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="price">{{ __('Price') }}<span class="text-danger">*</span></label>
                                                                <input type="number" name="price" class="form-control" id="price" required value="{{ old('price', $addon->price) }}" step="0.01" min="0">
                                                                @error('price')
                                                                    <span class="text-danger">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="description">{{ __('Description') }}</label>
                                                                <textarea name="description" class="form-control" id="description" rows="3">{{ old('description', $addon->description) }}</textarea>
                                                                @error('description')
                                                                    <span class="text-danger">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Menu Items Using This Add-on -->
                                            @if ($addon->menuItems->count() > 0)
                                                <div class="card mt-3">
                                                    <div class="card-header">
                                                        <h5>{{ __('Used in Menu Items') }} ({{ $addon->menuItems->count() }})</h5>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="table-responsive">
                                                            <table class="table table-sm table-bordered">
                                                                <thead>
                                                                    <tr>
                                                                        <th>{{ __('Item') }}</th>
                                                                        <th>{{ __('Max Qty') }}</th>
                                                                        <th>{{ __('Required') }}</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach ($addon->menuItems as $item)
                                                                        <tr>
                                                                            <td>
                                                                                <a href="{{ route('admin.menu-item.edit', $item->id) }}">
                                                                                    {{ $item->name }}
                                                                                </a>
                                                                            </td>
                                                                            <td>{{ $item->pivot->max_quantity }}</td>
                                                                            <td>
                                                                                @if ($item->pivot->is_required)
                                                                                    <span class="badge bg-danger">{{ __('Yes') }}</span>
                                                                                @else
                                                                                    {{ __('No') }}
                                                                                @endif
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="col-lg-4">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5>{{ __('Image') }}</h5>
                                                </div>
                                                <div class="card-body">
                                                    @if ($addon->image)
                                                        <div class="mb-3">
                                                            <img src="{{ asset($addon->image) }}" alt="{{ $addon->name }}" style="max-width: 100%; max-height: 200px; border-radius: 5px;">
                                                        </div>
                                                    @endif
                                                    <div class="form-group">
                                                        <input type="file" name="image" class="form-control" id="image" accept="image/*">
                                                        @error('image')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                    <div id="image-preview" class="mt-2"></div>
                                                </div>
                                            </div>

                                            <div class="card mt-3">
                                                <div class="card-header">
                                                    <h5>{{ __('Status') }}</h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <label for="status">{{ __('Status') }}<span class="text-danger">*</span></label>
                                                        <select name="status" id="status" class="form-control">
                                                            <option value="1" {{ old('status', $addon->status) == 1 ? 'selected' : '' }}>{{ __('Active') }}</option>
                                                            <option value="0" {{ old('status', $addon->status) == 0 ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="card mt-3">
                                                <div class="card-body text-center">
                                                    <x-admin.save-button :text="__('Update Add-on')"></x-admin.save-button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('js')
    <script>
        (function($) {
            "use strict";
            $(document).ready(function() {
                // Image preview
                $('#image').on('change', function() {
                    var file = this.files[0];
                    if (file) {
                        var reader = new FileReader();
                        reader.onload = function(e) {
                            $('#image-preview').html('<img src="' + e.target.result + '" style="max-width: 100%; max-height: 200px; border-radius: 5px;">');
                        }
                        reader.readAsDataURL(file);
                    }
                });
            });
        })(jQuery);
    </script>
@endpush
