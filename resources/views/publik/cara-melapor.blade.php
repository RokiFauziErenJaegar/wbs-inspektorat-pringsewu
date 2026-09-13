@extends('layouts.publik')

@section('judul', 'Cara Melapor')

@section('konten')

    <section class="relative -mt-18 overflow-hidden bg-gradient-to-br from-brand-900 via-brand-950 to-ink-950 pt-32 pb-20">
        <div class="bg-grid absolute inset-0 opacity-20"></div>
        <div class="absolute -top-24 right-0 size-96 rounded-full bg-brand-500/15 blur-3xl"></div>

        <div class="relative mx-auto max-w-3xl px-4 text-center sm:px-6">
            <span class="badge bg-white/10 text-brand-200 ring-white/20 backdrop-blur">
                <x-ikon nama="book-open" class="size-3.5"/> Panduan Pelaporan
            </span>
            <h1 class="mt-5 text-4xl font-extrabold tracking-tight text-white sm:text-5xl">Cara Menyampaikan Pengaduan</h1>
            <p class="mt-4 text-base/7 text-white/70">
                Ikuti panduan berikut agar pengaduan Anda lengkap, jelas, dan dapat segera ditindaklanjuti
                oleh Inspektorat Kabupaten Pringsewu.
            </p>
        </div>
    </section>

    {{-- Langkah-langkah --}}
    <section class="mx-auto max-w-5xl px-4 py-20 sm:px-6 lg:px-8">
        <div class="space-y-6">
            @foreach ([
                ['user-circle', 'Daftarkan akun perangkat daerah', [
                    'Buka menu <strong>Daftar</strong> lalu isi nama lengkap, username, surel aktif, dan pilih OPD tempat Anda bertugas.',
                    'Gunakan surel dinas bila tersedia agar verifikasi lebih mudah dilakukan.',
                    'Kata sandi minimal 8 karakter dan mengandung huruf serta angka.',
                ]],
                ['tag', 'Pilih kategori pengaduan', [
                    'Setelah masuk, klik <strong>Buat Pengaduan</strong> lalu pilih kategori yang paling sesuai.',
                    'Bila ragu, pilih kategori <strong>Pelanggaran Lainnya</strong> — Inspektorat akan menentukan klasifikasi akhirnya.',
                ]],
                ['pencil', 'Uraikan kronologi secara lengkap', [
                    'Tuliskan judul singkat yang menggambarkan inti persoalan.',
                    'Uraikan kejadian memenuhi unsur <strong>5W + 1H</strong>: apa, siapa, di mana, kapan, mengapa, dan bagaimana.',
                    'Cantumkan lokasi kejadian, tanggal kejadian, serta perkiraan nilai kerugian bila ada.',
                ]],
                ['users', 'Cantumkan pihak yang diduga terlibat', [
                    'Isikan nama, jabatan, instansi, dan klasifikasi jabatan pihak terlapor.',
                    'Anda dapat menambahkan lebih dari satu pihak dengan menekan tombol <strong>Tambah</strong>.',
                ]],
                ['paper-clip', 'Lampirkan bukti pendukung', [
                    'Unggah dokumen, foto, tangkapan layar, rekaman, atau berkas lain yang relevan.',
                    'Format didukung: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, JPG, PNG, GIF, ZIP, RAR, MP3, MP4, MOV, 3GP.',
                    'Ukuran maksimal <strong>50 MB</strong> per berkas, maksimal 10 berkas per pengaduan.',
                ]],
                ['send', 'Kirim dan pantau statusnya', [
                    'Simpan sebagai draf bila belum selesai, atau langsung tekan <strong>Kirim Pengaduan</strong>.',
                    'Setelah terkirim, Anda menerima <strong>nomor tiket</strong> untuk memantau proses penanganan.',
                    'Pantau perkembangan melalui menu <strong>Pengaduan Saya</strong> atau halaman <strong>Lacak Aduan</strong>.',
                ]],
            ] as $i => [$ikon, $judul, $poin])
                <div class="card card-hover flex flex-col gap-5 p-6 sm:flex-row sm:p-7">
                    <div class="flex shrink-0 items-center gap-4 sm:flex-col sm:items-center">
                        <span class="relative grid size-14 place-items-center rounded-2xl bg-gradient-to-br from-brand-500 to-brand-700 text-white shadow-lg shadow-brand-500/25">
                            <x-ikon :nama="$ikon" class="size-6"/>
                            <span class="absolute -top-2 -right-2 grid size-7 place-items-center rounded-full border-2 border-white bg-gold-400 text-xs font-extrabold text-ink-900 dark:border-ink-900">
                                {{ $i + 1 }}
                            </span>
                        </span>
                    </div>

                    <div class="min-w-0">
                        <h2 class="text-lg font-bold text-ink-900 dark:text-white">{{ $judul }}</h2>
                        <ul class="mt-3 space-y-2">
                            @foreach ($poin as $isi)
                                <li class="flex gap-2.5 text-sm/6 text-ink-600 dark:text-ink-300">
                                    <x-ikon nama="check" class="mt-1 size-4 shrink-0 text-brand-500"/>
                                    <span>{!! $isi !!}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Yang perlu diperhatikan --}}
    <section class="bg-white py-20 dark:bg-ink-900/40">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <div class="grid gap-6 lg:grid-cols-2">
                <div class="card border-emerald-200 p-7 dark:border-emerald-900/60">
                    <span class="grid size-11 place-items-center rounded-xl bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300">
                        <x-ikon nama="check-badge" class="size-5"/>
                    </span>
                    <h3 class="mt-4 text-lg font-bold text-ink-900 dark:text-white">Pengaduan yang dapat ditindaklanjuti</h3>
                    <ul class="mt-4 space-y-2.5">
                        @foreach ([
                            'Memuat identitas pihak terlapor yang jelas',
                            'Menguraikan perbuatan yang diduga melanggar secara rinci',
                            'Menyebutkan waktu dan tempat kejadian',
                            'Disertai bukti pendukung yang relevan',
                            'Merupakan kewenangan pengawasan Inspektorat',
                        ] as $poin)
                            <li class="flex gap-2.5 text-sm/6 text-ink-600 dark:text-ink-300">
                                <x-ikon nama="check" class="mt-1 size-4 shrink-0 text-emerald-500"/> {{ $poin }}
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="card border-rose-200 p-7 dark:border-rose-900/60">
                    <span class="grid size-11 place-items-center rounded-xl bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300">
                        <x-ikon nama="x-circle" class="size-5"/>
                    </span>
                    <h3 class="mt-4 text-lg font-bold text-ink-900 dark:text-white">Pengaduan yang tidak dapat diproses</h3>
                    <ul class="mt-4 space-y-2.5">
                        @foreach ([
                            'Bersifat fitnah tanpa dasar atau data pendukung',
                            'Hanya berisi ungkapan kemarahan tanpa uraian peristiwa',
                            'Materi yang sama telah dilaporkan dan sedang ditangani',
                            'Menjadi kewenangan instansi lain di luar Inspektorat',
                            'Perkara yang telah masuk proses peradilan',
                        ] as $poin)
                            <li class="flex gap-2.5 text-sm/6 text-ink-600 dark:text-ink-300">
                                <x-ikon nama="x" class="mt-1 size-4 shrink-0 text-rose-500"/> {{ $poin }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            {{-- Kategori --}}
            <div class="mt-14">
                <h3 class="text-center text-xl font-extrabold text-ink-900 dark:text-white">Kategori yang tersedia</h3>
                <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($kategori as $item)
                        <div class="card p-5">
                            <div class="flex items-center gap-3">
                                <span class="grid size-10 shrink-0 place-items-center rounded-xl bg-brand-100 text-brand-700 dark:bg-brand-900/40 dark:text-brand-300">
                                    <x-ikon :nama="$item->icon" class="size-5"/>
                                </span>
                                <h4 class="text-sm font-bold text-ink-900 dark:text-white">{{ $item->nama }}</h4>
                            </div>
                            @if ($item->petunjuk)
                                <p class="mt-3 text-xs/6 text-ink-500 dark:text-ink-400">{{ $item->petunjuk }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- Ajakan --}}
    <section class="mx-auto max-w-4xl px-4 py-20 text-center sm:px-6">
        <h2 class="section-title">Siap menyampaikan pengaduan?</h2>
        <p class="mt-3 text-[15px]/7 text-ink-500 dark:text-ink-400">
            Daftarkan akun Anda dan mulai laporkan dugaan pelanggaran dengan aman.
        </p>
        <div class="mt-8 flex flex-wrap justify-center gap-3">
            @auth
                <a href="{{ auth()->user()->isPelapor() ? route('pelapor.laporan.pilih-kategori') : route('admin.dashboard') }}" class="btn btn-primary btn-lg">
                    <x-ikon nama="megaphone" class="size-5"/> Buat Pengaduan
                </a>
            @else
                <a href="{{ route('register') }}" class="btn btn-primary btn-lg">
                    <x-ikon nama="user-circle" class="size-5"/> Daftar Sekarang
                </a>
                <a href="{{ route('login') }}" class="btn btn-outline btn-lg">Sudah punya akun? Masuk</a>
            @endauth
        </div>
    </section>

@endsection
