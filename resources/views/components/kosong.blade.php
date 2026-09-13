@props([
    'ikon' => 'inbox',
    'judul' => 'Belum ada data',
    'pesan' => null,
])

<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center px-6 py-16 text-center']) }}>
    <span class="relative grid size-20 place-items-center rounded-2xl bg-gradient-to-br from-ink-100 to-ink-200 text-ink-400 dark:from-ink-800 dark:to-ink-900 dark:text-ink-500">
        <x-ikon :nama="$ikon" class="size-9"/>
        <span class="absolute -inset-3 -z-10 rounded-3xl bg-brand-500/5 blur-xl"></span>
    </span>

    <h3 class="mt-5 text-base font-bold text-ink-800 dark:text-ink-100">{{ $judul }}</h3>

    @if ($pesan)
        <p class="mt-1.5 max-w-sm text-sm text-ink-500 dark:text-ink-400">{{ $pesan }}</p>
    @endif

    @if ($slot->isNotEmpty())
        <div class="mt-6">{{ $slot }}</div>
    @endif
</div>
