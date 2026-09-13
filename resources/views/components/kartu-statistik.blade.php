@props([
    'label' => '',
    'nilai' => 0,
    'ikon' => 'chart',
    'warna' => 'brand',
    'keterangan' => null,
    'tautan' => null,
])

@php
    $palet = [
        'brand' => 'from-brand-500 to-brand-700 shadow-brand-500/30',
        'gold' => 'from-gold-400 to-gold-600 shadow-gold-500/30',
        'sky' => 'from-sky-400 to-sky-600 shadow-sky-500/30',
        'indigo' => 'from-indigo-400 to-indigo-600 shadow-indigo-500/30',
        'violet' => 'from-violet-400 to-violet-600 shadow-violet-500/30',
        'rose' => 'from-rose-400 to-rose-600 shadow-rose-500/30',
        'slate' => 'from-slate-400 to-slate-600 shadow-slate-500/30',
    ][$warna] ?? 'from-brand-500 to-brand-700 shadow-brand-500/30';

    $tag = $tautan ? 'a' : 'div';
@endphp

<{{ $tag }} @if ($tautan) href="{{ $tautan }}" @endif
    {{ $attributes->merge(['class' => 'card card-hover group relative overflow-hidden p-5']) }}>
    {{-- Aksen dekoratif --}}
    <span class="pointer-events-none absolute -top-10 -right-10 size-32 rounded-full bg-gradient-to-br {{ $palet }} opacity-[0.07] transition-transform duration-500 group-hover:scale-125"></span>

    <div class="flex items-start justify-between gap-3">
        <div class="min-w-0">
            <p class="truncate text-[13px] font-semibold text-ink-500 dark:text-ink-400">{{ $label }}</p>
            <p class="mt-1.5 text-3xl font-extrabold tracking-tight text-ink-900 tabular-nums dark:text-white">
                {{ is_numeric($nilai) ? number_format((float) $nilai, 0, ',', '.') : $nilai }}
            </p>
            @if ($keterangan)
                <p class="mt-1 truncate text-xs text-ink-500 dark:text-ink-400">{{ $keterangan }}</p>
            @endif
        </div>

        <span class="grid size-11 shrink-0 place-items-center rounded-xl bg-gradient-to-br {{ $palet }} text-white shadow-lg">
            <x-ikon :nama="$ikon" class="size-5"/>
        </span>
    </div>
</{{ $tag }}>
