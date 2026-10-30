@if ($paginator->hasPages())
    <nav aria-label="Pagination" class="flex items-center justify-center gap-2">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="px-3 py-2 text-gray-400 cursor-not-allowed rounded-lg bg-gray-50">
                <i class="fas fa-chevron-left"></i>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="px-3 py-2 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg bg-gray-50 transition-colors duration-200">
                <i class="fas fa-chevron-left"></i>
            </a>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <span class="px-3 py-2 text-gray-400">{{ $element }}</span>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="px-4 py-2 text-white bg-blue-600 rounded-lg font-medium">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $url }}" class="px-4 py-2 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg bg-gray-50 transition-colors duration-200">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="px-3 py-2 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg bg-gray-50 transition-colors duration-200">
                <i class="fas fa-chevron-right"></i>
            </a>
        @else
            <span class="px-3 py-2 text-gray-400 cursor-not-allowed rounded-lg bg-gray-50">
                <i class="fas fa-chevron-right"></i>
            </span>
        @endif
    </nav>
@endif
