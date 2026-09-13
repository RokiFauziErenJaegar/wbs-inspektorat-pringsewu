@extends('layouts.app')

@section('judul', 'Kategori Pengaduan')
@section('subjudul', 'Ruang lingkup dugaan pelanggaran yang dapat dilaporkan')

@section('konten')

    <div class="flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm text-ink-500 dark:text-ink-400">
            Total <strong class="text-ink-900 dark:text-white">{{ $kategori->total() }}</strong> kategori terdaftar
        </p>
        <a href="{{ route('admin.kategori.create') }}" class="btn btn-primary">
            <x-ikon nama="plus" class="size-4"/> Tambah Kategori
        </a>
    </div>

    @if ($kategori->isEmpty())
        <div class="card">
            <x-kosong ikon="tag" judul="Belum ada kategori" pesan="Tambahkan kategori agar pelapor dapat memilih tujuan pengaduan.">
                <a href="{{ route('admin.kategori.create') }}" class="btn btn-primary"><x-ikon nama="plus" class="size-4"/> Tambah Kategori</a>
            </x-kosong>
        </div>
    @else
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($kategori as $item)
                <div class="card card-hover flex flex-col p-6 {{ $item->is_active ? '' : 'opacity-60' }}">
                    <div class="flex items-start justify-between gap-3">
                        <span class="grid size-12 place-items-center rounded-2xl bg-gradient-to-br from-brand-500 to-brand-700 text-white shadow-lg shadow-brand-500/25">
                            <x-ikon :nama="$item->icon" class="size-6"/>
                        </span>

                        <span class="badge {{ $item->is_active
                            ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-500/15 dark:text-emerald-300 dark:ring-emerald-400/30'
                            : 'bg-slate-100 text-slate-600 ring-slate-600/20 dark:bg-slate-500/15 dark:text-slate-300 dark:ring-slate-400/30' }}">
                            {{ $item->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>

                    <h3 class="mt-4 text-base leading-snug font-bold text-ink-900 dark:text-white">{{ $item->nama }}</h3>
                    <p class="mt-2 flex-1 line-clamp-3 text-sm/6 text-ink-500 dark:text-ink-400">{{ $item->deskripsi }}</p>

                    <div class="mt-4 flex items-center gap-2 text-xs">
                        <span class="chip bg-ink-100 font-mono text-ink-500 dark:bg-ink-800 dark:text-ink-400">{{ $item->kode }}</span>
                        <span class="chip bg-brand-100 text-brand-700 dark:bg-brand-900/40 dark:text-brand-300">
                            {{ $item->laporan_count }} pengaduan
                        </span>
                    </div>

                    <div class="mt-5 flex gap-2 border-t border-ink-100 pt-4 dark:border-ink-800">
                        <a href="{{ route('admin.kategori.edit', $item) }}" class="btn btn-outline btn-sm flex-1">
                            <x-ikon nama="pencil" class="size-3.5"/> Ubah
                        </a>
                        <form method="POST" action="{{ route('admin.kategori.destroy', $item) }}"
                              onsubmit="return confirm('Hapus kategori {{ $item->nama }}?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-outline btn-sm text-rose-600 hover:border-rose-300 hover:bg-rose-50 dark:text-rose-400 dark:hover:bg-rose-950/30">
                                <x-ikon nama="trash" class="size-3.5"/>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <div>{{ $kategori->links() }}</div>
    @endif

@endsection
