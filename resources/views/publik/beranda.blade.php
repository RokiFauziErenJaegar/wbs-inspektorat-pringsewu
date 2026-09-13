@extends('layouts.publik')

@section('judul', 'Beranda')

@section('konten')

    {{-- ============================================================= --}}
    {{-- Hero                                                           --}}
    {{-- ============================================================= --}}
    <section class="relative -mt-18 overflow-hidden bg-gradient-to-br from-brand-900 via-brand-950 to-ink-950 pt-32 pb-24 lg:pt-40 lg:pb-32">
        <div class="bg-grid absolute inset-0 opacity-20"></div>
        <div class="absolute -top-32 -right-24 size-[30rem] rounded-full bg-brand-500/20 blur-3xl"></div>
        <div class="absolute -bottom-40 -left-32 size-[28rem] rounded-full bg-gold-500/10 blur-3xl"></div>

        <div class="relative mx-auto grid max-w-7xl items-center gap-14 px-4 sm:px-6 lg:grid-cols-[1.1fr_1fr] lg:px-8">

            <div class="animate-fade-up">
                <span class="badge bg-white/10 text-brand-200 ring-white/20 backdrop-blur">
                    <span class="flex size-2">
                        <span class="absolute inline-flex size-2 animate-ping rounded-full bg-brand-300 opacity-75"></span>
                        <span class="relative inline-flex size-2 rounded-full bg-brand-300"></span>
                    </span>
                    Kanal resmi Inspektorat Kabupaten Pringsewu
                </span>

                <h1 class="mt-6 text-4xl leading-[1.1] font-extrabold tracking-tight text-white sm:text-5xl lg:text-6xl">
                    Laporkan dugaan pelanggaran,
                    <span class="teks-gradien">identitas Anda aman.</span>
                </h1>

                <p class="mt-6 max-w-xl text-base/7 text-white/70 sm:text-lg/8">
                    Whistleblowing System adalah sarana resmi bagi Organisasi Perangkat Daerah dan masyarakat
                    untuk melaporkan dugaan korupsi, gratifikasi, pungutan liar, dan penyimpangan lain
                    di lingkungan Pemerintah Kabupaten Pringsewu.
                </p>

                <div class="mt-9 flex flex-wrap items-center gap-3">
                    <a href="{{ route('register') }}" class="btn btn-gold btn-lg">
                        <x-ikon nama="megaphone" class="size-5"/> Sampaikan Pengaduan
                    </a>
                    <a href="{{ route('cara-melapor') }}" class="btn btn-lg border border-white/20 bg-white/5 text-white backdrop-blur hover:bg-white/10">
                        <x-ikon nama="book-open" class="size-5"/> Pelajari Alurnya
                    </a>
                </div>

                <div class="mt-10 flex flex-wrap items-center gap-x-7 gap-y-3 text-sm text-white/60">
                    @foreach ([['lock', 'Kerahasiaan terjamin'], ['shield-check', 'Ditangani Inspektorat'], ['activity', 'Status bisa dipantau']] as [$ikon, $teks])
                        <span class="flex items-center gap-2">
                            <x-ikon :nama="$ikon" class="size-4 text-brand-300"/> {{ $teks }}
                        </span>
                    @endforeach
                </div>
            </div>

            {{-- Kartu contoh pelacakan status --}}
            <div class="relative hidden lg:block">
                <div class="absolute -inset-6 rounded-[2rem] bg-gradient-to-br from-brand-400/20 to-gold-400/10 blur-2xl"></div>

                <div class="relative animate-float rounded-3xl border border-white/15 bg-white/10 p-6 shadow-2xl backdrop-blur-xl">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-[11px] font-bold tracking-widest text-white/50 uppercase">Nomor Tiket</p>
                            <p class="mt-1 font-mono text-lg font-bold text-white">WBS-{{ now()->format('Ymd') }}-0148</p>
                        </div>
                        <span class="badge bg-indigo-400/20 text-indigo-200 ring-indigo-300/30">
                            <x-ikon nama="refresh" class="size-3.5"/> Dalam Tindak Lanjut
                        </span>
                    </div>

                    <div class="mt-6 space-y-1.5">
                        <div class="flex justify-between text-xs text-white/60">
                            <span>Kemajuan penanganan</span>
                            <span class="font-bold text-white">75%</span>
                        </div>
                        <div class="h-2 overflow-hidden rounded-full bg-white/10">
                            <div class="h-full w-3/4 rounded-full bg-gradient-to-r from-brand-400 to-gold-300"></div>
                        </div>
                    </div>

                    <div class="mt-6 space-y-4">
                        @foreach ([
                            ['check-badge', 'Pengaduan diterima', 'Terverifikasi lengkap', true],
                            ['users', 'Disposisi tim auditor', 'Irban Wilayah I', true],
                            ['clipboard', 'Pengumpulan bahan keterangan', 'Sedang berjalan', false],
                        ] as [$ikon, $judul, $ket, $selesai])
                            <div class="flex gap-3">
                                <span class="grid size-8 shrink-0 place-items-center rounded-lg {{ $selesai ? 'bg-brand-400/20 text-brand-200' : 'bg-white/10 text-white/50' }}">
                                    <x-ikon :nama="$ikon" class="size-4"/>
                                </span>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-white">{{ $judul }}</p>
                                    <p class="text-xs text-white/50">{{ $ket }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-6 flex items-center gap-2 rounded-xl border border-white/10 bg-white/5 px-3 py-2.5 text-xs text-white/60">
                        <x-ikon nama="lock" class="size-4 shrink-0 text-brand-300"/>
                        Identitas pelapor hanya dapat diakses petugas berwenang.
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================================= --}}
    {{-- Statistik                                                      --}}
    {{-- ============================================================= --}}
    <section class="relative z-10 -mt-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="card grid grid-cols-2 divide-ink-100 overflow-hidden p-2 lg:grid-cols-4 lg:divide-x dark:divide-ink-800">
                @foreach ([
                    ['inbox', 'Pengaduan Masuk', $statistik['laporan'], 'bg-brand-100 text-brand-700 dark:bg-brand-900/40 dark:text-brand-300'],
                    ['refresh', 'Sedang Ditangani', $statistik['diproses'], 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300'],
                    ['check-badge', 'Selesai Ditindaklanjuti', $statistik['selesai'], 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300'],
                    ['building', 'Perangkat Daerah', $statistik['opd'], 'bg-gold-100 text-gold-700 dark:bg-gold-900/40 dark:text-gold-300'],
                ] as [$ikon, $label, $nilai, $warna])
                    <div class="flex items-center gap-4 p-5">
                        <span class="grid size-12 shrink-0 place-items-center rounded-2xl {{ $warna }}">
                            <x-ikon :nama="$ikon" class="size-6"/>
                        </span>
                        <div x-data="penghitung({{ $nilai }})">
                            <p class="text-3xl font-extrabold tracking-tight text-ink-900 tabular-nums dark:text-white" x-text="tampil">0</p>
                            <p class="text-xs font-semibold text-ink-500 dark:text-ink-400">{{ $label }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============================================================= --}}
    {{-- Kategori pengaduan                                             --}}
    {{-- ============================================================= --}}
    <section class="mx-auto max-w-7xl px-4 py-24 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-2xl text-center">
            <span class="badge bg-brand-50 text-brand-700 ring-brand-600/20 dark:bg-brand-950/50 dark:text-brand-300 dark:ring-brand-500/30">
                <x-ikon nama="tag" class="size-3.5"/> Ruang Lingkup
            </span>
            <h2 class="section-title mt-4">Apa saja yang dapat dilaporkan?</h2>
            <p class="mt-3 text-[15px]/7 text-ink-500 dark:text-ink-400">
                Pilih kategori yang paling sesuai dengan dugaan pelanggaran yang Anda ketahui.
                Inspektorat akan menelaah dan menentukan klasifikasi akhirnya.
            </p>
        </div>

        <div class="mt-14 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($kategori as $item)
                <div class="card card-hover group relative flex flex-col overflow-hidden p-6">
                    <span class="pointer-events-none absolute -top-12 -right-12 size-32 rounded-full bg-brand-500/5 transition-transform duration-500 group-hover:scale-150"></span>

                    <span class="grid size-12 place-items-center rounded-2xl bg-gradient-to-br from-brand-500 to-brand-700 text-white shadow-lg shadow-brand-500/25 transition-transform duration-300 group-hover:scale-110">
                        <x-ikon :nama="$item->icon" class="size-6"/>
                    </span>

                    <h3 class="mt-5 text-base font-bold text-ink-900 dark:text-white">{{ $item->nama }}</h3>
                    <p class="mt-2 flex-1 text-sm/6 text-ink-500 dark:text-ink-400">{{ $item->deskripsi }}</p>

                    <div class="mt-5 flex items-center justify-between border-t border-ink-100 pt-4 dark:border-ink-800">
                        <span class="chip bg-ink-100 font-mono text-ink-500 dark:bg-ink-800 dark:text-ink-400">{{ $item->kode }}</span>
                        <span class="flex items-center gap-1 text-xs font-bold text-brand-600 transition-all group-hover:gap-2 dark:text-brand-400">
                            Laporkan <x-ikon nama="arrow-right" class="size-3.5"/>
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- ============================================================= --}}
    {{-- Alur pelaporan                                                 --}}
    {{-- ============================================================= --}}
    <section class="bg-white py-24 dark:bg-ink-900/40">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-2xl text-center">
                <span class="badge bg-gold-50 text-gold-700 ring-gold-600/20 dark:bg-gold-900/30 dark:text-gold-300 dark:ring-gold-500/30">
                    <x-ikon nama="activity" class="size-3.5"/> Alur Sistem
                </span>
                <h2 class="section-title mt-4">Empat langkah, tuntas ditangani</h2>
                <p class="mt-3 text-[15px]/7 text-ink-500 dark:text-ink-400">
                    Dari pendaftaran akun hingga penerbitan rekomendasi, seluruh prosesnya transparan dan dapat Anda pantau.
                </p>
            </div>

            <div class="relative mt-16">
                {{-- Garis penghubung --}}
                <div class="absolute top-8 right-0 left-0 hidden h-px bg-gradient-to-r from-transparent via-brand-300 to-transparent lg:block dark:via-brand-700"></div>

                <div class="grid gap-10 lg:grid-cols-4">
                    @foreach ([
                        ['user-circle', 'Daftar & Masuk', 'Setiap OPD mendaftarkan akun dengan data pegawai dan perangkat daerahnya, lalu masuk ke sistem.'],
                        ['pencil', 'Susun Pengaduan', 'Pilih kategori, uraikan kronologi, cantumkan pihak terlapor, dan unggah bukti pendukung.'],
                        ['shield-check', 'Verifikasi Inspektorat', 'Pengaduan masuk ke dasbor Inspektorat untuk diverifikasi kelayakan dan kelengkapannya.'],
                        ['gavel', 'Tindak Lanjut & Kesimpulan', 'Tim auditor menangani berkas hingga terbit kesimpulan serta rekomendasi perbaikan.'],
                    ] as $i => [$ikon, $judul, $teks])
                        <div class="relative text-center lg:text-left">
                            <div class="relative mx-auto flex size-16 items-center justify-center lg:mx-0">
                                <span class="absolute inset-0 rounded-2xl bg-gradient-to-br from-brand-500 to-brand-700 shadow-lg shadow-brand-500/30"></span>
                                <x-ikon :nama="$ikon" class="relative size-7 text-white"/>
                                <span class="absolute -top-2 -right-2 grid size-7 place-items-center rounded-full border-2 border-white bg-gold-400 text-xs font-extrabold text-ink-900 dark:border-ink-900">
                                    {{ $i + 1 }}
                                </span>
                            </div>

                            <h3 class="mt-5 text-base font-bold text-ink-900 dark:text-white">{{ $judul }}</h3>
                            <p class="mt-2 text-sm/6 text-ink-500 dark:text-ink-400">{{ $teks }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="mt-14 text-center">
                <a href="{{ route('cara-melapor') }}" class="btn btn-outline">
                    Lihat panduan lengkap <x-ikon nama="arrow-right" class="size-4"/>
                </a>
            </div>
        </div>
    </section>

    {{-- ============================================================= --}}
    {{-- Jaminan perlindungan                                           --}}
    {{-- ============================================================= --}}
    <section class="mx-auto max-w-7xl px-4 py-24 sm:px-6 lg:px-8">
        <div class="grid items-center gap-14 lg:grid-cols-2">
            <div>
                <span class="badge bg-brand-50 text-brand-700 ring-brand-600/20 dark:bg-brand-950/50 dark:text-brand-300 dark:ring-brand-500/30">
                    <x-ikon nama="lock" class="size-3.5"/> Perlindungan Pelapor
                </span>
                <h2 class="section-title mt-4">Melapor tanpa rasa khawatir</h2>
                <p class="mt-4 text-[15px]/7 text-ink-500 dark:text-ink-400">
                    Inspektorat Kabupaten Pringsewu berkomitmen melindungi setiap pelapor yang beritikad baik.
                    Kerahasiaan identitas menjadi prinsip utama dalam penanganan seluruh pengaduan.
                </p>

                <div class="mt-8 space-y-5">
                    @foreach ([
                        ['lock', 'Identitas dirahasiakan', 'Data pelapor hanya dapat diakses petugas Inspektorat yang berwenang menangani berkas.'],
                        ['user', 'Opsi pengaduan anonim', 'Anda dapat memilih untuk tidak menampilkan identitas sama sekali kepada petugas.'],
                        ['shield-check', 'Bebas tindakan balasan', 'Tidak boleh ada intimidasi maupun pembalasan terhadap pelapor yang beritikad baik.'],
                        ['chat', 'Komunikasi dua arah', 'Ajukan pertanyaan atau kirim bukti tambahan langsung melalui kanal pesan pengaduan.'],
                    ] as [$ikon, $judul, $teks])
                        <div class="flex gap-4">
                            <span class="grid size-11 shrink-0 place-items-center rounded-xl bg-brand-100 text-brand-700 dark:bg-brand-900/40 dark:text-brand-300">
                                <x-ikon :nama="$ikon" class="size-5"/>
                            </span>
                            <div>
                                <h3 class="text-sm font-bold text-ink-900 dark:text-white">{{ $judul }}</h3>
                                <p class="mt-1 text-sm/6 text-ink-500 dark:text-ink-400">{{ $teks }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="relative">
                <div class="absolute -inset-4 rounded-[2rem] bg-gradient-to-br from-brand-500/10 to-gold-400/10 blur-2xl"></div>

                <div class="card relative overflow-hidden p-8">
                    <div class="flex items-center gap-3">
                        <span class="grid size-12 place-items-center rounded-2xl bg-gradient-to-br from-brand-600 to-brand-800 text-white">
                            <x-ikon nama="scale" class="size-6"/>
                        </span>
                        <div>
                            <p class="text-sm font-bold text-ink-900 dark:text-white">Dasar Hukum</p>
                            <p class="text-xs text-ink-500 dark:text-ink-400">Landasan penyelenggaraan WBS</p>
                        </div>
                    </div>

                    <ul class="mt-6 space-y-4">
                        @foreach ([
                            'Undang-Undang Nomor 31 Tahun 1999 jo. Undang-Undang Nomor 20 Tahun 2001 tentang Pemberantasan Tindak Pidana Korupsi',
                            'Undang-Undang Nomor 13 Tahun 2006 jo. Undang-Undang Nomor 31 Tahun 2014 tentang Perlindungan Saksi dan Korban',
                            'Peraturan Pemerintah Nomor 43 Tahun 2018 tentang Peran Serta Masyarakat dalam Pencegahan dan Pemberantasan Tindak Pidana Korupsi',
                            'Peraturan Pemerintah Nomor 94 Tahun 2021 tentang Disiplin Pegawai Negeri Sipil',
                        ] as $dasar)
                            <li class="flex gap-3 text-sm/6 text-ink-600 dark:text-ink-300">
                                <x-ikon nama="check" class="mt-1 size-4 shrink-0 text-brand-500"/>
                                {{ $dasar }}
                            </li>
                        @endforeach
                    </ul>

                    <div class="mt-7 rounded-2xl bg-gradient-to-br from-brand-600 to-brand-800 p-5 text-white">
                        <p class="text-sm font-bold">Kriteria pengaduan yang dapat ditindaklanjuti</p>
                        <p class="mt-2 text-xs/6 text-white/75">
                            Memuat unsur <strong>5W + 1H</strong> — apa perbuatannya, siapa pelakunya, di mana terjadinya,
                            kapan waktunya, mengapa terjadi, dan bagaimana kronologinya — serta didukung bukti yang memadai.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================================= --}}
    {{-- Berita                                                         --}}
    {{-- ============================================================= --}}
    @if ($artikel->isNotEmpty())
        <section class="bg-white py-24 dark:bg-ink-900/40">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex flex-wrap items-end justify-between gap-4">
                    <div>
                        <span class="badge bg-gold-50 text-gold-700 ring-gold-600/20 dark:bg-gold-900/30 dark:text-gold-300 dark:ring-gold-500/30">
                            <x-ikon nama="megaphone" class="size-3.5"/> Informasi
                        </span>
                        <h2 class="section-title mt-4">Berita &amp; Publikasi</h2>
                    </div>
                    <a href="{{ route('berita.index') }}" class="btn btn-outline btn-sm">
                        Semua berita <x-ikon nama="arrow-right" class="size-4"/>
                    </a>
                </div>

                <div class="mt-10 grid gap-6 md:grid-cols-3">
                    @foreach ($artikel as $item)
                        <a href="{{ route('berita.show', $item) }}" class="card card-hover group overflow-hidden">
                            <div class="relative aspect-[16/10] overflow-hidden bg-gradient-to-br from-brand-600 to-brand-900">
                                @if ($item->gambar_url)
                                    <img src="{{ $item->gambar_url }}" alt="" class="size-full object-cover transition duration-500 group-hover:scale-105">
                                @else
                                    <div class="bg-noise absolute inset-0 grid place-items-center">
                                        <x-ikon nama="megaphone" class="size-12 text-white/25"/>
                                    </div>
                                @endif
                            </div>

                            <div class="p-5">
                                <p class="text-xs font-semibold text-brand-600 dark:text-brand-400">
                                    {{ $item->published_at?->translatedFormat('d F Y') }}
                                </p>
                                <h3 class="mt-2 line-clamp-2 text-base leading-snug font-bold text-ink-900 transition group-hover:text-brand-700 dark:text-white dark:group-hover:text-brand-400">
                                    {{ $item->judul }}
                                </h3>
                                <p class="mt-2 line-clamp-3 text-sm/6 text-ink-500 dark:text-ink-400">{{ $item->ringkasan }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ============================================================= --}}
    {{-- FAQ                                                            --}}
    {{-- ============================================================= --}}
    @if ($faq->isNotEmpty())
        <section class="mx-auto max-w-4xl px-4 py-24 sm:px-6 lg:px-8">
            <div class="text-center">
                <span class="badge bg-brand-50 text-brand-700 ring-brand-600/20 dark:bg-brand-950/50 dark:text-brand-300 dark:ring-brand-500/30">
                    <x-ikon nama="info" class="size-3.5"/> Pertanyaan Umum
                </span>
                <h2 class="section-title mt-4">Hal yang sering ditanyakan</h2>
            </div>

            <div class="mt-12 space-y-3" x-data="{ aktif: 0 }">
                @foreach ($faq as $i => $item)
                    <div class="card overflow-hidden">
                        <button type="button" @click="aktif = aktif === {{ $i }} ? null : {{ $i }}"
                                class="flex w-full items-center justify-between gap-4 px-5 py-4 text-left">
                            <span class="text-sm font-bold text-ink-900 dark:text-white">{{ $item->pertanyaan }}</span>
                            <span class="grid size-8 shrink-0 place-items-center rounded-lg bg-ink-100 text-ink-500 transition-transform duration-300 dark:bg-ink-800 dark:text-ink-400"
                                  :class="aktif === {{ $i }} && 'rotate-180 bg-brand-600 text-white'">
                                <x-ikon nama="chevron-down" class="size-4"/>
                            </span>
                        </button>
                        <div x-show="aktif === {{ $i }}" x-collapse x-cloak>
                            <p class="border-t border-ink-100 px-5 py-4 text-sm/7 text-ink-600 dark:border-ink-800 dark:text-ink-300">
                                {{ $item->jawaban }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8 text-center">
                <a href="{{ route('faq') }}" class="btn btn-outline btn-sm">
                    Lihat semua pertanyaan <x-ikon nama="arrow-right" class="size-4"/>
                </a>
            </div>
        </section>
    @endif

    {{-- ============================================================= --}}
    {{-- Ajakan akhir                                                   --}}
    {{-- ============================================================= --}}
    <section class="mx-auto max-w-7xl px-4 pb-24 sm:px-6 lg:px-8">
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-brand-700 via-brand-800 to-ink-950 px-8 py-16 text-center sm:px-16">
            <div class="bg-grid absolute inset-0 opacity-20"></div>
            <div class="absolute -top-24 -right-24 size-80 rounded-full bg-gold-400/15 blur-3xl"></div>
            <div class="absolute -bottom-24 -left-24 size-80 rounded-full bg-brand-400/20 blur-3xl"></div>

            <div class="relative mx-auto max-w-2xl">
                <span class="grid size-16 place-items-center rounded-2xl bg-white/10 text-white ring-1 ring-white/20 mx-auto backdrop-blur">
                    <x-ikon nama="megaphone" class="size-8"/>
                </span>

                <h2 class="mt-6 text-3xl font-extrabold tracking-tight text-white sm:text-4xl">
                    Satu laporan Anda, satu langkah perbaikan.
                </h2>
                <p class="mt-4 text-[15px]/7 text-white/70">
                    Daftarkan akun perangkat daerah Anda sekarang dan sampaikan pengaduan dengan aman.
                    Sudah pernah melapor? Lacak status pengaduan menggunakan nomor tiket.
                </p>

                <div class="mt-9 flex flex-wrap justify-center gap-3">
                    <a href="{{ route('register') }}" class="btn btn-gold btn-lg">
                        <x-ikon nama="user-circle" class="size-5"/> Daftar Akun OPD
                    </a>
                    <a href="{{ route('lacak.index') }}" class="btn btn-lg border border-white/20 bg-white/5 text-white backdrop-blur hover:bg-white/10">
                        <x-ikon nama="search" class="size-5"/> Lacak Aduan
                    </a>
                </div>
            </div>
        </div>
    </section>

@endsection
