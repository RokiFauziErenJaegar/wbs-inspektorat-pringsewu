@extends('layouts.app')

@section('judul', $artikel->exists ? 'Ubah Berita' : 'Tulis Berita Baru')

@section('konten')

    <div>
        <a href="{{ route('admin.artikel.index') }}" class="btn btn-ghost btn-sm">
            <x-ikon nama="arrow-left" class="size-4"/> Kembali ke daftar berita
        </a>
    </div>

    <form method="POST" action="{{ $artikel->exists ? route('admin.artikel.update', $artikel) : route('admin.artikel.store') }}"
          enctype="multipart/form-data" class="grid gap-5 lg:grid-cols-3">
        @csrf
        @if ($artikel->exists) @method('PUT') @endif

        {{-- Isi --}}
        <div class="card space-y-5 p-6 lg:col-span-2 sm:p-7">
            <x-input nama="judul" label="Judul Berita" wajib :nilai="$artikel->judul"
                     placeholder="Judul yang ringkas dan informatif" maxlength="200" />

            <x-textarea nama="ringkasan" label="Ringkasan" baris="2" :nilai="$artikel->ringkasan"
                        maxlength="300" placeholder="Kosongkan untuk diisi otomatis dari isi berita"
                        bantuan="Tampil pada kartu berita dan hasil pencarian" />

            <div>
                <x-textarea nama="konten" label="Isi Berita" wajib baris="16" :nilai="$artikel->konten"
                            placeholder="Tulis isi berita di sini. Anda dapat menggunakan tag HTML sederhana seperti <p>, <h2>, <ul>, <li>, <strong>, dan <blockquote>." />
                <p class="help">
                    Tag HTML yang didukung: <code class="rounded bg-ink-100 px-1 dark:bg-ink-800">&lt;p&gt;</code>,
                    <code class="rounded bg-ink-100 px-1 dark:bg-ink-800">&lt;h2&gt;</code>,
                    <code class="rounded bg-ink-100 px-1 dark:bg-ink-800">&lt;h3&gt;</code>,
                    <code class="rounded bg-ink-100 px-1 dark:bg-ink-800">&lt;ul&gt;</code>,
                    <code class="rounded bg-ink-100 px-1 dark:bg-ink-800">&lt;ol&gt;</code>,
                    <code class="rounded bg-ink-100 px-1 dark:bg-ink-800">&lt;strong&gt;</code>,
                    <code class="rounded bg-ink-100 px-1 dark:bg-ink-800">&lt;a&gt;</code>,
                    <code class="rounded bg-ink-100 px-1 dark:bg-ink-800">&lt;blockquote&gt;</code>
                </p>
            </div>
        </div>

        {{-- Pengaturan --}}
        <div class="space-y-5">
            <div class="card p-6">
                <h3 class="text-base font-bold text-ink-900 dark:text-white">Publikasi</h3>

                <div class="mt-5 space-y-5">
                    <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-ink-200 p-4 transition hover:border-brand-300 hover:bg-brand-50/50 dark:border-ink-700 dark:hover:border-brand-700 dark:hover:bg-brand-950/20">
                        <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $artikel->is_published ?? true))
                               class="mt-0.5 size-4 rounded border-ink-300 text-brand-600 focus:ring-2 focus:ring-brand-500/30 dark:border-ink-600 dark:bg-ink-900">
                        <span>
                            <span class="block text-sm font-bold text-ink-900 dark:text-white">Terbitkan</span>
                            <span class="mt-0.5 block text-xs text-ink-500 dark:text-ink-400">Tampil pada halaman berita publik.</span>
                        </span>
                    </label>

                    <x-input nama="published_at" label="Tanggal Terbit" tipe="datetime-local"
                             :nilai="$artikel->published_at?->format('Y-m-d\TH:i')"
                             bantuan="Kosongkan untuk memakai waktu saat ini" />

                    <x-input nama="sumber" label="Sumber" :nilai="$artikel->sumber"
                             placeholder="Humas Inspektorat Kabupaten Pringsewu" />
                </div>
            </div>

            <div class="card p-6">
                <h3 class="text-base font-bold text-ink-900 dark:text-white">Gambar Sampul</h3>

                @if ($artikel->gambar_url)
                    <img src="{{ $artikel->gambar_url }}" alt="" class="mt-4 aspect-video w-full rounded-xl object-cover">
                @endif

                <div class="mt-4">
                    <input type="file" name="gambar" accept="image/*"
                           class="w-full text-sm text-ink-600 file:mr-3 file:cursor-pointer file:rounded-lg file:border-0 file:bg-brand-600 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-brand-700 dark:text-ink-300">
                    <p class="help">JPG, PNG, atau WEBP · maksimal 4 MB · rasio ideal 16:9</p>
                    @error('gambar')
                        <p class="mt-1.5 text-xs font-medium text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="card flex gap-3 p-4">
                <a href="{{ route('admin.artikel.index') }}" class="btn btn-outline flex-1">Batal</a>
                <button class="btn btn-primary flex-1"><x-ikon nama="check" class="size-4"/> Simpan</button>
            </div>
        </div>
    </form>

@endsection
