@extends('layouts.app')

@section('judul', 'Dasbor Inspektorat')
@section('subjudul', 'Ringkasan penanganan pengaduan Whistleblowing System')

@section('konten')

    {{-- Sambutan --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-brand-700 via-brand-800 to-ink-950 p-7 sm:p-9">
        <div class="bg-grid absolute inset-0 opacity-20"></div>
        <div class="absolute -top-20 -right-16 size-64 rounded-full bg-gold-400/15 blur-3xl"></div>

        <div class="relative flex flex-wrap items-end justify-between gap-6">
            <div class="max-w-xl">
                <p class="text-sm font-medium text-white/60">{{ now()->translatedFormat('l, d F Y') }}</p>
                <h2 class="mt-1.5 text-2xl font-extrabold tracking-tight text-white sm:text-3xl">
                    Halo, {{ auth()->user()->panggilan }}
                </h2>
                <p class="mt-2.5 text-sm/6 text-white/70">
                    @if ($statistik['baru'] > 0)
                        Terdapat <strong class="text-gold-300">{{ $statistik['baru'] }} pengaduan baru</strong> yang menunggu verifikasi Anda.
                    @else
                        Seluruh pengaduan telah diverifikasi. Terima kasih atas kerja pengawasan Anda.
                    @endif
                </p>

                <div class="mt-6 flex flex-wrap gap-3">
                    <a href="{{ route('admin.laporan.index', ['status' => 'terkirim']) }}" class="btn btn-gold">
                        <x-ikon nama="inbox" class="size-4"/> Tinjau Pengaduan Baru
                    </a>
                    <a href="{{ route('admin.statistik') }}" class="btn border border-white/20 bg-white/10 text-white backdrop-blur hover:bg-white/20">
                        <x-ikon nama="chart" class="size-4"/> Lihat Statistik
                    </a>
                </div>
            </div>

            {{-- Tingkat penyelesaian --}}
            <div class="rounded-2xl border border-white/15 bg-white/10 p-5 backdrop-blur">
                <p class="text-xs font-semibold text-white/60">Tingkat Penyelesaian</p>
                <p class="mt-1 text-4xl font-extrabold text-white tabular-nums">{{ $statistik['penyelesaian'] }}%</p>
                <div class="mt-3 h-1.5 w-40 overflow-hidden rounded-full bg-white/15">
                    <div class="h-full rounded-full bg-gradient-to-r from-brand-300 to-gold-300" style="width: {{ $statistik['penyelesaian'] }}%"></div>
                </div>
                <p class="mt-2 text-[11px] text-white/50">{{ $statistik['selesai'] }} dari {{ $statistik['total'] }} pengaduan</p>
            </div>
        </div>
    </div>

    {{-- Statistik --}}
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-kartu-statistik label="Menunggu Verifikasi" :nilai="$statistik['baru']" ikon="clock" warna="gold"
                           keterangan="Perlu ditinjau segera"
                           :tautan="route('admin.laporan.index', ['status' => 'terkirim'])" />
        <x-kartu-statistik label="Sedang Diverifikasi" :nilai="$statistik['verifikasi']" ikon="shield-check" warna="sky"
                           :tautan="route('admin.laporan.index', ['status' => 'verifikasi'])" />
        <x-kartu-statistik label="Dalam Tindak Lanjut" :nilai="$statistik['diproses']" ikon="refresh" warna="indigo"
                           :tautan="route('admin.laporan.index', ['status' => 'diproses'])" />
        <x-kartu-statistik label="Selesai Ditangani" :nilai="$statistik['selesai']" ikon="check-badge" warna="brand"
                           :tautan="route('admin.laporan.index', ['status' => 'selesai'])" />
    </div>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-kartu-statistik label="Total Pengaduan" :nilai="$statistik['total']" ikon="inbox" warna="slate"
                           :tautan="route('admin.laporan.index')" />
        <x-kartu-statistik label="Tidak Ditindaklanjuti" :nilai="$statistik['ditolak']" ikon="x-circle" warna="rose"
                           :tautan="route('admin.laporan.index', ['status' => 'ditolak'])" />
        <x-kartu-statistik label="Potensi Kerugian" nilai="Rp {{ number_format((float) $statistik['kerugian'], 0, ',', '.') }}"
                           ikon="banknote" warna="gold" keterangan="Akumulasi seluruh pengaduan" />
        <x-kartu-statistik label="Pelapor Terdaftar" :nilai="$statistik['pelapor']" ikon="users" warna="violet"
                           keterangan="{{ $statistik['opd'] }} perangkat daerah aktif" />
    </div>

    <div class="grid gap-5 lg:grid-cols-3">

        {{-- Tren tahunan --}}
        <div class="card p-6 lg:col-span-2">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <h3 class="text-base font-bold text-ink-900 dark:text-white">Tren Pengaduan 12 Bulan Terakhir</h3>
                    <p class="mt-0.5 text-xs text-ink-500 dark:text-ink-400">Perbandingan pengaduan masuk dan yang berhasil diselesaikan</p>
                </div>
                <span class="grid size-10 place-items-center rounded-xl bg-brand-100 text-brand-700 dark:bg-brand-900/40 dark:text-brand-300">
                    <x-ikon nama="trending-up" class="size-5"/>
                </span>
            </div>

            @php
                $grafikTren = [
                    'label' => $tren->pluck('label'),
                    'seri' => [
                        ['nama' => 'Masuk', 'data' => $tren->pluck('masuk')],
                        ['nama' => 'Selesai', 'data' => $tren->pluck('selesai')],
                    ],
                ];
            @endphp

            <div class="mt-6 h-72">
                <canvas data-grafik="garis" data-konfig='@json($grafikTren)'></canvas>
            </div>
        </div>

        {{-- Status --}}
        <div class="card p-6">
            <h3 class="text-base font-bold text-ink-900 dark:text-white">Sebaran Status</h3>
            <p class="mt-0.5 text-xs text-ink-500 dark:text-ink-400">Kondisi seluruh berkas pengaduan</p>

            @if ($statistik['total'] > 0)
                @php
                    $grafikStatus = [
                        'label' => ['Menunggu', 'Verifikasi', 'Tindak Lanjut', 'Selesai', 'Ditolak'],
                        'data' => [
                            $perStatus['terkirim'] ?? 0,
                            $perStatus['verifikasi'] ?? 0,
                            $perStatus['diproses'] ?? 0,
                            $perStatus['selesai'] ?? 0,
                            $perStatus['ditolak'] ?? 0,
                        ],
                        'warna' => ['#f7b027', '#0ea5e9', '#6366f1', '#12825b', '#f43f5e'],
                    ];
                @endphp

                <div class="mt-6 h-64">
                    <canvas data-grafik="donat" data-konfig='@json($grafikStatus)'></canvas>
                </div>
            @else
                <x-kosong ikon="chart" judul="Belum ada data" class="!py-14" />
            @endif
        </div>
    </div>

    <div class="grid gap-5 lg:grid-cols-2">

        {{-- Antrian verifikasi --}}
        <div class="card overflow-hidden">
            <div class="flex items-center justify-between gap-3 border-b border-ink-100 px-5 py-4 dark:border-ink-800">
                <div>
                    <h3 class="text-base font-bold text-ink-900 dark:text-white">Antrean Verifikasi</h3>
                    <p class="mt-0.5 text-xs text-ink-500 dark:text-ink-400">Diurutkan dari yang paling lama menunggu</p>
                </div>
                <a href="{{ route('admin.laporan.index', ['status' => 'terkirim']) }}" class="btn btn-outline btn-sm">
                    Semua <x-ikon nama="arrow-right" class="size-3.5"/>
                </a>
            </div>

            @if ($antrian->isEmpty())
                <x-kosong ikon="check-badge" judul="Antrean kosong"
                          pesan="Semua pengaduan yang masuk sudah diverifikasi." class="!py-12" />
            @else
                <div class="divide-y divide-ink-100 dark:divide-ink-800">
                    @foreach ($antrian as $item)
                        <a href="{{ route('admin.laporan.show', $item) }}" class="flex items-start gap-3 px-5 py-3.5 transition hover:bg-ink-50 dark:hover:bg-ink-800/40">
                            <span class="grid size-10 shrink-0 place-items-center rounded-xl bg-gradient-to-br from-brand-500 to-brand-700 text-white">
                                <x-ikon :nama="$item->kategori->icon" class="size-5"/>
                            </span>

                            <span class="min-w-0 flex-1">
                                <span class="flex flex-wrap items-center gap-2">
                                    <span class="font-mono text-[11px] font-bold text-brand-700 dark:text-brand-400">{{ $item->nomor_tiket }}</span>
                                    <span class="chip {{ $item->prioritas_kelas }}">{{ $item->prioritas_label }}</span>
                                </span>
                                <span class="mt-1 block truncate text-sm font-semibold text-ink-900 dark:text-white">{{ $item->judul }}</span>
                                <span class="mt-0.5 block truncate text-xs text-ink-500 dark:text-ink-400">
                                    {{ $item->opd?->nama_singkat ?? $item->kategori->nama }} ·
                                    {{ $item->submitted_at?->diffForHumans() }}
                                </span>
                            </span>

                            <x-ikon nama="chevron-right" class="mt-2 size-4 shrink-0 text-ink-400"/>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Prioritas tinggi --}}
        <div class="card overflow-hidden">
            <div class="flex items-center justify-between gap-3 border-b border-ink-100 px-5 py-4 dark:border-ink-800">
                <div>
                    <h3 class="text-base font-bold text-ink-900 dark:text-white">Perlu Perhatian Khusus</h3>
                    <p class="mt-0.5 text-xs text-ink-500 dark:text-ink-400">Pengaduan berprioritas tinggi dan mendesak</p>
                </div>
                <span class="grid size-9 place-items-center rounded-xl bg-rose-100 text-rose-600 dark:bg-rose-900/40 dark:text-rose-300">
                    <x-ikon nama="alert" class="size-[18px]"/>
                </span>
            </div>

            @if ($prioritas->isEmpty())
                <x-kosong ikon="shield-check" judul="Tidak ada yang mendesak"
                          pesan="Belum ada pengaduan berprioritas tinggi yang belum tertangani." class="!py-12" />
            @else
                <div class="divide-y divide-ink-100 dark:divide-ink-800">
                    @foreach ($prioritas as $item)
                        <a href="{{ route('admin.laporan.show', $item) }}" class="flex items-start gap-3 px-5 py-3.5 transition hover:bg-ink-50 dark:hover:bg-ink-800/40">
                            <span class="mt-1 grid size-2.5 shrink-0 place-items-center rounded-full {{ $item->prioritas === 'mendesak' ? 'bg-rose-500' : 'bg-orange-400' }}"></span>

                            <span class="min-w-0 flex-1">
                                <span class="block truncate text-sm font-semibold text-ink-900 dark:text-white">{{ $item->judul }}</span>
                                <span class="mt-1 flex flex-wrap items-center gap-2">
                                    <x-status :laporan="$item" />
                                    <span class="text-xs text-ink-500 dark:text-ink-400">
                                        {{ $item->petugas?->name ?? 'Belum didisposisi' }}
                                    </span>
                                </span>
                            </span>

                            <x-ikon nama="chevron-right" class="mt-2 size-4 shrink-0 text-ink-400"/>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <div class="grid gap-5 lg:grid-cols-3">

        {{-- Kategori --}}
        <div class="card p-6 lg:col-span-2">
            <h3 class="text-base font-bold text-ink-900 dark:text-white">Pengaduan per Kategori</h3>
            <p class="mt-0.5 text-xs text-ink-500 dark:text-ink-400">Jenis dugaan pelanggaran yang paling banyak dilaporkan</p>

            @php
                $grafikKategori = [
                    'label' => $perKategori->pluck('nama'),
                    'data' => $perKategori->pluck('laporan_count'),
                    'horizontal' => true,
                ];
            @endphp

            <div class="mt-6 h-72">
                <canvas data-grafik="batang" data-konfig='@json($grafikKategori)'></canvas>
            </div>
        </div>

        {{-- OPD terbanyak --}}
        <div class="card p-6">
            <h3 class="text-base font-bold text-ink-900 dark:text-white">OPD Paling Banyak Dilaporkan</h3>

            @if ($perOpd->isEmpty())
                <x-kosong ikon="building" judul="Belum ada data" class="!py-12" />
            @else
                @php $maksimum = max($perOpd->max('laporan_count'), 1); @endphp
                <div class="mt-5 space-y-4">
                    @foreach ($perOpd as $item)
                        <div>
                            <div class="flex items-center justify-between gap-3 text-xs">
                                <span class="truncate font-semibold text-ink-700 dark:text-ink-200">{{ $item->nama_singkat }}</span>
                                <span class="shrink-0 font-bold text-brand-700 tabular-nums dark:text-brand-400">{{ $item->laporan_count }}</span>
                            </div>
                            <div class="mt-1.5 h-2 overflow-hidden rounded-full bg-ink-100 dark:bg-ink-800">
                                <div class="h-full rounded-full bg-gradient-to-r from-brand-400 to-brand-600"
                                     style="width: {{ round($item->laporan_count / $maksimum * 100) }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Jatuh tempo & kinerja --}}
    <div class="grid gap-5 lg:grid-cols-2">
        <div class="card overflow-hidden">
            <div class="flex items-center gap-2 border-b border-ink-100 px-5 py-4 dark:border-ink-800">
                <x-ikon nama="clock" class="size-5 text-gold-500"/>
                <h3 class="text-base font-bold text-ink-900 dark:text-white">Mendekati Jatuh Tempo</h3>
            </div>

            @if ($jatuhTempo->isEmpty())
                <x-kosong ikon="calendar" judul="Tidak ada tenggat terdekat"
                          pesan="Belum ada berkas yang mendekati batas waktu penanganan." class="!py-12" />
            @else
                <div class="divide-y divide-ink-100 dark:divide-ink-800">
                    @foreach ($jatuhTempo as $item)
                        @php $lewat = $item->deadline->isPast(); @endphp
                        <a href="{{ route('admin.laporan.show', $item) }}" class="flex items-center gap-3 px-5 py-3.5 transition hover:bg-ink-50 dark:hover:bg-ink-800/40">
                            <span class="grid size-10 shrink-0 place-items-center rounded-xl {{ $lewat ? 'bg-rose-100 text-rose-600 dark:bg-rose-900/40 dark:text-rose-300' : 'bg-gold-100 text-gold-700 dark:bg-gold-900/40 dark:text-gold-300' }}">
                                <x-ikon nama="calendar" class="size-5"/>
                            </span>
                            <span class="min-w-0 flex-1">
                                <span class="block truncate text-sm font-semibold text-ink-900 dark:text-white">{{ $item->judul }}</span>
                                <span class="block text-xs {{ $lewat ? 'font-bold text-rose-600 dark:text-rose-400' : 'text-ink-500 dark:text-ink-400' }}">
                                    {{ $lewat ? 'Terlambat ' : 'Tenggat ' }}{{ $item->deadline->diffForHumans() }}
                                    · {{ $item->petugas?->name ?? 'Belum didisposisi' }}
                                </span>
                            </span>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        @if ($kinerjaPetugas->isNotEmpty())
            <div class="card overflow-hidden">
                <div class="flex items-center gap-2 border-b border-ink-100 px-5 py-4 dark:border-ink-800">
                    <x-ikon nama="award" class="size-5 text-brand-500"/>
                    <h3 class="text-base font-bold text-ink-900 dark:text-white">Beban Kerja Petugas</h3>
                </div>

                <div class="divide-y divide-ink-100 dark:divide-ink-800">
                    @foreach ($kinerjaPetugas as $orang)
                        <div class="flex items-center gap-3 px-5 py-3.5">
                            <span class="grid size-10 shrink-0 place-items-center rounded-xl bg-gradient-to-br from-brand-500 to-brand-700 text-xs font-bold text-white">
                                {{ $orang->inisial }}
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-ink-900 dark:text-white">{{ $orang->name }}</p>
                                <p class="truncate text-xs text-ink-500 dark:text-ink-400">{{ $orang->jabatan }}</p>
                            </div>
                            <div class="shrink-0 text-right">
                                <p class="text-sm font-extrabold text-ink-900 tabular-nums dark:text-white">{{ $orang->laporan_ditangani_count }}</p>
                                <p class="text-[11px] text-emerald-600 dark:text-emerald-400">{{ $orang->selesai_count }} selesai</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

@endsection
