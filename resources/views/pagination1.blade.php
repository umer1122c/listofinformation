@if ($paginator->hasPages())
    <div class="pagination-wrapper">
        <nav aria-label="Page navigation example">
            <ul class="pagination justify-content-center">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <li class="page-item active"><a class="page-link"><span aria-hidden="true">&laquo;</span> <span class="sr-only">Previous</span></a></li>
                @else
    <div class="pagination-wrapper">
                    <li class="page-item"><a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev"><span aria-hidden="true">&laquo;</span> <span class="sr-only">Previous</span></a></li>
                @endif

                {{-- Pagination Elements --}}
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <li class="page-item"><a class="page-link">{{ $element }}</a></li>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <li class="active page-item"><a class="page-link">{{ $page }}</a></li>
                            @else
                                <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <li class="page-item"><a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next"><span aria-hidden="true">&raquo;</span> <span class="sr-only">Next</span></a></li>
                @else
                    <li class="page-item active"><a class="page-link"><span aria-hidden="true">&raquo;</span> <span class="sr-only">Next</span></a></li>
                @endif
            </ul>
        </nav>
    </div>
@endif