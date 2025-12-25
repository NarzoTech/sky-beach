{{-- @props([
    'title' => __('Title'),
    'list' => [],
])

<div class="section-header">
    <h1>{{ $title }}</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb mb-0">
            @foreach ($list as $key => $value)
                @if ($loop->last)
                    <div class="breadcrumb-item active" aria-current="page">{{ $key }}</div>
                @else
                    <div class="breadcrumb-item"><a href="{{ $value}}">{{ $key }}</a></div>
                @endif
            @endforeach
        </div>
    </div>
</div> --}}

@props([
    'title' => __('Title'),
    'subtitle' => '',
    'list' => [],
])

<div class="app-page-title">
    <div class="page-title-wrapper">
        <div class="page-title-heading">
            <div class="page-title-icon">
                <i class="pe-7s-car icon-gradient bg-mean-fruit"></i>
            </div>
            <div>{{ $title }}
                @if ($subtitle)
                    <div class="page-title-subheading">{{ $subtitle }}</div>
                @endif
            </div>
        </div>
        <div class="page-title-actions">
            <button type="button" data-bs-toggle="tooltip" title="Example Tooltip" data-placement="bottom"
                class="btn-shadow me-3 btn btn-dark">
                <i class="fa fa-search"></i>
            </button>

            <div class="d-inline-block dropdown">
                <button type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                    class="btn-shadow dropdown-toggle btn btn-info">
                    <span class="btn-icon-wrapper pr-2 opacity-7">
                        <i class="fa fa-business-time fa-w-20"></i>
                    </span>
                    Action
                </button>
                <div tabindex="-1" role="menu" aria-hidden="true" class="dropdown-menu dropdown-menu-right">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link">
                                <i class="nav-link-icon lnr-inbox"></i>
                                <span>
                                    Inbox
                                </span>
                                <div class="ml-auto badge badge-pill badge-secondary">86</div>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link">
                                <i class="nav-link-icon lnr-book"></i>
                                <span>
                                    Book
                                </span>
                                <div class="ml-auto badge badge-pill badge-danger">5</div>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link">
                                <i class="nav-link-icon lnr-picture"></i>
                                <span>
                                    Picture
                                </span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a disabled class="nav-link disabled">
                                <i class="nav-link-icon lnr-file-empty"></i>
                                <span>
                                    File Disabled
                                </span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
