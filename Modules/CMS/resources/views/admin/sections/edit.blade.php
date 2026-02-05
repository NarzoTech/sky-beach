@extends('admin.layouts.master')

@section('title')
    {{ __('Edit') }} {{ $config['label'] ?? ucwords(str_replace('_', ' ', $section)) }}
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">{{ __('Edit') }} {{ $config['label'] ?? ucwords(str_replace('_', ' ', $section)) }}</h4>
            @php
                $backRoutes = [
                    'home' => 'admin.cms.sections.homepage',
                    'about' => 'admin.cms.sections.about',
                    'contact' => 'admin.cms.sections.contact',
                    'menu' => 'admin.cms.sections.menu',
                    'menu_detail' => 'admin.cms.sections.menu-detail',
                    'reservation' => 'admin.cms.sections.reservation',
                    'service' => 'admin.cms.sections.service',
                ];
                $backRoute = $backRoutes[$pageName] ?? 'admin.cms.sections.homepage';
                $pageLabels = [
                    'home' => 'Homepage',
                    'about' => 'About Page',
                    'contact' => 'Contact Page',
                    'menu' => 'Menu Page',
                    'menu_detail' => 'Menu Detail Page',
                    'reservation' => 'Reservation Page',
                    'service' => 'Service Page',
                ];
                $pageLabel = $pageLabels[$pageName] ?? 'Homepage';
            @endphp
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route($backRoute) }}">{{ __($pageLabel) }}</a></li>
                    <li class="breadcrumb-item active">{{ $config['label'] ?? $section }}</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route($backRoute) }}" class="btn btn-outline-secondary">
            <i class="bx bx-arrow-back me-1"></i> {{ __('Back to Sections') }}
        </a>
    </div>

    <form action="{{ route('admin.cms.sections.update', $section) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <input type="hidden" name="page_name" value="{{ $pageName }}">
        <input type="hidden" name="lang_code" value="{{ $langCode }}">

        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('Section Content') }}</h5>
                    </div>
                    <div class="card-body">
                        @if(in_array('title', $config['fields']))
                        <div class="mb-3">
                            <label class="form-label" for="title">{{ __('Title') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror"
                                   id="title" name="title"
                                   value="{{ old('title', $translation->title ?? '') }}"
                                   placeholder="{{ __('Enter section title') }}">
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        @endif

                        @if(in_array('subtitle', $config['fields']))
                        <div class="mb-3">
                            <label class="form-label" for="subtitle">{{ __('Subtitle') }}</label>
                            <input type="text" class="form-control @error('subtitle') is-invalid @enderror"
                                   id="subtitle" name="subtitle"
                                   value="{{ old('subtitle', $translation->subtitle ?? '') }}"
                                   placeholder="{{ __('Enter section subtitle') }}">
                            @error('subtitle')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        @endif

                        @if(in_array('description', $config['fields']))
                        <div class="mb-3">
                            <label class="form-label" for="description">{{ __('Description') }}</label>
                            <textarea class="form-control summernote @error('description') is-invalid @enderror"
                                      id="description" name="description" rows="4"
                                      placeholder="{{ __('Enter section description') }}">{{ old('description', $translation->description ?? '') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        @endif

                        @if(in_array('quantity', $config['fields']))
                        <div class="mb-3">
                            <label class="form-label" for="quantity">{{ __('Number of Items to Display') }}</label>
                            <input type="number" class="form-control @error('quantity') is-invalid @enderror"
                                   id="quantity" name="quantity"
                                   value="{{ old('quantity', $sectionData->quantity ?? 6) }}"
                                   min="1" max="12">
                            @error('quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        @endif

                        @if(in_array('video', $config['fields']))
                        <div class="mb-3">
                            @php
                                $isMapSection = str_contains($section, '_map') || $section === 'contact_map';
                            @endphp
                            <label class="form-label" for="video">
                                {{ $isMapSection ? __('Google Maps Embed URL') : __('Video URL') }}
                            </label>
                            <input type="url" class="form-control @error('video') is-invalid @enderror"
                                   id="video" name="video"
                                   value="{{ old('video', $sectionData->video ?? '') }}"
                                   placeholder="{{ $isMapSection ? 'https://www.google.com/maps/embed?pb=...' : 'https://youtube.com/watch?v=...' }}">
                            @error('video')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                @if($isMapSection)
                                    {{ __('Paste the Google Maps embed URL (from Google Maps > Share > Embed)') }}
                                @else
                                    {{ __('YouTube or Vimeo URL') }}
                                @endif
                            </small>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Button Settings -->
                @if(in_array('button_text', $config['fields']) || in_array('button_link', $config['fields']))
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('Button Settings') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @if(in_array('button_text', $config['fields']))
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="button_text">{{ __('Button Text') }}</label>
                                <input type="text" class="form-control @error('button_text') is-invalid @enderror"
                                       id="button_text" name="button_text"
                                       value="{{ old('button_text', $sectionData->button_text ?? '') }}"
                                       placeholder="{{ __('e.g., Order Now') }}">
                                @error('button_text')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            @endif

                            @if(in_array('button_link', $config['fields']))
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="button_link">{{ __('Button Link') }}</label>
                                <input type="text" class="form-control @error('button_link') is-invalid @enderror"
                                       id="button_link" name="button_link"
                                       value="{{ old('button_link', $sectionData->button_link ?? '') }}"
                                       placeholder="{{ __('e.g., /menu or https://...') }}">
                                @error('button_link')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            @endif
                        </div>

                        @if(in_array('button_link_2', $config['fields']))
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="button_text_2">{{ __('Secondary Button Text') }}</label>
                                <input type="text" class="form-control"
                                       id="button_text_2" name="button_text_2"
                                       value="{{ old('button_text_2', $sectionData->button_text_2 ?? '') }}"
                                       placeholder="{{ __('e.g., Play Store') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="button_link_2">{{ __('Secondary Button Link') }}</label>
                                <input type="text" class="form-control @error('button_link_2') is-invalid @enderror"
                                       id="button_link_2" name="button_link_2"
                                       value="{{ old('button_link_2', $sectionData->button_link_2 ?? '') }}"
                                       placeholder="{{ __('e.g., https://play.google.com/...') }}">
                                @error('button_link_2')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>

            <div class="col-md-4">
                <!-- Status & Settings -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('Settings') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input type="checkbox" class="form-check-input" id="section_status" name="section_status" value="1"
                                       {{ old('section_status', $sectionData->section_status ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="section_status">{{ __('Show this section') }}</label>
                            </div>
                        </div>

                        @if(in_array('show_search', $config['fields']))
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input type="checkbox" class="form-check-input" id="show_search" name="show_search" value="1"
                                       {{ old('show_search', $sectionData->show_search ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label" for="show_search">{{ __('Show search bar') }}</label>
                            </div>
                        </div>
                        @endif

                        <div class="mb-3">
                            <label class="form-label" for="sort_order">{{ __('Sort Order') }}</label>
                            <input type="number" class="form-control" id="sort_order" name="sort_order"
                                   value="{{ old('sort_order', $sectionData->sort_order ?? 0) }}" min="0">
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bx bx-save me-1"></i> {{ __('Save Changes') }}
                        </button>
                    </div>
                </div>

                <!-- Image Upload -->
                @if(in_array('image', $config['fields']))
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('Section Image') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            @if($sectionData && $sectionData->image)
                                <div class="mb-2">
                                    <img src="{{ asset($sectionData->image) }}" alt="Current Image" class="img-fluid rounded" style="max-height: 150px;">
                                </div>
                            @endif
                            <input type="file" class="form-control @error('image') is-invalid @enderror"
                                   id="image" name="image" accept="image/*">
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">{{ __('Recommended: 800x600px, Max 2MB') }}</small>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Gallery Images -->
                @if(in_array('gallery_images', $config['fields']))
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('Gallery Images') }}</h5>
                    </div>
                    <div class="card-body">
                        @php
                            $galleryCount = $config['gallery_count'] ?? 4;
                            $galleryLabels = $config['gallery_labels'] ?? [];
                            $existingImages = $sectionData->images ?? [];
                        @endphp
                        <div class="row">
                            @for($i = 0; $i < $galleryCount; $i++)
                            <div class="col-6 mb-3">
                                <label class="form-label">
                                    {{ $galleryLabels[$i] ?? __('Image') . ' ' . ($i + 1) }}
                                </label>
                                @if(!empty($existingImages[$i]))
                                    <div class="mb-2 position-relative">
                                        <img src="{{ asset($existingImages[$i]) }}" alt="Gallery Image {{ $i + 1 }}" class="img-fluid rounded" style="max-height: 120px; width: 100%; object-fit: cover;">
                                    </div>
                                @endif
                                <input type="file" class="form-control form-control-sm @error('gallery_images.' . $i) is-invalid @enderror"
                                       name="gallery_images[{{ $i }}]" accept="image/*">
                                @error('gallery_images.' . $i)
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            @endfor
                        </div>
                        <small class="text-muted">{{ __('Max 2MB per image. Leave empty to keep existing.') }}</small>
                    </div>
                </div>
                @endif

                <!-- Background Image -->
                @if(in_array('background_image', $config['fields']))
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('Background Image') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            @if($sectionData && $sectionData->background_image)
                                <div class="mb-2">
                                    <img src="{{ asset($sectionData->background_image) }}" alt="Current Background" class="img-fluid rounded" style="max-height: 150px;">
                                </div>
                            @endif
                            <input type="file" class="form-control @error('background_image') is-invalid @enderror"
                                   id="background_image" name="background_image" accept="image/*">
                            @error('background_image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">{{ __('Recommended: 1920x1080px, Max 2MB') }}</small>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </form>
</div>
@endsection
