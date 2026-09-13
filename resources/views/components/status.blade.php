@props(['laporan' => null, 'status' => null])

@php
    $status ??= $laporan?->status;
    $label = \App\Models\Laporan::STATUS[$status] ?? $status;

    $gaya = [
        'draft' => ['pencil', 'bg-slate-100 text-slate-700 ring-slate-600/20 dark:bg-slate-500/15 dark:text-slate-300 dark:ring-slate-400/30'],
        'terkirim' => ['clock', 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-500/15 dark:text-amber-300 dark:ring-amber-400/30'],
        'verifikasi' => ['shield-check', 'bg-sky-50 text-sky-700 ring-sky-600/20 dark:bg-sky-500/15 dark:text-sky-300 dark:ring-sky-400/30'],
        'diproses' => ['refresh', 'bg-indigo-50 text-indigo-700 ring-indigo-600/20 dark:bg-indigo-500/15 dark:text-indigo-300 dark:ring-indigo-400/30'],
        'selesai' => ['check-badge', 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-500/15 dark:text-emerald-300 dark:ring-emerald-400/30'],
        'ditolak' => ['x-circle', 'bg-rose-50 text-rose-700 ring-rose-600/20 dark:bg-rose-500/15 dark:text-rose-300 dark:ring-rose-400/30'],
    ];

    [$ikon, $kelas] = $gaya[$status] ?? ['info', 'bg-slate-100 text-slate-700 ring-slate-600/20'];
@endphp

<span {{ $attributes->merge(['class' => 'badge '.$kelas]) }}>
    <x-ikon :nama="$ikon" class="size-3.5"/>
    {{ $label }}
</span>
