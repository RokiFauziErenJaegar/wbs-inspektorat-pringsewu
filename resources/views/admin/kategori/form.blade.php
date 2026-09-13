@extends('layouts.app')

@section('judul', $kategori->exists ? 'Ubah Kategori Pengaduan' : 'Tambah Kategori Pengaduan')

@section('konten')

    <div>
        <a href="{{ route('admin.kategori.index') }}" class="btn btn-ghost btn-sm">
            <x-ikon nama="arrow-left" class="size-4"/> Kembali ke daftar kategori
        </a>
    </div>

    <form method="POST" action="{{ $kategori->exists ? route('admin.kategori.update', $kategori) : route('admin.kategori.store') }}"
          x-data="{ ikon: '{{ old('icon', $kategori->icon ?? 'shield') }}' }"
          class="card mx-auto max-w-3xl p-6 sm:p-7">
        @csrf
        @if ($kategori->exists) @method('PUT') @endif

        <div class="flex items-center gap-3 border-b border-ink-100 pb-4 dark:border-ink-800">
            <span class="grid size-12 place-items-center rounded-xl bg-gradient-to-br from-brand-500 to-brand-700 text-white">
                <x-ikon nama="tag" class="size-6"/>
            </span>
            <div>
                <h3 class="text-base font-bold text-ink-900 dark:text-white">Data Kategori</h3>
                <p class="text-xs text-ink-500 dark:text-ink-400">Tampil pada halaman pemilihan tujuan pengaduan</p>
            </div>
        </div>

        <div class="mt-6 space-y-5">
            <div class="grid gap-5 sm:grid-cols-3">
                <div class="sm:col-span-2">
                    <x-input nama="nama" label="Nama Kategori" wajib :nilai="$kategori->nama"
                             placeholder="Contoh: Tindak Pidana Korupsi" />
                </div>
                <x-input nama="kode" label="Kode" wajib :nilai="$kategori->kode" placeholder="TPK"
                         class="uppercase" bantuan="Huruf/angka tanpa spasi" />
            </div>

            <x-textarea nama="deskripsi" label="Deskripsi Singkat" baris="3" :nilai="$kategori->deskripsi"
                        placeholder="Penjelasan singkat yang tampil pada kartu kategori" />

            <x-textarea nama="petunjuk" label="Petunjuk Pengisian" baris="3" :nilai="$kategori->petunjuk"
                        placeholder="Panduan bagi pelapor saat mengisi formulir kategori ini" />

            {{-- Pemilih ikon --}}
            <div>
                <label class="label">Ikon Kategori</label>
                <div class="grid grid-cols-6 gap-2 sm:grid-cols-12">
                    @foreach (\App\Http\Controllers\Admin\KategoriController::IKON as $pilihanIkon)
                        <label class="cursor-pointer">
                            <input type="radio" name="icon" value="{{ $pilihanIkon }}" x-model="ikon" class="peer sr-only"
                                   @checked(old('icon', $kategori->icon ?? 'shield') === $pilihanIkon)>
                            <span class="grid aspect-square place-items-center rounded-xl border border-ink-200 text-ink-500 transition peer-checked:border-brand-500 peer-checked:bg-brand-600 peer-checked:text-white hover:border-brand-300 dark:border-ink-700 dark:text-ink-400">
                                <x-ikon :nama="$pilihanIkon" class="size-5"/>
                            </span>
                        </label>
                    @endforeach
                </div>
                @error('icon')
                    <p class="mt-1.5 text-xs font-medium text-rose-600 dark:text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <x-select nama="warna" label="Warna Aksen" wajib :nilai="$kategori->warna ?? 'emerald'"
                          :pilihan="collect(\App\Http\Controllers\Admin\KategoriController::WARNA)->mapWithKeys(fn ($w) => [$w => Str::title($w)])" />

                <x-input nama="urutan" label="Urutan Tampil" tipe="number" :nilai="$kategori->urutan ?? 0" min="0" max="999" />
            </div>

            <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-ink-200 p-4 transition hover:border-brand-300 hover:bg-brand-50/50 dark:border-ink-700 dark:hover:border-brand-700 dark:hover:bg-brand-950/20">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $kategori->is_active ?? true))
                       class="mt-0.5 size-4 rounded border-ink-300 text-brand-600 focus:ring-2 focus:ring-brand-500/30 dark:border-ink-600 dark:bg-ink-900">
                <span>
                    <span class="block text-sm font-bold text-ink-900 dark:text-white">Aktif</span>
                    <span class="mt-0.5 block text-xs text-ink-500 dark:text-ink-400">Kategori nonaktif tidak dapat dipilih pelapor.</span>
                </span>
            </label>
        </div>

        <div class="mt-7 flex justify-end gap-3 border-t border-ink-100 pt-5 dark:border-ink-800">
            <a href="{{ route('admin.kategori.index') }}" class="btn btn-outline">Batal</a>
            <button class="btn btn-primary"><x-ikon nama="check" class="size-4"/> Simpan</button>
        </div>
    </form>

@endsection
