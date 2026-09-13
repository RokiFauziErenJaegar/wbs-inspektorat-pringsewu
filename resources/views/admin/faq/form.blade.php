@extends('layouts.app')

@section('judul', $faq->exists ? 'Ubah FAQ' : 'Tambah FAQ')

@section('konten')

    <div>
        <a href="{{ route('admin.faq.index') }}" class="btn btn-ghost btn-sm">
            <x-ikon nama="arrow-left" class="size-4"/> Kembali ke daftar FAQ
        </a>
    </div>

    <form method="POST" action="{{ $faq->exists ? route('admin.faq.update', $faq) : route('admin.faq.store') }}"
          class="card mx-auto max-w-3xl p-6 sm:p-7">
        @csrf
        @if ($faq->exists) @method('PUT') @endif

        <div class="flex items-center gap-3 border-b border-ink-100 pb-4 dark:border-ink-800">
            <span class="grid size-10 place-items-center rounded-xl bg-brand-100 text-brand-700 dark:bg-brand-900/40 dark:text-brand-300">
                <x-ikon nama="book-open" class="size-5"/>
            </span>
            <div>
                <h3 class="text-base font-bold text-ink-900 dark:text-white">Data FAQ</h3>
                <p class="text-xs text-ink-500 dark:text-ink-400">Tampil pada halaman FAQ dan beranda publik</p>
            </div>
        </div>

        <div class="mt-6 space-y-5">
            <x-input nama="pertanyaan" label="Pertanyaan" wajib :nilai="$faq->pertanyaan"
                     placeholder="Contoh: Apakah identitas saya dirahasiakan?" />

            <x-textarea nama="jawaban" label="Jawaban" wajib baris="6" :nilai="$faq->jawaban"
                        placeholder="Tulis jawaban yang jelas dan mudah dipahami." />

            <div class="grid gap-5 sm:grid-cols-2">
                <x-select nama="kelompok" label="Kelompok" wajib :nilai="$faq->kelompok ?? 'umum'" :pilihan="[
                    'umum' => 'Umum',
                    'kerahasiaan' => 'Kerahasiaan & Perlindungan',
                    'proses' => 'Proses Penanganan',
                    'teknis' => 'Teknis Aplikasi',
                ]" />

                <x-input nama="urutan" label="Urutan Tampil" tipe="number" :nilai="$faq->urutan ?? 0" min="0" max="999" />
            </div>

            <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-ink-200 p-4 transition hover:border-brand-300 hover:bg-brand-50/50 dark:border-ink-700 dark:hover:border-brand-700 dark:hover:bg-brand-950/20">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $faq->is_active ?? true))
                       class="mt-0.5 size-4 rounded border-ink-300 text-brand-600 focus:ring-2 focus:ring-brand-500/30 dark:border-ink-600 dark:bg-ink-900">
                <span>
                    <span class="block text-sm font-bold text-ink-900 dark:text-white">Aktif</span>
                    <span class="mt-0.5 block text-xs text-ink-500 dark:text-ink-400">FAQ nonaktif tidak tampil pada halaman publik.</span>
                </span>
            </label>
        </div>

        <div class="mt-7 flex justify-end gap-3 border-t border-ink-100 pt-5 dark:border-ink-800">
            <a href="{{ route('admin.faq.index') }}" class="btn btn-outline">Batal</a>
            <button class="btn btn-primary"><x-ikon nama="check" class="size-4"/> Simpan</button>
        </div>
    </form>

@endsection
