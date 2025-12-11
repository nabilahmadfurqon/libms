@if ($paginator->hasPages())
    <div class="flex flex-col gap-2 text-xs sm:text-sm">

        {{-- Info "menampilkan X–Y dari Z data" --}}
        @if ($paginator->firstItem())
            <div class="text-slate-500 dark:text-slate-400">
                Menampilkan
                <span class="font-semibold text-slate-700 dark:text-slate-100">{{ $paginator->firstItem() }}</span>
                –
                <span class="font-semibold text-slate-700 dark:text-slate-100">{{ $paginator->lastItem() }}</span>
                dari
                <span class="font-semibold text-slate-700 dark:text-slate-100">{{ $paginator->total() }}</span>
                data
            </div>
        @else
            <div class="text-slate-500 dark:text-slate-400">
                Menampilkan {{ $paginator->count() }} data
            </div>
        @endif

        {{-- Links halaman --}}
        <div class="flex flex-wrap items-center gap-1">

            {{-- Previous --}}
            @if ($paginator->onFirstPage())
                <span class="px-2.5 py-1 rounded-md text-slate-400 border border-transparent cursor-default">
                    ‹
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}"
                   class="px-2.5 py-1 rounded-md border border-slate-200 dark:border-slate-700
                          text-slate-600 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/60">
                    ‹
                </a>
            @endif

            {{-- Numbers --}}
            @foreach ($elements as $element)
                {{-- "..." --}}
                @if (is_string($element))
                    <span class="px-2.5 py-1 text-slate-400">
                        {{ $element }}
                    </span>
                @endif

                {{-- Array of links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span
                                class="px-2.5 py-1 rounded-md bg-indigo-600 text-white text-xs sm:text-sm font-semibold">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}"
                               class="px-2.5 py-1 rounded-md border border-slate-200 dark:border-slate-700
                                      text-slate-600 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/60
                                      text-xs sm:text-sm">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}"
                   class="px-2.5 py-1 rounded-md border border-slate-200 dark:border-slate-700
                          text-slate-600 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/60">
                    ›
                </a>
            @else
                <span class="px-2.5 py-1 rounded-md text-slate-400 border border-transparent cursor-default">
                    ›
                </span>
            @endif
        </div>
    </div>
@endif
