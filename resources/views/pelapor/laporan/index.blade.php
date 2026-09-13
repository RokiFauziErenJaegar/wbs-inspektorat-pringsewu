@extends('layouts.app')

@section('judul', 'Pengaduan Saya')
@section('subjudul', 'Seluruh pengaduan yang pernah Anda sampaikan')

@section('konten')

    {{-- Penyaring --}}
    <div class="card p-5">
        <form method="GET" class="space-y-4">
            <div class="flex flex-wrap gap-2">
                @php
                    $tabStatus = ['' => 'Semua'] + \App\Models\Laporan::STATUS;
                    $statusAktif = request('status', '');
                @endphp

                @foreach ($tabStatus as $nilai => $label)
                    <a href="{{ route('pelapor.laporan.index', array_filter(['status' => $nilai, 'q' => request('q'), 'kategori' => request('kategori')])) }}"
                       class="rounded-xl px-3.5 py-2 text-xs font-bold transition {{ (string) $statusAktif === (string) $nilai
                           ? 'bg-brand-600 text-white shadow-[var(--shadow-glow)]'
                           : 'bg-ink-100 text-ink-600 hover:bg-ink-200 dark:bg-ink-800 dark:text-ink-300 dark:hover:bg-ink-700' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            <div class="flex flex-col gap-3 sm:flex-row">
                <input type="hidden" name="status" value="{{ request('status') }}">

                <div class="relative flex-1">
                    <span class="pointer-events-none absolute inset-y-0 left-0 grid w-11 place-items-center text-ink-400">
                        <x-ikon nama="search" class="size-[18px]"/>
                    </span>
                    <input type="search" name="q" value="{{ request('q') }}"
                           placeholder="Cari judul, nomor tiket, atau isi pengaduan…" class="field pl-11">
                </div>

                <select name="kategori" class="field sm:max-w-60">
                    <option value="">Semua kategori</option>
                    @foreach ($kategoriList as $item)
                        <option value="{{ $item->id }}" @selected(request('kategori') == $item->id)>{{ $item->nama }}</option>
                    @endforeach
                </select>

                <div class="flex gap-2">
                    <button class="btn btn-primary"><x-ikon nama="filter" class="size-4"/> Saring</button>
                    @if (request()->hasAny(['q', 'kategori', 'status']))
                        <a href="{{ route('pelapor.laporan.index') }}" class="btn btn-outline">Atur Ulang</a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    {{-- Daftar --}}
    @if ($laporan->isEmpty())
        <div class="card">
            <x-kosong ikon="inbox"
                      judul="{{ request()->hasAny(['q', 'kategori', 'status']) ? 'Tidak ada pengaduan yang cocok' : 'Belum ada pengaduan' }}"
                      pesan="{{ request()->hasAny(['q', 'kategori', 'status'])
                          ? 'Coba ubah kata kunci atau atur ulang penyaring pencarian Anda.'
                          : 'Sampaikan dugaan pelanggaran yang Anda ketahui. Identitas Anda dijamin kerahasiaannya.' }}">
                <a href="{{ route('pelapor.laporan.pilih-kategori') }}" class="btn btn-primary">
                    <x-ikon nama="plus" class="size-4"/> Buat Pengaduan
                </a>
            </x-kosong>
        </div>
    @else
        <div class="space-y-4">
            @foreach ($laporan as $item)
                <div class="card card-hover overflow-hidden">
                    <div class="flex flex-col gap-4 p-5 sm:flex-row sm:items-start">

                        <span class="grid size-12 shrink-0 place-items-center rounded-2xl bg-gradient-to-br from-brand-500 to-brand-700 text-white shadow-lg shadow-brand-500/20">
                            <x-ikon :nama="$item->kategori->icon" class="size-6"/>
                        </span>

                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="font-mono text-xs font-bold text-brand-700 dark:text-brand-400">{{ $item->nomor_tiket }}</span>
                                <x-status :laporan="$item" />
                                @if ($item->is_anonymous)
                                    <span class="chip bg-ink-100 text-ink-600 dark:bg-ink-800 dark:text-ink-300">
                                        <x-ikon nama="lock" class="size-3"/> Anonim
                                    </span>
                                @endif
                                @if ($item->unread_pelapor > 0)
                                    <span class="chip bg-rose-500 text-white">
                                        <x-ikon nama="chat" class="size-3"/> {{ $item->unread_pelapor }} pesan baru
                                    </span>
                                @endif
                            </div>

                            <h3 class="mt-2 text-base leading-snug font-bold text-ink-900 dark:text-white">
                                <a href="{{ route('pelapor.laporan.show', $item) }}" class="transition hover:text-brand-700 dark:hover:text-brand-400">
                                    {{ $item->judul }}
                                </a>
                            </h3>

                            <p class="mt-1.5 line-clamp-2 text-sm/6 text-ink-500 dark:text-ink-400">
                                {{ Str::limit(strip_tags($item->uraian), 220) }}
                            </p>

                            <div class="mt-3 flex flex-wrap items-center gap-x-5 gap-y-1.5 text-xs text-ink-500 dark:text-ink-400">
                                <span class="flex items-center gap-1.5"><x-ikon nama="tag" class="size-3.5"/> {{ $item->kategori->nama }}</span>
                                @if ($item->opd)
                                    <span class="flex items-center gap-1.5"><x-ikon nama="building" class="size-3.5"/> {{ $item->opd->nama_singkat }}</span>
                                @endif
                                <span class="flex items-center gap-1.5"><x-ikon nama="calendar" class="size-3.5"/> {{ $item->created_at->translatedFormat('d M Y') }}</span>
                            </div>

                            {{-- Kemajuan --}}
                            <div class="mt-4 space-y-1.5">
                                <div class="h-1.5 overflow-hidden rounded-full bg-ink-100 dark:bg-ink-800">
                                    <div class="h-full rounded-full bg-gradient-to-r from-brand-500 to-brand-700 transition-all duration-700"
                                         style="width: {{ $item->progres }}%"></div>
                                </div>
                            </div>
                        </div>

                        <div class="flex shrink-0 gap-2 sm:flex-col">
                            <a href="{{ route('pelapor.laporan.show', $item) }}" class="btn btn-outline btn-sm">
                                <x-ikon nama="eye" class="size-3.5"/> Detail
                            </a>
                            @if ($item->isDraft())
                                <a href="{{ route('pelapor.laporan.edit', $item) }}" class="btn btn-primary btn-sm">
                                    <x-ikon nama="pencil" class="size-3.5"/> Lanjutkan
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div>{{ $laporan->links() }}</div>
    @endif

@endsection
