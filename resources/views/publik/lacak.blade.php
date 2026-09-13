@extends('layouts.publik')

@section('judul', 'Lacak Aduan')

@section('konten')

    <section class="relative -mt-18 overflow-hidden bg-gradient-to-br from-brand-900 via-brand-950 to-ink-950 pt-32 pb-24">
        <div class="bg-grid absolute inset-0 opacity-20"></div>
        <div class="absolute -top-24 left-1/4 size-96 rounded-full bg-brand-500/15 blur-3xl"></div>

        <div class="relative mx-auto max-w-2xl px-4 text-center sm:px-6">
            <span class="badge bg-white/10 text-brand-200 ring-white/20 backdrop-blur">
                <x-ikon nama="search" class="size-3.5"/> Pelacakan Status
            </span>
            <h1 class="mt-5 text-4xl font-extrabold tracking-tight text-white sm:text-5xl">Lacak Pengaduan Anda</h1>
            <p class="mt-4 text-base/7 text-white/70">
                Masukkan nomor tiket dan kode akses yang Anda terima saat mengirim pengaduan
                untuk melihat perkembangan penanganannya.
            </p>
        </div>
    </section>

    <section class="mx-auto -mt-14 max-w-3xl px-4 pb-24 sm:px-6 lg:px-8">

        <div class="card relative z-10 p-6 sm:p-8">
            <form method="POST" action="{{ route('lacak.cari') }}" class="space-y-5">
                @csrf

                <div class="grid gap-5 sm:grid-cols-2">
                    <x-input nama="nomor_tiket" label="Nomor Tiket" wajib ikon="tag"
                             placeholder="WBS-20260910-0001" class="font-mono uppercase" />
                    <x-input nama="kode_akses" label="Kode Akses" wajib ikon="key"
                             placeholder="A1B2C3D4" class="font-mono uppercase" />
                </div>

                <button class="btn btn-primary btn-lg w-full">
                    <x-ikon nama="search" class="size-5"/> Lacak Pengaduan
                </button>
            </form>

            <div class="mt-5 flex items-start gap-2 rounded-xl bg-ink-50 px-4 py-3 text-xs/6 text-ink-500 dark:bg-ink-800/50 dark:text-ink-400">
                <x-ikon nama="info" class="mt-0.5 size-4 shrink-0 text-brand-500"/>
                <p>
                    Nomor tiket dan kode akses ditampilkan pada halaman rincian pengaduan setelah Anda mengirimkannya.
                    Pelapor yang memiliki akun dapat langsung memantau status melalui menu <strong>Pengaduan Saya</strong>.
                </p>
            </div>
        </div>

        {{-- Hasil pencarian --}}
        @if (! empty($dicari))
            @if (! $laporan)
                <div class="card mt-6">
                    <x-kosong ikon="search" judul="Pengaduan tidak ditemukan"
                              pesan="Periksa kembali nomor tiket dan kode akses Anda. Pastikan tidak ada spasi berlebih dan huruf ditulis dengan benar." />
                </div>
            @else
                <div class="card mt-6 overflow-hidden">
                    <div class="border-b border-ink-100 bg-gradient-to-r from-brand-50 to-transparent px-6 py-5 dark:border-ink-800 dark:from-brand-950/30">
                        <div class="flex flex-wrap items-start justify-between gap-4">
                            <div class="min-w-0">
                                <p class="font-mono text-sm font-bold text-brand-700 dark:text-brand-400">{{ $laporan->nomor_tiket }}</p>
                                <h2 class="mt-1 text-lg leading-snug font-bold text-ink-900 dark:text-white">{{ $laporan->judul }}</h2>
                                <p class="mt-1.5 text-xs text-ink-500 dark:text-ink-400">
                                    {{ $laporan->kategori->nama }} &middot; Dikirim {{ $laporan->submitted_at?->translatedFormat('d F Y') }}
                                </p>
                            </div>
                            <x-status :laporan="$laporan" />
                        </div>

                        <div class="mt-5 space-y-1.5">
                            <div class="flex justify-between text-xs font-semibold text-ink-500 dark:text-ink-400">
                                <span>Kemajuan penanganan</span>
                                <span class="text-brand-700 dark:text-brand-400">{{ $laporan->progres }}%</span>
                            </div>
                            <div class="h-2 overflow-hidden rounded-full bg-ink-200 dark:bg-ink-800">
                                <div class="h-full rounded-full bg-gradient-to-r from-brand-500 to-brand-700 transition-all duration-700"
                                     style="width: {{ $laporan->progres }}%"></div>
                            </div>
                        </div>
                    </div>

                    <div class="p-6">
                        <h3 class="text-sm font-bold text-ink-900 dark:text-white">Riwayat Penanganan</h3>

                        <ol class="mt-5 space-y-6">
                            @forelse ($laporan->tindakLanjut->sortByDesc('created_at') as $riwayat)
                                <li class="relative flex gap-4 pb-6 last:pb-0">
                                    <span class="absolute top-10 bottom-0 left-[19px] w-px bg-ink-200 dark:bg-ink-800"></span>
                                    <span class="relative grid size-10 shrink-0 place-items-center rounded-xl bg-brand-100 text-brand-700 dark:bg-brand-900/40 dark:text-brand-300">
                                        <x-ikon :nama="$riwayat->tampilan['icon']" class="size-[18px]"/>
                                    </span>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-bold text-ink-900 dark:text-white">{{ $riwayat->judul }}</p>
                                        @if ($riwayat->catatan)
                                            <p class="mt-1 text-sm/6 text-ink-600 dark:text-ink-300">{{ $riwayat->catatan }}</p>
                                        @endif
                                        <p class="mt-1.5 text-xs text-ink-400">
                                            {{ $riwayat->created_at->translatedFormat('d F Y, H:i') }} WIB
                                        </p>
                                    </div>
                                </li>
                            @empty
                                <li class="text-sm text-ink-500 dark:text-ink-400">Belum ada riwayat penanganan.</li>
                            @endforelse
                        </ol>

                        @if ($laporan->status === \App\Models\Laporan::STATUS_SELESAI && $laporan->kesimpulan)
                            <div class="mt-6 rounded-2xl border border-emerald-200 bg-emerald-50 p-5 dark:border-emerald-900/60 dark:bg-emerald-950/25">
                                <p class="flex items-center gap-2 text-sm font-bold text-emerald-800 dark:text-emerald-300">
                                    <x-ikon nama="check-badge" class="size-4"/> Kesimpulan
                                </p>
                                <p class="mt-2 text-sm/6 text-emerald-900 dark:text-emerald-200">{{ $laporan->kesimpulan }}</p>
                            </div>
                        @endif

                        @if ($laporan->status === \App\Models\Laporan::STATUS_DITOLAK && $laporan->alasan_penolakan)
                            <div class="mt-6 rounded-2xl border border-rose-200 bg-rose-50 p-5 dark:border-rose-900/60 dark:bg-rose-950/25">
                                <p class="flex items-center gap-2 text-sm font-bold text-rose-800 dark:text-rose-300">
                                    <x-ikon nama="x-circle" class="size-4"/> Alasan tidak dapat ditindaklanjuti
                                </p>
                                <p class="mt-2 text-sm/6 text-rose-900 dark:text-rose-200">{{ $laporan->alasan_penolakan }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        @endif
    </section>

@endsection
