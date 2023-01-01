@if ($paginator->hasPages())
    <nav class="mb-3 flex items-center justify-between" role="navigation">
        <div class="flex flex-1 justify-between sm:hidden">
            @if ($paginator->onFirstPage())
                <span
                    class="cursor-default bg-white p-2 text-sm text-gray-300 shadow dark:bg-neutral-600 dark:text-gray-400">
                    上一頁
                </span>
            @else
                <a class="bg-white p-2 text-sm text-gray-600 shadow hover:bg-cyan-600 hover:text-white dark:bg-neutral-600 dark:text-gray-200 dark:hover:bg-cyan-600"
                    href="{{ $paginator->previousPageUrl() }}">
                    上一頁
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a class="ml-3 bg-white p-2 text-sm text-gray-600 shadow hover:bg-cyan-600 hover:text-white dark:bg-neutral-600 dark:text-gray-200 dark:hover:bg-cyan-600"
                    href="{{ $paginator->nextPageUrl() }}">
                    下一頁
                </a>
            @else
                <span
                    class="ml-3 cursor-default bg-white p-2 text-sm text-gray-300 shadow dark:bg-neutral-600 dark:text-gray-400">
                    下一頁
                </span>
            @endif
        </div>

        <div class="hidden grow items-center justify-center gap-1 sm:flex">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span
                    class="cursor-default bg-white p-2 text-sm text-gray-300 shadow dark:bg-neutral-600 dark:text-gray-400">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                            clip-rule="evenodd" />
                    </svg>
                </span>
            @else
                <a class="bg-white p-2 text-sm text-gray-600 shadow hover:bg-cyan-600 hover:text-white dark:bg-neutral-600 dark:text-gray-200 dark:hover:bg-cyan-600"
                    href="{{ $paginator->previousPageUrl() }}" rel="prev">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                            clip-rule="evenodd" />
                    </svg>
                </a>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span
                        class="cursor-default bg-white p-2 text-sm text-gray-300 shadow dark:bg-neutral-600 dark:text-gray-400">
                        {{ $element }}
                    </span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span
                                class="basis-8 cursor-default bg-cyan-600 p-2 text-center text-sm text-white shadow dark:bg-cyan-700">
                                {{ $page }}
                            </span>
                        @else
                            <a class="basis-8 bg-white p-2 text-center text-sm text-gray-600 shadow hover:bg-cyan-600 hover:text-white dark:bg-neutral-600 dark:text-gray-200 dark:hover:bg-cyan-600"
                                href="{{ $url }}">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a class="bg-white p-2 text-sm text-gray-600 shadow hover:bg-cyan-600 hover:text-white dark:bg-neutral-600 dark:text-gray-200 dark:hover:bg-cyan-600"
                    href="{{ $paginator->nextPageUrl() }}" rel="next">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                            clip-rule="evenodd" />
                    </svg>
                </a>
            @else
                <span
                    class="cursor-default bg-white p-2 text-sm text-gray-300 shadow dark:bg-neutral-600 dark:text-gray-400">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                            clip-rule="evenodd" />
                    </svg>
                </span>
            @endif
        </div>
    </nav>
@endif
