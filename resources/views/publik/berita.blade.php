@extends('layouts.publik')

@section('judul', 'Berita')

@section('konten')

    <section class="relative -mt-18 overflow-hidden bg-gradient-to-br from-brand-900 via-brand-950 to-ink-950 pt-32 pb-20">
        <div class="bg-grid absolute inset-0 opacity-20"></div>
        <div class="absolute -top-24 left-1/3 size-96 rounded-full bg-brand-500/15 blur-3xl"></div>

        <div class="relative mx-auto max-w-3xl px-4 text-center sm:px-6">
            <span class="badge bg-white/10 text-brand-200 ring-white/20 backdrop-blur">
                <x-ikon nama="megaphone" class="size-3.5"/> Publikasi
            </span>
            <h1 class="mt-5 text-4xl font-extrabold tracking-tight text-white sm:text-5xl">Berita &amp; Informasi</h1>
            <p class="mt-4 text-base/7 text-white/70">
                Kabar terbaru seputar pengawasan internal dan penyelenggaraan Whistleblowing System
                Inspektorat Kabupaten Pringsewu.
            </p>

            <form method="GET" class="mx-auto mt-8 flex max-w-lg gap-2">
                <div class="relative flex-1">
                    <span class="pointer-events-none absolute inset-y-0 left-0 grid w-11 place-items-center text-white/40">
                        <x-ikon nama="search" class="size-[18px]"/>
                    </span>
                    <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari berita…"
                           class="w-full rounded-xl border border-white/15 bg-white/10 py-2.5 pr-4 pl-11 text-sm text-white backdrop-blur transition placeholder:text-white/40 focus:border-brand-400 focus:ring-4 focus:ring-brand-400/20 focus:outline-none">
                </div>
                <button class="btn btn-gold">Cari</button>
            </form>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        @if ($artikel->isEmpty())
            <div class="card">
                <x-kosong ikon="megaphone" judul="Berita tidak ditemukan"
                          pesan="Belum ada publikasi yang sesuai dengan kata kunci pencarian Anda." />
            </div>
        @else
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($artikel as $item)
                    <a href="{{ route('berita.show', $item) }}" class="card card-hover group flex flex-col overflow-hidden">
                        <div class="relative aspect-[16/10] overflow-hidden bg-gradient-to-br from-brand-600 to-brand-900">
                            @if ($item->gambar_url)
                                <img src="{{ $item->gambar_url }}" alt="" class="size-full object-cover transition duration-500 group-hover:scale-105">
                            @else
                                <div class="bg-noise absolute inset-0 grid place-items-center">
                                    <x-ikon nama="megaphone" class="size-12 text-white/25"/>
                                </div>
                            @endif
                            <span class="absolute top-3 left-3 badge bg-white/90 text-ink-700 ring-white/50 backdrop-blur">
                                <x-ikon nama="calendar" class="size-3.5"/>
                                {{ $item->published_at?->translatedFormat('d M Y') }}
                            </span>
                        </div>

                        <div class="flex flex-1 flex-col p-5">
                            <h2 class="line-clamp-2 text-base leading-snug font-bold text-ink-900 transition group-hover:text-brand-700 dark:text-white dark:group-hover:text-brand-400">
                                {{ $item->judul }}
                            </h2>
                            <p class="mt-2 line-clamp-3 flex-1 text-sm/6 text-ink-500 dark:text-ink-400">{{ $item->ringkasan }}</p>

                            <div class="mt-4 flex items-center justify-between border-t border-ink-100 pt-3 text-xs text-ink-500 dark:border-ink-800 dark:text-ink-400">
                                <span class="flex items-center gap-1.5"><x-ikon nama="eye" class="size-3.5"/> {{ number_format($item->dilihat, 0, ',', '.') }}</span>
                                <span class="flex items-center gap-1 font-bold text-brand-600 transition-all group-hover:gap-2 dark:text-brand-400">
                                    Baca <x-ikon nama="arrow-right" class="size-3.5"/>
                                </span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-10">{{ $artikel->links() }}</div>
        @endif
    </section>

@endsection
