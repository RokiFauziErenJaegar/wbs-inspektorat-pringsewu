@extends('layouts.app')

@section('judul', 'Pertanyaan Umum (FAQ)')
@section('subjudul', 'Kelola daftar pertanyaan yang tampil di halaman publik')

@section('konten')

    <div class="flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm text-ink-500 dark:text-ink-400">
            Total <strong class="text-ink-900 dark:text-white">{{ $faq->total() }}</strong> pertanyaan
        </p>
        <a href="{{ route('admin.faq.create') }}" class="btn btn-primary">
            <x-ikon nama="plus" class="size-4"/> Tambah FAQ
        </a>
    </div>

    <div class="card overflow-hidden">
        @if ($faq->isEmpty())
            <x-kosong ikon="book-open" judul="Belum ada FAQ" pesan="Tambahkan pertanyaan yang sering diajukan pelapor.">
                <a href="{{ route('admin.faq.create') }}" class="btn btn-primary"><x-ikon nama="plus" class="size-4"/> Tambah FAQ</a>
            </x-kosong>
        @else
            <div class="divide-y divide-ink-100 dark:divide-ink-800">
                @foreach ($faq as $item)
                    <div class="flex flex-col gap-4 p-5 sm:flex-row sm:items-start">
                        <span class="grid size-10 shrink-0 place-items-center rounded-xl bg-brand-100 text-sm font-bold text-brand-700 dark:bg-brand-900/40 dark:text-brand-300">
                            {{ $item->urutan }}
                        </span>

                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="chip bg-ink-100 text-ink-600 dark:bg-ink-800 dark:text-ink-300">{{ Str::title($item->kelompok) }}</span>
                                <span class="badge {{ $item->is_active
                                    ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-500/15 dark:text-emerald-300 dark:ring-emerald-400/30'
                                    : 'bg-slate-100 text-slate-600 ring-slate-600/20 dark:bg-slate-500/15 dark:text-slate-300 dark:ring-slate-400/30' }}">
                                    {{ $item->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </div>

                            <h3 class="mt-2 text-sm font-bold text-ink-900 dark:text-white">{{ $item->pertanyaan }}</h3>
                            <p class="mt-1.5 line-clamp-2 text-sm/6 text-ink-500 dark:text-ink-400">{{ $item->jawaban }}</p>
                        </div>

                        <div class="flex shrink-0 gap-1">
                            <a href="{{ route('admin.faq.edit', $item) }}" class="btn btn-ghost btn-sm">
                                <x-ikon nama="pencil" class="size-3.5"/>
                            </a>
                            <form method="POST" action="{{ route('admin.faq.destroy', $item) }}"
                                  onsubmit="return confirm('Hapus FAQ ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-ghost btn-sm text-rose-600 hover:bg-rose-50 dark:text-rose-400 dark:hover:bg-rose-950/40">
                                    <x-ikon nama="trash" class="size-3.5"/>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="border-t border-ink-100 px-5 py-4 dark:border-ink-800">{{ $faq->links() }}</div>
        @endif
    </div>

@endsection
