@props([
    'nama',
    'judul' => '',
    'keterangan' => null,
    'lebar' => 'max-w-lg',
])

<div x-data="{ buka: false }"
     x-on:buka-modal.window="if ($event.detail === '{{ $nama }}') buka = true"
     x-on:keydown.escape.window="buka = false"
     x-cloak>

    <div x-show="buka" x-transition.opacity.duration.200ms
         class="fixed inset-0 z-50 bg-ink-950/60 backdrop-blur-sm" @click="buka = false"></div>

    <div x-show="buka" class="fixed inset-0 z-50 overflow-y-auto p-4 sm:p-6">
        <div class="flex min-h-full items-center justify-center">
            <div x-show="buka"
                 x-transition:enter="transition duration-250 ease-out"
                 x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 x-transition:leave="transition duration-150 ease-in"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 @click.stop
                 class="card w-full {{ $lebar }} overflow-hidden shadow-[var(--shadow-lift)]">

                <div class="flex items-start justify-between gap-4 border-b border-ink-100 px-6 py-4 dark:border-ink-800">
                    <div>
                        <h3 class="text-base font-bold text-ink-900 dark:text-white">{{ $judul }}</h3>
                        @if ($keterangan)
                            <p class="mt-0.5 text-xs text-ink-500 dark:text-ink-400">{{ $keterangan }}</p>
                        @endif
                    </div>
                    <button type="button" @click="buka = false" class="btn btn-ghost btn-sm -mt-1 -mr-2" aria-label="Tutup">
                        <x-ikon nama="x" class="size-4"/>
                    </button>
                </div>

                <div class="px-6 py-5">{{ $slot }}</div>
            </div>
        </div>
    </div>
</div>
