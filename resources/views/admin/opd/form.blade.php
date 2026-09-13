@extends('layouts.app')

@section('judul', $opd->exists ? 'Ubah Perangkat Daerah' : 'Tambah Perangkat Daerah')

@section('konten')

    <div>
        <a href="{{ route('admin.opd.index') }}" class="btn btn-ghost btn-sm">
            <x-ikon nama="arrow-left" class="size-4"/> Kembali ke daftar OPD
        </a>
    </div>

    <form method="POST" action="{{ $opd->exists ? route('admin.opd.update', $opd) : route('admin.opd.store') }}"
          class="card mx-auto max-w-3xl p-6 sm:p-7">
        @csrf
        @if ($opd->exists) @method('PUT') @endif

        <div class="flex items-center gap-3 border-b border-ink-100 pb-4 dark:border-ink-800">
            <span class="grid size-10 place-items-center rounded-xl bg-brand-100 text-brand-700 dark:bg-brand-900/40 dark:text-brand-300">
                <x-ikon nama="building" class="size-5"/>
            </span>
            <div>
                <h3 class="text-base font-bold text-ink-900 dark:text-white">Data Perangkat Daerah</h3>
                <p class="text-xs text-ink-500 dark:text-ink-400">Digunakan pada pendaftaran pelapor dan penentuan OPD terlapor</p>
            </div>
        </div>

        <div class="mt-6 space-y-5">
            <x-input nama="nama" label="Nama Perangkat Daerah" wajib :nilai="$opd->nama"
                     placeholder="Contoh: Dinas Pendidikan dan Kebudayaan" />

            <div class="grid gap-5 sm:grid-cols-2">
                <x-input nama="singkatan" label="Singkatan" :nilai="$opd->singkatan" placeholder="DISDIKBUD" />

                <x-select nama="jenis" label="Jenis" wajib :nilai="$opd->jenis" :pilihan="[
                    'sekretariat' => 'Sekretariat',
                    'dinas' => 'Dinas',
                    'badan' => 'Badan',
                    'inspektorat' => 'Inspektorat',
                    'kecamatan' => 'Kecamatan',
                    'rsud' => 'RSUD',
                    'satuan' => 'Satuan',
                    'lainnya' => 'Lainnya',
                ]" />
            </div>

            <x-input nama="alamat" label="Alamat" :nilai="$opd->alamat" ikon="map-pin" />

            <div class="grid gap-5 sm:grid-cols-2">
                <x-input nama="telepon" label="Telepon" :nilai="$opd->telepon" ikon="phone" />
                <x-input nama="email" label="Email" tipe="email" :nilai="$opd->email" ikon="mail" />
            </div>

            <x-input nama="kepala" label="Nama Kepala OPD" :nilai="$opd->kepala" ikon="user" />

            <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-ink-200 p-4 transition hover:border-brand-300 hover:bg-brand-50/50 dark:border-ink-700 dark:hover:border-brand-700 dark:hover:bg-brand-950/20">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $opd->is_active ?? true))
                       class="mt-0.5 size-4 rounded border-ink-300 text-brand-600 focus:ring-2 focus:ring-brand-500/30 dark:border-ink-600 dark:bg-ink-900">
                <span>
                    <span class="block text-sm font-bold text-ink-900 dark:text-white">Aktif</span>
                    <span class="mt-0.5 block text-xs text-ink-500 dark:text-ink-400">OPD nonaktif tidak muncul pada pilihan pendaftaran maupun formulir pengaduan.</span>
                </span>
            </label>
        </div>

        <div class="mt-7 flex justify-end gap-3 border-t border-ink-100 pt-5 dark:border-ink-800">
            <a href="{{ route('admin.opd.index') }}" class="btn btn-outline">Batal</a>
            <button class="btn btn-primary"><x-ikon nama="check" class="size-4"/> Simpan</button>
        </div>
    </form>

@endsection
