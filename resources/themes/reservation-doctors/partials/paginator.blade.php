@if ($paginator->hasPages())
    <div class="row">
        <div class="col-md-12">
            <div class="blog-pagination">
                <nav>
                    <ul class="pagination justify-content-center">
                        @if ($paginator->onFirstPage())
                            <li class="page-item disabled">
                                <a class="page-link" href="#" tabindex="-1"><i
                                            class="fas fa-angle-double-left"></i></a>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $paginator->previousPageUrl() }}" tabindex="-1"><i
                                            class="fas fa-angle-double-left"></i></a>
                            </li>
                        @endif

                        @foreach ($elements as $element)
                            {{-- "Three Dots" Separator --}}
                            @if (is_string($element))
                                <li class="disabled"><a href="#" class="page-link">{{ $element }}</a></li>
                            @endif

                            {{-- Array Of Links --}}
                            @if (is_array($element))
                                @foreach ($element as $page => $url)
                                    @if ($page == $paginator->currentPage())
                                        <li class="page-item active"><a href="#" class="page-link">{{ $page }}</a></li>
                                    @else
                                        <li class="page-item"><a href="{{ $url }}" class="page-link">{{ $page }}</a>
                                        </li>
                                    @endif
                                @endforeach
                            @endif
                        @endforeach
                        {{-- Next Page Link --}}
                        @if ($paginator->hasMorePages())
                            <li class="page-item">
                                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="page-link">
                                    <i class="fas fa-angle-double-right"></i>
                                </a>
                            </li>
                        @else
                            <li class="page-item disabled"><a href="#" class="page-link"> <i
                                            class="fas fa-angle-double-right"></i></a>
                            </li>
                        @endif
                    </ul>
                </nav>
            </div>
        </div>
    </div>
@endif
