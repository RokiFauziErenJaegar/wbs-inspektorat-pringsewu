@extends('layouts.app')

@section('judul', 'Dasbor')
@section('subjudul', 'Ringkasan pengaduan yang Anda sampaikan')

@section('konten')

    {{-- Sambutan --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-brand-700 via-brand-800 to-ink-950 p-7 sm:p-9">
        <div class="bg-grid absolute inset-0 opacity-20"></div>
        <div class="absolute -top-20 -right-16 size-64 rounded-full bg-gold-400/15 blur-3xl"></div>

        <div class="relative flex flex-wrap items-center justify-between gap-6">
            <div class="max-w-xl">
                <p class="text-sm font-medium text-white/60">
                    {{ now()->translatedFormat('l, d F Y') }}
                </p>
                <h2 class="mt-1.5 text-2xl font-extrabold tracking-tight text-white sm:text-3xl">
                    Selamat datang, {{ auth()->user()->panggilan }}!
                </h2>
                <p class="mt-2.5 text-sm/6 text-white/70">
                    @if (auth()->user()->opd)
                        Anda terdaftar sebagai pelapor dari <strong class="text-white">{{ auth()->user()->opd->nama }}</strong>.
                    @endif
                    Setiap pengaduan yang Anda kirim akan ditangani langsung oleh Inspektorat Kabupaten Pringsewu.
                </p>

                <div class="mt-6 flex flex-wrap gap-3">
                    <a href="{{ route('pelapor.laporan.pilih-kategori') }}" class="btn btn-gold">
                        <x-ikon nama="plus" class="size-4"/> Buat Pengaduan Baru
                    </a>
                    <a href="{{ route('pelapor.laporan.index') }}" class="btn border border-white/20 bg-white/10 text-white backdrop-blur hover:bg-white/20">
                        <x-ikon nama="list" class="size-4"/> Pengaduan Saya
                    </a>
                </div>
            </div>

            <div class="hidden shrink-0 sm:block">
                <span class="grid size-28 place-items-center rounded-3xl bg-white/10 text-white ring-1 ring-white/15 backdrop-blur">
                    <x-ikon nama="shield-check" class="size-14"/>
                </span>
            </div>
        </div>
    </div>

    {{-- Statistik --}}
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-kartu-statistik label="Total Pengaduan" :nilai="$statistik['total']" ikon="inbox" warna="brand"
                           :tautan="route('pelapor.laporan.index')" />
        <x-kartu-statistik label="Menunggu Verifikasi" :nilai="$statistik['menunggu']" ikon="clock" warna="gold"
                           :tautan="route('pelapor.laporan.index', ['status' => 'terkirim'])" />
        <x-kartu-statistik label="Dalam Tindak Lanjut" :nilai="$statistik['diproses']" ikon="refresh" warna="indigo"
                           :tautan="route('pelapor.laporan.index', ['status' => 'diproses'])" />
        <x-kartu-statistik label="Selesai" :nilai="$statistik['selesai']" ikon="check-badge" warna="brand"
                           :tautan="route('pelapor.laporan.index', ['status' => 'selesai'])" />
    </div>

    {{-- Pesan belum dibaca --}}
    @if ($perluDibaca->isNotEmpty())
        <div class="card overflow-hidden border-gold-300 dark:border-gold-800/60">
            <div class="flex items-center gap-3 border-b border-gold-200 bg-gold-50 px-5 py-3.5 dark:border-gold-900/50 dark:bg-gold-950/25">
                <span class="grid size-9 place-items-center rounded-xl bg-gold-400 text-ink-900">
                    <x-ikon nama="chat" class="size-[18px]"/>
                </span>
                <div>
                    <p class="text-sm font-bold text-ink-900 dark:text-white">Ada pesan baru dari Inspektorat</p>
                    <p class="text-xs text-ink-600 dark:text-ink-300">Segera tanggapi agar penanganan tidak tertunda.</p>
                </div>
            </div>

            <div class="divide-y divide-ink-100 dark:divide-ink-800">
                @foreach ($perluDibaca as $item)
                    <a href="{{ route('pelapor.laporan.show', $item) }}" class="flex items-center gap-4 px-5 py-3.5 transition hover:bg-ink-50 dark:hover:bg-ink-800/40">
                        <span class="grid size-9 shrink-0 place-items-center rounded-lg bg-rose-500 text-xs font-bold text-white">
                            {{ $item->unread_pelapor }}
                        </span>
                        <span class="min-w-0 flex-1">
                            <span class="block truncate text-sm font-semibold text-ink-900 dark:text-white">{{ $item->judul }}</span>
                            <span class="block font-mono text-xs text-ink-500 dark:text-ink-400">{{ $item->nomor_tiket }}</span>
                        </span>
                        <x-ikon nama="chevron-right" class="size-4 shrink-0 text-ink-400"/>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    <div class="grid gap-5 lg:grid-cols-3">

        {{-- Tren --}}
        <div class="card p-6 lg:col-span-2">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h3 class="text-base font-bold text-ink-900 dark:text-white">Tren Pengaduan</h3>
                    <p class="mt-0.5 text-xs text-ink-500 dark:text-ink-400">Jumlah pengaduan yang Anda buat dalam 6 bulan terakhir</p>
                </div>
                <span class="grid size-10 place-items-center rounded-xl bg-brand-100 text-brand-700 dark:bg-brand-900/40 dark:text-brand-300">
                    <x-ikon nama="trending-up" class="size-5"/>
                </span>
            </div>

            @php
                $grafikTren = [
                    'label' => $tren->pluck('label'),
                    'seri' => [['nama' => 'Pengaduan', 'data' => $tren->pluck('jumlah')]],
                ];
            @endphp

            <div class="mt-6 h-64">
                <canvas data-grafik="garis" data-konfig='@json($grafikTren)'></canvas>
            </div>
        </div>

        {{-- Komposisi status --}}
        <div class="card p-6">
            <h3 class="text-base font-bold text-ink-900 dark:text-white">Komposisi Status</h3>
            <p class="mt-0.5 text-xs text-ink-500 dark:text-ink-400">Sebaran seluruh pengaduan Anda</p>

            @if ($statistik['total'] > 0)
                @php
                    $grafikStatus = [
                        'label' => collect(\App\Models\Laporan::STATUS)->values(),
                        'data' => collect(\App\Models\Laporan::STATUS)->keys()->map(fn ($s) => $perStatus[$s] ?? 0),
                        'warna' => ['#94a3b8', '#f7b027', '#0ea5e9', '#6366f1', '#12825b', '#f43f5e'],
                    ];
                @endphp

                <div class="mt-6 h-56">
                    <canvas data-grafik="donat" data-konfig='@json($grafikStatus)'></canvas>
                </div>
            @else
                <x-kosong ikon="chart" judul="Belum ada data" pesan="Grafik akan muncul setelah Anda mengirim pengaduan." class="!py-12" />
            @endif
        </div>
    </div>

    {{-- Pengaduan terbaru --}}
    <div class="card overflow-hidden">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-ink-100 px-5 py-4 dark:border-ink-800">
            <div>
                <h3 class="text-base font-bold text-ink-900 dark:text-white">Pengaduan Terbaru</h3>
                <p class="mt-0.5 text-xs text-ink-500 dark:text-ink-400">Lima pengaduan terakhir yang Anda buat</p>
            </div>
            <a href="{{ route('pelapor.laporan.index') }}" class="btn btn-outline btn-sm">
                Lihat semua <x-ikon nama="arrow-right" class="size-3.5"/>
            </a>
        </div>

        @if ($terbaru->isEmpty())
            <x-kosong ikon="inbox" judul="Belum ada pengaduan"
                      pesan="Mulai sampaikan dugaan pelanggaran yang Anda ketahui. Identitas Anda kami lindungi.">
                <a href="{{ route('pelapor.laporan.pilih-kategori') }}" class="btn btn-primary">
                    <x-ikon nama="plus" class="size-4"/> Buat Pengaduan
                </a>
            </x-kosong>
        @else
            <div class="overflow-x-auto">
                <table class="tabel">
                    <thead>
                        <tr>
                            <th>Nomor Tiket</th>
                            <th>Judul</th>
                            <th class="hidden md:table-cell">Kategori</th>
                            <th>Status</th>
                            <th class="hidden sm:table-cell">Tanggal</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($terbaru as $item)
                            <tr>
                                <td class="font-mono text-xs font-semibold text-brand-700 dark:text-brand-400">
                                    {{ $item->nomor_tiket }}
                                </td>
                                <td class="max-w-xs">
                                    <span class="line-clamp-1 font-semibold text-ink-900 dark:text-white">{{ $item->judul }}</span>
                                </td>
                                <td class="hidden md:table-cell">
                                    <span class="chip bg-ink-100 text-ink-600 dark:bg-ink-800 dark:text-ink-300">{{ $item->kategori->nama }}</span>
                                </td>
                                <td><x-status :laporan="$item" /></td>
                                <td class="hidden text-xs whitespace-nowrap sm:table-cell">
                                    {{ $item->created_at->translatedFormat('d M Y') }}
                                </td>
                                <td class="text-right">
                                    <a href="{{ route('pelapor.laporan.show', $item) }}" class="btn btn-ghost btn-sm">
                                        <x-ikon nama="eye" class="size-3.5"/> Detail
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

@endsection
