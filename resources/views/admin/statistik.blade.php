@extends('layouts.app')

@section('judul', 'Statistik & Rekapitulasi')
@section('subjudul', 'Analisis penanganan pengaduan tahun '.$tahun)

@section('konten')

    {{-- Penyaring tahun --}}
    <div class="card flex flex-wrap items-center justify-between gap-4 p-5">
        <form method="GET" class="flex items-center gap-3">
            <label for="tahun" class="text-sm font-semibold text-ink-700 dark:text-ink-200">Tahun Anggaran</label>
            <select name="tahun" id="tahun" onchange="this.form.submit()" class="field w-32">
                @foreach ($tahunTersedia as $t)
                    <option value="{{ $t }}" @selected($tahun == $t)>{{ $t }}</option>
                @endforeach
            </select>
        </form>

        <a href="{{ route('admin.statistik.ekspor', ['tahun' => $tahun]) }}" class="btn btn-primary btn-sm">
            <x-ikon nama="download" class="size-4"/> Unduh Rekap CSV
        </a>
    </div>

    {{-- Ringkasan --}}
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-kartu-statistik label="Total Pengaduan" :nilai="$ringkasan['total']" ikon="inbox" warna="brand"
                           keterangan="Sepanjang tahun {{ $tahun }}" />
        <x-kartu-statistik label="Potensi Kerugian" nilai="Rp {{ number_format((float) $ringkasan['kerugian'], 0, ',', '.') }}"
                           ikon="banknote" warna="rose" keterangan="Akumulasi laporan masuk" />
        <x-kartu-statistik label="Rata-rata Penyelesaian"
                           nilai="{{ $ringkasan['rataHari'] !== null ? $ringkasan['rataHari'].' hari' : '—' }}"
                           ikon="clock" warna="gold" keterangan="Dari kirim hingga selesai" />
        <x-kartu-statistik label="Pengaduan Anonim" :nilai="$ringkasan['anonim']" ikon="lock" warna="slate"
                           keterangan="{{ $ringkasan['total'] > 0 ? round($ringkasan['anonim'] / $ringkasan['total'] * 100) : 0 }}% dari total" />
    </div>

    <div class="grid gap-5 lg:grid-cols-3">

        {{-- Grafik bulanan --}}
        <div class="card p-6 lg:col-span-2">
            <h3 class="text-base font-bold text-ink-900 dark:text-white">Rekapitulasi Bulanan {{ $tahun }}</h3>
            <p class="mt-0.5 text-xs text-ink-500 dark:text-ink-400">Perbandingan pengaduan masuk dengan yang diselesaikan</p>

            @php
                $grafikBulanan = [
                    'label' => $perBulan->pluck('label'),
                    'seri' => [
                        ['nama' => 'Masuk', 'data' => $perBulan->pluck('masuk')],
                        ['nama' => 'Selesai', 'data' => $perBulan->pluck('selesai')],
                    ],
                ];
            @endphp

            <div class="mt-6 h-72">
                <canvas data-grafik="garis" data-konfig='@json($grafikBulanan)'></canvas>
            </div>
        </div>

        {{-- Prioritas --}}
        <div class="card p-6">
            <h3 class="text-base font-bold text-ink-900 dark:text-white">Sebaran Prioritas</h3>
            <p class="mt-0.5 text-xs text-ink-500 dark:text-ink-400">Tingkat urgensi pengaduan</p>

            @if ($ringkasan['total'] > 0)
                @php
                    $grafikPrioritas = [
                        'label' => array_values(\App\Models\Laporan::PRIORITAS),
                        'data' => collect(array_keys(\App\Models\Laporan::PRIORITAS))->map(fn ($p) => $perPrioritas[$p] ?? 0),
                        'warna' => ['#94a3b8', '#0ea5e9', '#f7b027', '#f43f5e'],
                    ];
                @endphp

                <div class="mt-6 h-64">
                    <canvas data-grafik="donat" data-konfig='@json($grafikPrioritas)'></canvas>
                </div>
            @else
                <x-kosong ikon="chart" judul="Belum ada data" class="!py-14" />
            @endif
        </div>
    </div>

    {{-- Tabel rekap bulanan --}}
    <div class="card overflow-hidden">
        <div class="border-b border-ink-100 px-5 py-4 dark:border-ink-800">
            <h3 class="text-base font-bold text-ink-900 dark:text-white">Tabel Rekapitulasi Bulanan</h3>
            <p class="mt-0.5 text-xs text-ink-500 dark:text-ink-400">
                Kolom <em>Dituntaskan</em> menghitung berkas yang selesai pada bulan tersebut, termasuk berkas dari bulan sebelumnya.
                Kolom <em>Capaian</em> menghitung berapa berkas masuk bulan itu yang kini sudah selesai.
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="tabel">
                <thead>
                    <tr>
                        <th>Bulan</th>
                        <th class="text-right">Pengaduan Masuk</th>
                        <th class="text-right">Dituntaskan</th>
                        <th class="text-right">Capaian Berkas Bulan Ini</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($perBulan as $baris)
                        <tr>
                            <td class="font-semibold text-ink-900 dark:text-white">{{ $baris['label'] }}</td>
                            <td class="text-right tabular-nums">{{ $baris['masuk'] }}</td>
                            <td class="text-right tabular-nums">{{ $baris['selesai'] }}</td>
                            <td class="text-right tabular-nums">
                                @if ($baris['masuk'] > 0)
                                    {{ $baris['tuntas'] }} / {{ $baris['masuk'] }}
                                    <span class="ml-1 font-semibold text-brand-700 dark:text-brand-400">
                                        ({{ round($baris['tuntas'] / $baris['masuk'] * 100) }}%)
                                    </span>
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    @php $totalMasuk = $perBulan->sum('masuk'); @endphp
                    <tr class="bg-ink-50 font-bold dark:bg-ink-800/40">
                        <td class="px-4 py-3 text-ink-900 dark:text-white">Jumlah</td>
                        <td class="px-4 py-3 text-right tabular-nums text-ink-900 dark:text-white">{{ $totalMasuk }}</td>
                        <td class="px-4 py-3 text-right tabular-nums text-ink-900 dark:text-white">{{ $perBulan->sum('selesai') }}</td>
                        <td class="px-4 py-3 text-right tabular-nums text-ink-900 dark:text-white">
                            @if ($totalMasuk > 0)
                                {{ $perBulan->sum('tuntas') }} / {{ $totalMasuk }}
                                <span class="ml-1 text-brand-700 dark:text-brand-400">
                                    ({{ round($perBulan->sum('tuntas') / $totalMasuk * 100) }}%)
                                </span>
                            @else
                                —
                            @endif
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="grid gap-5 lg:grid-cols-2">

        {{-- Kategori --}}
        <div class="card p-6">
            <h3 class="text-base font-bold text-ink-900 dark:text-white">Pengaduan per Kategori</h3>

            @if ($perKategori->sum('laporan_count') === 0)
                <x-kosong ikon="tag" judul="Belum ada data" class="!py-12" />
            @else
                @php $maksKategori = max($perKategori->max('laporan_count'), 1); @endphp
                <div class="mt-5 space-y-4">
                    @foreach ($perKategori as $item)
                        <div>
                            <div class="flex items-center justify-between gap-3 text-xs">
                                <span class="flex items-center gap-2 truncate font-semibold text-ink-700 dark:text-ink-200">
                                    <x-ikon :nama="$item->icon" class="size-3.5 shrink-0 text-brand-500"/>
                                    {{ $item->nama }}
                                </span>
                                <span class="shrink-0 font-bold text-brand-700 tabular-nums dark:text-brand-400">{{ $item->laporan_count }}</span>
                            </div>
                            <div class="mt-1.5 h-2 overflow-hidden rounded-full bg-ink-100 dark:bg-ink-800">
                                <div class="h-full rounded-full bg-gradient-to-r from-brand-400 to-brand-600"
                                     style="width: {{ round($item->laporan_count / $maksKategori * 100) }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- OPD --}}
        <div class="card p-6">
            <h3 class="text-base font-bold text-ink-900 dark:text-white">10 OPD Paling Banyak Dilaporkan</h3>

            @if ($perOpd->isEmpty())
                <x-kosong ikon="building" judul="Belum ada data" class="!py-12" />
            @else
                @php
                    $grafikOpd = [
                        'label' => $perOpd->pluck('nama_singkat'),
                        'data' => $perOpd->pluck('laporan_count'),
                        'horizontal' => true,
                    ];
                @endphp

                <div class="mt-5 h-80">
                    <canvas data-grafik="batang" data-konfig='@json($grafikOpd)'></canvas>
                </div>
            @endif
        </div>
    </div>

    {{-- Kinerja petugas --}}
    @if ($kinerjaPetugas->isNotEmpty())
        <div class="card overflow-hidden">
            <div class="border-b border-ink-100 px-5 py-4 dark:border-ink-800">
                <h3 class="text-base font-bold text-ink-900 dark:text-white">Kinerja Petugas Inspektorat</h3>
                <p class="mt-0.5 text-xs text-ink-500 dark:text-ink-400">Rekap berkas yang ditangani sepanjang tahun {{ $tahun }}</p>
            </div>

            <div class="overflow-x-auto">
                <table class="tabel">
                    <thead>
                        <tr>
                            <th>Petugas</th>
                            <th class="hidden sm:table-cell">Jabatan</th>
                            <th class="text-right">Ditangani</th>
                            <th class="text-right">Selesai</th>
                            <th class="w-40">Capaian</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($kinerjaPetugas as $orang)
                            @php $capaian = $orang->total > 0 ? round($orang->selesai / $orang->total * 100) : 0; @endphp
                            <tr>
                                <td>
                                    <span class="flex items-center gap-2.5">
                                        <span class="grid size-8 shrink-0 place-items-center rounded-lg bg-gradient-to-br from-brand-500 to-brand-700 text-[10px] font-bold text-white">
                                            {{ $orang->inisial }}
                                        </span>
                                        <span class="font-semibold text-ink-900 dark:text-white">{{ $orang->name }}</span>
                                    </span>
                                </td>
                                <td class="hidden text-xs sm:table-cell">{{ $orang->jabatan }}</td>
                                <td class="text-right font-bold tabular-nums">{{ $orang->total }}</td>
                                <td class="text-right font-bold text-emerald-600 tabular-nums dark:text-emerald-400">{{ $orang->selesai }}</td>
                                <td>
                                    <span class="flex items-center gap-2">
                                        <span class="h-2 flex-1 overflow-hidden rounded-full bg-ink-100 dark:bg-ink-800">
                                            <span class="block h-full rounded-full bg-gradient-to-r from-brand-400 to-brand-600" style="width: {{ $capaian }}%"></span>
                                        </span>
                                        <span class="w-9 shrink-0 text-right text-xs font-bold tabular-nums">{{ $capaian }}%</span>
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

@endsection
