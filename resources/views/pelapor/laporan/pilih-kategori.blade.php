@extends('layouts.app')

@section('judul', 'Pilih Tujuan Pengaduan')
@section('subjudul', 'Langkah 1 dari 2 — tentukan kategori pengaduan Anda')

@section('konten')

    {{-- Indikator langkah --}}
    <div class="card p-5">
        <ol class="flex items-center gap-3 sm:gap-6">
            @foreach ([['Pilih Kategori', true], ['Isi Formulir', false], ['Kirim Pengaduan', false]] as $i => [$label, $aktif])
                <li class="flex flex-1 items-center gap-3">
                    <span class="grid size-9 shrink-0 place-items-center rounded-xl text-sm font-bold {{ $aktif ? 'bg-brand-600 text-white shadow-[var(--shadow-glow)]' : 'bg-ink-100 text-ink-400 dark:bg-ink-800 dark:text-ink-500' }}">
                        {{ $i + 1 }}
                    </span>
                    <span class="hidden text-sm font-semibold sm:block {{ $aktif ? 'text-ink-900 dark:text-white' : 'text-ink-400 dark:text-ink-500' }}">
                        {{ $label }}
                    </span>
                    @unless ($loop->last)
                        <span class="h-px flex-1 bg-ink-200 dark:bg-ink-800"></span>
                    @endunless
                </li>
            @endforeach
        </ol>
    </div>

    <div class="rounded-2xl border border-sky-200 bg-sky-50 p-5 dark:border-sky-900/60 dark:bg-sky-950/25">
        <div class="flex gap-3">
            <x-ikon nama="info" class="mt-0.5 size-5 shrink-0 text-sky-600 dark:text-sky-400"/>
            <div class="text-sm/6 text-sky-900 dark:text-sky-200">
                <p class="font-bold">Pilih kategori yang paling sesuai</p>
                <p class="mt-1">
                    Kategori membantu Inspektorat mengarahkan pengaduan Anda kepada tim yang tepat.
                    Jika ragu, pilih <strong>Pelanggaran Lainnya</strong> — klasifikasi akhir tetap ditentukan Inspektorat.
                </p>
            </div>
        </div>
    </div>

    {{-- Pencarian kategori --}}
    <div x-data="{ cari: '' }" class="space-y-6">
        <div class="relative">
            <span class="pointer-events-none absolute inset-y-0 left-0 grid w-12 place-items-center text-ink-400">
                <x-ikon nama="search" class="size-5"/>
            </span>
            <input type="search" x-model="cari" placeholder="Cari kategori pengaduan…"
                   class="field py-3.5 pl-12 text-base">
        </div>

        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($kategori as $item)
                <a href="{{ route('pelapor.laporan.create', ['kategori' => $item->slug]) }}"
                   x-show="!cari || '{{ Str::lower($item->nama.' '.$item->kode.' '.$item->deskripsi) }}'.includes(cari.toLowerCase())"
                   class="card card-hover group relative flex flex-col overflow-hidden p-6">

                    <span class="pointer-events-none absolute -top-12 -right-12 size-32 rounded-full bg-brand-500/5 transition-transform duration-500 group-hover:scale-150"></span>

                    <span class="grid size-14 place-items-center rounded-2xl bg-gradient-to-br from-brand-500 to-brand-700 text-white shadow-lg shadow-brand-500/25 transition-transform duration-300 group-hover:scale-110">
                        <x-ikon :nama="$item->icon" class="size-7"/>
                    </span>

                    <h3 class="mt-5 text-base leading-snug font-bold text-ink-900 dark:text-white">{{ $item->nama }}</h3>
                    <p class="mt-2 flex-1 text-sm/6 text-ink-500 dark:text-ink-400">{{ $item->deskripsi }}</p>

                    <span class="mt-5 flex items-center justify-between border-t border-ink-100 pt-4 dark:border-ink-800">
                        <span class="chip bg-ink-100 font-mono text-ink-500 dark:bg-ink-800 dark:text-ink-400">{{ $item->kode }}</span>
                        <span class="flex items-center gap-1 text-xs font-bold text-brand-600 transition-all group-hover:gap-2 dark:text-brand-400">
                            Pilih <x-ikon nama="arrow-right" class="size-3.5"/>
                        </span>
                    </span>
                </a>
            @endforeach
        </div>
    </div>

    <div>
        <a href="{{ route('pelapor.laporan.index') }}" class="btn btn-ghost">
            <x-ikon nama="arrow-left" class="size-4"/> Kembali ke daftar pengaduan
        </a>
    </div>

@endsection
