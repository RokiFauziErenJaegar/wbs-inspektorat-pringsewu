@if ($paginator->hasPages())
    <nav class="flex flex-wrap items-center justify-between gap-4" role="navigation" aria-label="Navigasi halaman">

        <p class="text-xs text-ink-500 dark:text-ink-400">
            Menampilkan <strong class="text-ink-900 dark:text-white">{{ $paginator->firstItem() }}</strong>–<strong class="text-ink-900 dark:text-white">{{ $paginator->lastItem() }}</strong>
            dari <strong class="text-ink-900 dark:text-white">{{ $paginator->total() }}</strong> data
        </p>

        <div class="flex items-center gap-1">
            {{-- Sebelumnya --}}
            @if ($paginator->onFirstPage())
                <span class="grid size-9 cursor-not-allowed place-items-center rounded-lg text-ink-300 dark:text-ink-600">
                    <x-ikon nama="chevron-left" class="size-4"/>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Halaman sebelumnya"
                   class="grid size-9 place-items-center rounded-lg text-ink-600 transition hover:bg-ink-100 hover:text-ink-900 dark:text-ink-300 dark:hover:bg-ink-800 dark:hover:text-white">
                    <x-ikon nama="chevron-left" class="size-4"/>
                </a>
            @endif

            {{-- Nomor halaman --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="grid size-9 place-items-center text-xs text-ink-400">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page"
                                  class="grid size-9 place-items-center rounded-lg bg-brand-600 text-xs font-bold text-white shadow-[var(--shadow-glow)]">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}"
                               class="grid size-9 place-items-center rounded-lg text-xs font-semibold text-ink-600 transition hover:bg-ink-100 hover:text-ink-900 dark:text-ink-300 dark:hover:bg-ink-800 dark:hover:text-white">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Berikutnya --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Halaman berikutnya"
                   class="grid size-9 place-items-center rounded-lg text-ink-600 transition hover:bg-ink-100 hover:text-ink-900 dark:text-ink-300 dark:hover:bg-ink-800 dark:hover:text-white">
                    <x-ikon nama="chevron-right" class="size-4"/>
                </a>
            @else
                <span class="grid size-9 cursor-not-allowed place-items-center rounded-lg text-ink-300 dark:text-ink-600">
                    <x-ikon nama="chevron-right" class="size-4"/>
                </span>
            @endif
        </div>
    </nav>
@endif
