@extends('layouts.publik')

@section('judul', $artikel->judul)
@section('deskripsi', $artikel->ringkasan)

@section('konten')

    <article class="mx-auto max-w-4xl px-4 pt-28 pb-16 sm:px-6 lg:px-8">

        <nav class="flex items-center gap-2 text-xs font-medium text-ink-500 dark:text-ink-400">
            <a href="{{ route('beranda') }}" class="transition hover:text-brand-600">Beranda</a>
            <x-ikon nama="chevron-right" class="size-3.5"/>
            <a href="{{ route('berita.index') }}" class="transition hover:text-brand-600">Berita</a>
            <x-ikon nama="chevron-right" class="size-3.5"/>
            <span class="truncate text-ink-700 dark:text-ink-200">{{ Str::limit($artikel->judul, 40) }}</span>
        </nav>

        <header class="mt-6">
            <h1 class="text-3xl leading-tight font-extrabold tracking-tight text-ink-900 sm:text-4xl dark:text-white">
                {{ $artikel->judul }}
            </h1>

            <div class="mt-5 flex flex-wrap items-center gap-x-5 gap-y-2 text-sm text-ink-500 dark:text-ink-400">
                <span class="flex items-center gap-1.5">
                    <x-ikon nama="calendar" class="size-4 text-brand-500"/>
                    {{ $artikel->published_at?->translatedFormat('l, d F Y') }}
                </span>
                <span class="flex items-center gap-1.5">
                    <x-ikon nama="eye" class="size-4 text-brand-500"/>
                    {{ number_format($artikel->dilihat, 0, ',', '.') }} kali dibaca
                </span>
                @if ($artikel->sumber)
                    <span class="flex items-center gap-1.5">
                        <x-ikon nama="info" class="size-4 text-brand-500"/> {{ $artikel->sumber }}
                    </span>
                @endif
            </div>
        </header>

        @if ($artikel->gambar_url)
            <img src="{{ $artikel->gambar_url }}" alt="{{ $artikel->judul }}"
                 class="mt-8 aspect-[16/9] w-full rounded-2xl object-cover shadow-[var(--shadow-soft)]">
        @else
            <div class="bg-noise mt-8 grid aspect-[16/6] w-full place-items-center rounded-2xl bg-gradient-to-br from-brand-600 to-brand-900">
                <x-ikon nama="megaphone" class="size-16 text-white/25"/>
            </div>
        @endif

        @if ($artikel->ringkasan)
            <p class="mt-8 border-l-4 border-brand-400 bg-brand-50/60 py-3 pl-4 text-[15px]/7 font-medium text-ink-700 dark:bg-brand-950/25 dark:text-ink-200">
                {{ $artikel->ringkasan }}
            </p>
        @endif

        <div class="isi-teks mt-8">
            {!! $artikel->konten !!}
        </div>

        <div class="mt-12 flex flex-wrap items-center justify-between gap-4 rounded-2xl border border-brand-200 bg-brand-50 p-6 dark:border-brand-900/60 dark:bg-brand-950/30">
            <div>
                <p class="text-sm font-bold text-ink-900 dark:text-white">Mengetahui dugaan pelanggaran?</p>
                <p class="mt-1 text-sm text-ink-600 dark:text-ink-300">Sampaikan melalui WBS Inspektorat Kabupaten Pringsewu.</p>
            </div>
            <a href="{{ route('register') }}" class="btn btn-primary">
                <x-ikon nama="megaphone" class="size-4"/> Lapor Sekarang
            </a>
        </div>
    </article>

    @if ($lainnya->isNotEmpty())
        <section class="border-t border-ink-200 bg-white py-16 dark:border-ink-800 dark:bg-ink-900/40">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <h2 class="text-xl font-extrabold text-ink-900 dark:text-white">Berita lainnya</h2>

                <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($lainnya as $item)
                        <a href="{{ route('berita.show', $item) }}" class="card card-hover group overflow-hidden">
                            <div class="relative aspect-[16/10] overflow-hidden bg-gradient-to-br from-brand-600 to-brand-900">
                                @if ($item->gambar_url)
                                    <img src="{{ $item->gambar_url }}" alt="" class="size-full object-cover transition duration-500 group-hover:scale-105">
                                @else
                                    <div class="bg-noise absolute inset-0 grid place-items-center">
                                        <x-ikon nama="megaphone" class="size-9 text-white/25"/>
                                    </div>
                                @endif
                            </div>
                            <div class="p-4">
                                <p class="text-[11px] font-semibold text-brand-600 dark:text-brand-400">
                                    {{ $item->published_at?->translatedFormat('d M Y') }}
                                </p>
                                <h3 class="mt-1.5 line-clamp-2 text-sm leading-snug font-bold text-ink-900 dark:text-white">
                                    {{ $item->judul }}
                                </h3>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

@endsection
