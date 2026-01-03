@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Edit Table') }}</title>
@endsection
@section('content')
    <div class="main-content">
        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between">
                                <h4 class="section_title">{{ __('Edit Table') }}: {{ $table->name }}</h4>
                                <div>
                                    <a href="{{ route('admin.tables.index') }}" class="btn btn-primary">
                                        <i class="fa fa-arrow-left"></i> {{ __('Back') }}
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('admin.tables.update', $table->id) }}" method="post">
                                    @csrf
                                    @method('PUT')
                                    <div class="row">
                                        <div class="col-lg-8">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5>{{ __('Table Information') }}</h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="name">{{ __('Table Name') }}<span class="text-danger">*</span></label>
                                                                <input type="text" name="name" class="form-control" id="name" required value="{{ old('name', $table->name) }}">
                                                                @error('name')
                                                                    <span class="text-danger">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="table_number">{{ __('Table Number') }}<span class="text-danger">*</span></label>
                                                                <input type="text" name="table_number" class="form-control" id="table_number" required value="{{ old('table_number', $table->table_number) }}">
                                                                @error('table_number')
                                                                    <span class="text-danger">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="capacity">{{ __('Seating Capacity') }}<span class="text-danger">*</span></label>
                                                                <input type="number" name="capacity" class="form-control" id="capacity" required value="{{ old('capacity', $table->capacity) }}" min="1" max="50">
                                                                @error('capacity')
                                                                    <span class="text-danger">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="floor">{{ __('Floor') }}</label>
                                                                <input type="text" name="floor" class="form-control" id="floor" value="{{ old('floor', $table->floor) }}" list="floor-list">
                                                                <datalist id="floor-list">
                                                                    @foreach ($floors ?? [] as $floor)
                                                                        <option value="{{ $floor }}">
                                                                    @endforeach
                                                                </datalist>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="section">{{ __('Section') }}</label>
                                                                <input type="text" name="section" class="form-control" id="section" value="{{ old('section', $table->section) }}" list="section-list">
                                                                <datalist id="section-list">
                                                                    @foreach ($sections ?? [] as $section)
                                                                        <option value="{{ $section }}">
                                                                    @endforeach
                                                                </datalist>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="shape">{{ __('Table Shape') }}</label>
                                                                <select name="shape" id="shape" class="form-control">
                                                                    @foreach ($shapes as $key => $label)
                                                                        <option value="{{ $key }}" {{ old('shape', $table->shape) == $key ? 'selected' : '' }}>
                                                                            {{ $label }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="sort_order">{{ __('Sort Order') }}</label>
                                                                <input type="number" name="sort_order" class="form-control" id="sort_order" value="{{ old('sort_order', $table->sort_order) }}" min="0">
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="notes">{{ __('Notes') }}</label>
                                                                <textarea name="notes" class="form-control" id="notes" rows="3">{{ old('notes', $table->notes) }}</textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-4">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5>{{ __('Current Status') }}</h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="mb-3">
                                                        <span class="badge bg-{{ $table->status_badge }} fs-6 p-2">
                                                            {{ $table->status_label }}
                                                        </span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="is_active">{{ __('Active') }}</label>
                                                        <select name="is_active" id="is_active" class="form-control">
                                                            <option value="1" {{ old('is_active', $table->is_active) == 1 ? 'selected' : '' }}>{{ __('Yes') }}</option>
                                                            <option value="0" {{ old('is_active', $table->is_active) == 0 ? 'selected' : '' }}>{{ __('No') }}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="card mt-3">
                                                <div class="card-header">
                                                    <h5>{{ __('Position (for Layout)') }}</h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-6">
                                                            <div class="form-group">
                                                                <label for="position_x">{{ __('X Position') }}</label>
                                                                <input type="number" name="position_x" class="form-control" id="position_x" value="{{ old('position_x', $table->position_x) }}" min="0">
                                                            </div>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="form-group">
                                                                <label for="position_y">{{ __('Y Position') }}</label>
                                                                <input type="number" name="position_y" class="form-control" id="position_y" value="{{ old('position_y', $table->position_y) }}" min="0">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="card mt-3">
                                                <div class="card-body text-center">
                                                    <x-admin.save-button :text="__('Update Table')"></x-admin.save-button>
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
