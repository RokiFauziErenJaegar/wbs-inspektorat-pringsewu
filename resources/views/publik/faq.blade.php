@extends('layouts.publik')

@section('judul', 'FAQ')

@section('konten')

    <section class="relative -mt-18 overflow-hidden bg-gradient-to-br from-brand-900 via-brand-950 to-ink-950 pt-32 pb-20">
        <div class="bg-grid absolute inset-0 opacity-20"></div>
        <div class="absolute -top-24 right-1/4 size-96 rounded-full bg-brand-500/15 blur-3xl"></div>

        <div class="relative mx-auto max-w-3xl px-4 text-center sm:px-6">
            <span class="badge bg-white/10 text-brand-200 ring-white/20 backdrop-blur">
                <x-ikon nama="info" class="size-3.5"/> Pusat Bantuan
            </span>
            <h1 class="mt-5 text-4xl font-extrabold tracking-tight text-white sm:text-5xl">Pertanyaan yang Sering Diajukan</h1>
            <p class="mt-4 text-base/7 text-white/70">
                Temukan jawaban atas pertanyaan seputar penggunaan Whistleblowing System,
                kerahasiaan pelapor, dan proses penanganan pengaduan.
            </p>
        </div>
    </section>

    <section class="mx-auto max-w-4xl px-4 py-16 sm:px-6 lg:px-8">
        @php
            $judulKelompok = [
                'umum' => ['Umum', 'info'],
                'kerahasiaan' => ['Kerahasiaan & Perlindungan', 'lock'],
                'proses' => ['Proses Penanganan', 'activity'],
                'teknis' => ['Teknis Aplikasi', 'cog'],
            ];
        @endphp

        @forelse ($kelompok as $nama => $daftar)
            @php [$label, $ikon] = $judulKelompok[$nama] ?? [Str::title($nama), 'info']; @endphp

            <div class="mb-12">
                <div class="flex items-center gap-3">
                    <span class="grid size-11 place-items-center rounded-xl bg-gradient-to-br from-brand-500 to-brand-700 text-white shadow-lg shadow-brand-500/25">
                        <x-ikon :nama="$ikon" class="size-5"/>
                    </span>
                    <h2 class="text-xl font-extrabold text-ink-900 dark:text-white">{{ $label }}</h2>
                </div>

                <div class="mt-6 space-y-3" x-data="{ aktif: null }">
                    @foreach ($daftar as $item)
                        <div class="card overflow-hidden">
                            <button type="button" @click="aktif = aktif === {{ $item->id }} ? null : {{ $item->id }}"
                                    class="flex w-full items-center justify-between gap-4 px-5 py-4 text-left">
                                <span class="text-sm font-bold text-ink-900 dark:text-white">{{ $item->pertanyaan }}</span>
                                <span class="grid size-8 shrink-0 place-items-center rounded-lg bg-ink-100 text-ink-500 transition-all duration-300 dark:bg-ink-800 dark:text-ink-400"
                                      :class="aktif === {{ $item->id }} && 'rotate-180 bg-brand-600 text-white'">
                                    <x-ikon nama="chevron-down" class="size-4"/>
                                </span>
                            </button>
                            <div x-show="aktif === {{ $item->id }}" x-collapse x-cloak>
                                <p class="border-t border-ink-100 px-5 py-4 text-sm/7 text-ink-600 dark:border-ink-800 dark:text-ink-300">
                                    {{ $item->jawaban }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="card">
                <x-kosong ikon="info" judul="Belum ada FAQ" pesan="Daftar pertanyaan umum akan segera tersedia." />
            </div>
        @endforelse

        <div class="card overflow-hidden">
            <div class="bg-gradient-to-br from-brand-700 to-brand-900 p-8 text-center">
                <span class="mx-auto grid size-14 place-items-center rounded-2xl bg-white/10 text-white ring-1 ring-white/20">
                    <x-ikon nama="chat" class="size-7"/>
                </span>
                <h3 class="mt-5 text-xl font-extrabold text-white">Pertanyaan Anda belum terjawab?</h3>
                <p class="mt-2 text-sm/6 text-white/70">
                    Hubungi Inspektorat Kabupaten Pringsewu melalui kontak resmi berikut.
                </p>

                <div class="mt-6 flex flex-wrap justify-center gap-3">
                    <a href="mailto:inspektorat@pringsewukab.go.id" class="btn border border-white/20 bg-white/10 text-white backdrop-blur hover:bg-white/20">
                        <x-ikon nama="mail" class="size-4"/> inspektorat@pringsewukab.go.id
                    </a>
                    <a href="{{ route('cara-melapor') }}" class="btn btn-gold">
                        <x-ikon nama="book-open" class="size-4"/> Baca Panduan
                    </a>
                </div>
            </div>
        </div>
    </section>

@endsection
