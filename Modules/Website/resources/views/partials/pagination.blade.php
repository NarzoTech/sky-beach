@if ($paginator->hasPages())
    <ul class="pagination justify-content-center">
        {{-- Previous Page Link --}}
        <li class="page-item">
            @if ($paginator->onFirstPage())
                <span class="page-link" aria-label="Previous">
                    <i class="far fa-arrow-left"></i>
                </span>
            @else
                <a class="page-link" href="{{ $paginator->previousPageUrl() }}" aria-label="Previous">
                    <i class="far fa-arrow-left"></i>
                </a>
            @endif
        </li>

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <li class="page-item"><span class="page-link">{{ $element }}</span></li>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    <li class="page-item">
                        @if ($page == $paginator->currentPage())
                            <a class="page-link active" href="#">{{ $page }}</a>
                        @else
                            <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                        @endif
                    </li>
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        <li class="page-item">
            @if ($paginator->hasMorePages())
                <a class="page-link" href="{{ $paginator->nextPageUrl() }}" aria-label="Next">
                    <i class="far fa-arrow-right"></i>
                </a>
            @else
                <span class="page-link" aria-label="Next">
                    <i class="far fa-arrow-right"></i>
                </span>
            @endif
        </li>
    </ul>
@endif
