@extends('layouts.app')

@section('judul', 'Berita & Publikasi')
@section('subjudul', 'Kelola konten informasi pada halaman publik')

@section('konten')

    <div class="card p-5">
        <form method="GET" class="flex flex-col gap-3 sm:flex-row">
            <div class="relative flex-1">
                <span class="pointer-events-none absolute inset-y-0 left-0 grid w-11 place-items-center text-ink-400">
                    <x-ikon nama="search" class="size-[18px]"/>
                </span>
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari judul berita…" class="field pl-11">
            </div>
            <div class="flex gap-2">
                <button class="btn btn-primary"><x-ikon nama="search" class="size-4"/> Cari</button>
                <a href="{{ route('admin.artikel.create') }}" class="btn btn-gold">
                    <x-ikon nama="plus" class="size-4"/> Tulis Berita
                </a>
            </div>
        </form>
    </div>

    @if ($artikel->isEmpty())
        <div class="card">
            <x-kosong ikon="megaphone" judul="Belum ada berita" pesan="Publikasikan informasi seputar pengawasan internal dan penyelenggaraan WBS.">
                <a href="{{ route('admin.artikel.create') }}" class="btn btn-primary"><x-ikon nama="plus" class="size-4"/> Tulis Berita</a>
            </x-kosong>
        </div>
    @else
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($artikel as $item)
                <div class="card card-hover flex flex-col overflow-hidden">
                    <div class="relative aspect-[16/9] overflow-hidden bg-gradient-to-br from-brand-600 to-brand-900">
                        @if ($item->gambar_url)
                            <img src="{{ $item->gambar_url }}" alt="" class="size-full object-cover">
                        @else
                            <div class="bg-noise absolute inset-0 grid place-items-center">
                                <x-ikon nama="megaphone" class="size-10 text-white/25"/>
                            </div>
                        @endif

                        <span class="absolute top-3 left-3 badge {{ $item->is_published
                            ? 'bg-emerald-500 text-white ring-emerald-400/40'
                            : 'bg-slate-500 text-white ring-slate-400/40' }}">
                            {{ $item->is_published ? 'Terbit' : 'Draf' }}
                        </span>
                    </div>

                    <div class="flex flex-1 flex-col p-5">
                        <p class="text-xs font-semibold text-brand-600 dark:text-brand-400">
                            {{ $item->published_at?->translatedFormat('d M Y') ?? 'Belum dipublikasikan' }}
                        </p>
                        <h3 class="mt-1.5 line-clamp-2 text-sm leading-snug font-bold text-ink-900 dark:text-white">{{ $item->judul }}</h3>
                        <p class="mt-2 line-clamp-2 flex-1 text-xs/6 text-ink-500 dark:text-ink-400">{{ $item->ringkasan }}</p>

                        <div class="mt-4 flex items-center justify-between border-t border-ink-100 pt-3 dark:border-ink-800">
                            <span class="flex items-center gap-1.5 text-xs text-ink-500 dark:text-ink-400">
                                <x-ikon nama="eye" class="size-3.5"/> {{ number_format($item->dilihat, 0, ',', '.') }}
                            </span>

                            <div class="flex gap-1">
                                @if ($item->is_published)
                                    <a href="{{ route('berita.show', $item) }}" target="_blank" class="btn btn-ghost btn-sm" title="Lihat">
                                        <x-ikon nama="external-link" class="size-3.5"/>
                                    </a>
                                @endif
                                <a href="{{ route('admin.artikel.edit', $item) }}" class="btn btn-ghost btn-sm">
                                    <x-ikon nama="pencil" class="size-3.5"/>
                                </a>
                                <form method="POST" action="{{ route('admin.artikel.destroy', $item) }}"
                                      onsubmit="return confirm('Hapus berita ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-ghost btn-sm text-rose-600 hover:bg-rose-50 dark:text-rose-400 dark:hover:bg-rose-950/40">
                                        <x-ikon nama="trash" class="size-3.5"/>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div>{{ $artikel->links() }}</div>
    @endif

@endsection
