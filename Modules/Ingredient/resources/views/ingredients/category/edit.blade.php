@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Category List') }}</title>
@endsection
@section('content')
    <div class="main-content">
        <section class="section">

            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between">
                                <h4 class="section_title">{{ __('Add Category') }}</h4>
                                <div>
                                    <a href="{{ route('admin.category.index') }}" class="btn btn-primary"><i
                                            class="fa fa-arrow-left"></i>{{ __('Back') }}</a>
                                </div>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('admin.category.update', $cat) }}" method="post">
                                    @csrf
                                    @method('PUT')
                                    <div class="row justify-content-center">
                                        <div class="col-lg-8">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label for="name">{{ __('Name') }}<span
                                                                class="text-danger">*</span></label>
                                                        <input type="text" name="name" class="form-control"
                                                            id="name" required value="{{ old('name', $cat->name) }}"
                                                            required>
                                                        @error('name')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label for="slug">{{ __('Status') }}<span
                                                                class="text-danger">*</span></label>
                                                        <select name="status" id="status" class="form-control">
                                                            <option value="1"
                                                                @if ($cat->status == 1) selected @endif>
                                                                {{ __('Active') }}</option>
                                                            <option value="0"
                                                                @if ($cat->status == 0) selected @endif>
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
                                                        <select name="parent_id" id="parent"
                                                            class="form-control select2">
                                                            <option value="">{{ __('Select One') }}</option>
                                                            @foreach ($categories as $category)
                                                                <option value="{{ $category->id }}"
                                                                    @if ($category->id == $cat->parent_id) selected @endif>
                                                                    {{ $category->name }}</option>
                                                            @endforeach
                                                        </select>
                                                        @error('parent')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-12 text-center">
                                                    <x-admin.update-button :text="__('Update')">
                                                    </x-admin.update-button>
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
