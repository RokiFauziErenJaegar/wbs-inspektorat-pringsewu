@extends('layouts.app')

@section('judul', $laporan->exists ? 'Ubah Draf Pengaduan' : 'Formulir Pengaduan')
@section('subjudul', 'Langkah 2 dari 2 — lengkapi rincian pengaduan Anda')

@section('konten')

@php
    $terlaporAwal = array_values(old('terlapor', $laporan->exists
        ? $laporan->terlapor->map(fn ($t) => [
            'nama' => $t->nama,
            'jabatan' => $t->jabatan,
            'instansi' => $t->instansi,
            'klasifikasi' => $t->klasifikasi,
        ])->values()->all()
        : []));
@endphp

<form method="POST"
      action="{{ $laporan->exists ? route('pelapor.laporan.update', $laporan) : route('pelapor.laporan.store') }}"
      enctype="multipart/form-data"
      x-data="{ mengirim: false }"
      @submit="mengirim = true"
      class="space-y-6">
    @csrf
    @if ($laporan->exists) @method('PUT') @endif

    <input type="hidden" name="kategori_id" value="{{ $kategori->id }}">

    {{-- Kategori terpilih --}}
    <div class="card overflow-hidden">
        <div class="flex flex-wrap items-center gap-4 bg-gradient-to-r from-brand-600 to-brand-800 p-5 text-white">
            <span class="grid size-14 shrink-0 place-items-center rounded-2xl bg-white/15 ring-1 ring-white/20">
                <x-ikon :nama="$kategori->icon" class="size-7"/>
            </span>
            <div class="min-w-0 flex-1">
                <p class="text-[11px] font-bold tracking-widest text-white/60 uppercase">Kategori Pengaduan</p>
                <h2 class="mt-0.5 text-lg font-extrabold">{{ $kategori->nama }}</h2>
            </div>
            @unless ($laporan->exists)
                <a href="{{ route('pelapor.laporan.pilih-kategori') }}" class="btn border border-white/20 bg-white/10 text-white backdrop-blur hover:bg-white/20 btn-sm">
                    <x-ikon nama="refresh" class="size-3.5"/> Ganti Kategori
                </a>
            @endunless
        </div>

        @if ($kategori->petunjuk)
            <p class="flex gap-2.5 px-5 py-4 text-sm/6 text-ink-600 dark:text-ink-300">
                <x-ikon nama="info" class="mt-0.5 size-4 shrink-0 text-brand-500"/>
                {{ $kategori->petunjuk }}
            </p>
        @endif
    </div>

    {{-- =========================================================== --}}
    {{-- Rincian pengaduan                                            --}}
    {{-- =========================================================== --}}
    <div class="card p-6 sm:p-7">
        <div class="flex items-center gap-3 border-b border-ink-100 pb-4 dark:border-ink-800">
            <span class="grid size-10 place-items-center rounded-xl bg-brand-100 text-brand-700 dark:bg-brand-900/40 dark:text-brand-300">
                <x-ikon nama="file-text" class="size-5"/>
            </span>
            <div>
                <h3 class="text-base font-bold text-ink-900 dark:text-white">Rincian Pengaduan</h3>
                <p class="text-xs text-ink-500 dark:text-ink-400">Uraikan peristiwa selengkap dan sejelas mungkin</p>
            </div>
        </div>

        <div class="mt-6 space-y-5">
            <x-input nama="judul" label="Judul Pengaduan" wajib :nilai="$laporan->judul"
                     placeholder="Ringkasan singkat inti persoalan, misal: Dugaan pungutan liar pada layanan perizinan"
                     maxlength="200" />

            <div x-data="{ isi: @js(old('uraian', $laporan->uraian ?? '')) }">
                <div class="mb-1.5 flex flex-wrap items-center justify-between gap-2">
                    <label for="uraian" class="text-sm font-semibold text-ink-700 dark:text-ink-200">
                        Uraian Pengaduan <span class="text-rose-500">*</span>
                    </label>
                    <span class="text-xs font-medium" :class="isi.length < 50 ? 'text-rose-500' : 'text-brand-600 dark:text-brand-400'">
                        <span x-text="isi.length"></span> / minimal 50 karakter
                    </span>
                </div>

                <textarea name="uraian" id="uraian" rows="10" x-model="isi"
                          placeholder="Uraikan kronologi kejadian dengan memenuhi unsur 5W + 1H:&#10;&#10;• Apa perbuatan yang diduga melanggar?&#10;• Siapa pihak yang terlibat?&#10;• Di mana peristiwa terjadi?&#10;• Kapan waktu kejadiannya?&#10;• Mengapa hal tersebut dapat terjadi?&#10;• Bagaimana rangkaian kejadiannya?"
                          class="field resize-y leading-7 @error('uraian') field-error @enderror">{{ old('uraian', $laporan->uraian) }}</textarea>

                @error('uraian')
                    <p class="mt-1.5 flex items-center gap-1 text-xs font-medium text-rose-600 dark:text-rose-400">
                        <x-ikon nama="alert" class="size-3.5"/> {{ $message }}
                    </p>
                @else
                    <p class="help">Semakin rinci uraian Anda, semakin cepat Inspektorat dapat menindaklanjutinya.</p>
                @enderror
            </div>

            <x-select nama="opd_id" label="Perangkat Daerah yang Dilaporkan"
                      :nilai="$laporan->opd_id" kosong="— Tidak diketahui / tidak spesifik —"
                      :pilihan="$opdList->pluck('nama', 'id')"
                      bantuan="Pilih OPD tempat dugaan pelanggaran terjadi" />

            <div class="grid gap-5 sm:grid-cols-3">
                <x-input nama="lokasi_kejadian" label="Lokasi Kejadian" ikon="map-pin"
                         :nilai="$laporan->lokasi_kejadian" placeholder="Unit kerja / alamat" />

                <x-input nama="tanggal_kejadian" label="Tanggal Kejadian" tipe="date"
                         :nilai="$laporan->tanggal_kejadian?->toDateString()" max="{{ date('Y-m-d') }}" />

                <x-input nama="nilai_kerugian" label="Perkiraan Kerugian (Rp)" tipe="number"
                         :nilai="$laporan->nilai_kerugian" placeholder="0" min="0" step="1000"
                         bantuan="Kosongkan bila tidak ada" />
            </div>
        </div>
    </div>

    {{-- =========================================================== --}}
    {{-- Pihak yang diduga terlibat                                   --}}
    {{-- =========================================================== --}}
    <div class="card p-6 sm:p-7"
         x-data="barisDinamis(@js($terlaporAwal), { nama: '', jabatan: '', instansi: '', klasifikasi: 'lainnya' })">

        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-ink-100 pb-4 dark:border-ink-800">
            <div class="flex items-center gap-3">
                <span class="grid size-10 place-items-center rounded-xl bg-brand-100 text-brand-700 dark:bg-brand-900/40 dark:text-brand-300">
                    <x-ikon nama="users" class="size-5"/>
                </span>
                <div>
                    <h3 class="text-base font-bold text-ink-900 dark:text-white">
                        Pihak yang Diduga Terlibat <span class="text-rose-500">*</span>
                    </h3>
                    <p class="text-xs text-ink-500 dark:text-ink-400">Wajib diisi minimal satu pihak saat mengirim pengaduan</p>
                </div>
            </div>

            <button type="button" @click="tambah()" class="btn btn-primary btn-sm">
                <x-ikon nama="plus" class="size-3.5"/> Tambah Pihak
            </button>
        </div>

        @error('terlapor')
            <p class="mt-4 flex items-center gap-1.5 rounded-xl bg-rose-50 px-3 py-2 text-xs font-medium text-rose-600 dark:bg-rose-950/30 dark:text-rose-400">
                <x-ikon nama="alert" class="size-3.5"/> {{ $message }}
            </p>
        @enderror

        <div class="mt-5 space-y-4">
            <template x-for="(pihak, i) in baris" :key="i">
                <div class="relative rounded-2xl border border-ink-200 bg-ink-50/60 p-4 dark:border-ink-700 dark:bg-ink-950/40">
                    <div class="mb-3 flex items-center justify-between">
                        <span class="chip bg-brand-100 font-bold text-brand-700 dark:bg-brand-900/40 dark:text-brand-300">
                            Pihak <span x-text="i + 1"></span>
                        </span>
                        <button type="button" @click="hapus(i)" x-show="baris.length > 1"
                                class="btn btn-ghost btn-sm text-rose-600 hover:bg-rose-50 dark:text-rose-400 dark:hover:bg-rose-950/40">
                            <x-ikon nama="trash" class="size-3.5"/> Hapus
                        </button>
                    </div>

                    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                        <input type="text" :name="`terlapor[${i}][nama]`" x-model="pihak.nama"
                               placeholder="Nama lengkap" class="field lg:col-span-1">
                        <input type="text" :name="`terlapor[${i}][jabatan]`" x-model="pihak.jabatan"
                               placeholder="Jabatan" class="field">
                        <input type="text" :name="`terlapor[${i}][instansi]`" x-model="pihak.instansi"
                               placeholder="Instansi / unit kerja" class="field">
                        <select :name="`terlapor[${i}][klasifikasi]`" x-model="pihak.klasifikasi" class="field">
                            @foreach (\App\Models\Terlapor::KLASIFIKASI as $kunci => $label)
                                <option value="{{ $kunci }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </template>
        </div>
    </div>

    {{-- =========================================================== --}}
    {{-- Lampiran                                                     --}}
    {{-- =========================================================== --}}
    <div class="card p-6 sm:p-7"
         x-data="{
             berkas: [{ nama: '', ukuran: 0 }],
             tambah() { if (this.berkas.length < 10) this.berkas.push({ nama: '', ukuran: 0 }) },
             hapus(i) { this.berkas.splice(i, 1); if (!this.berkas.length) this.tambah() },
             pilih(i, e) {
                 const f = e.target.files[0];
                 this.berkas[i].nama = f ? f.name : '';
                 this.berkas[i].ukuran = f ? f.size : 0;
             },
             get total() { return this.berkas.reduce((a, b) => a + b.ukuran, 0) },
             format(b) {
                 if (!b) return '0 MB';
                 return (b / 1048576).toFixed(2) + ' MB';
             },
         }">

        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-ink-100 pb-4 dark:border-ink-800">
            <div class="flex items-center gap-3">
                <span class="grid size-10 place-items-center rounded-xl bg-brand-100 text-brand-700 dark:bg-brand-900/40 dark:text-brand-300">
                    <x-ikon nama="paper-clip" class="size-5"/>
                </span>
                <div>
                    <h3 class="text-base font-bold text-ink-900 dark:text-white">Lampiran Bukti Pendukung</h3>
                    <p class="text-xs text-ink-500 dark:text-ink-400">Opsional, namun sangat membantu proses penanganan</p>
                </div>
            </div>

            <button type="button" @click="tambah()" class="btn btn-primary btn-sm">
                <x-ikon nama="plus" class="size-3.5"/> Tambah Berkas
            </button>
        </div>

        {{-- Lampiran yang sudah tersimpan (mode ubah draf) --}}
        @if ($laporan->exists && $laporan->lampiran->isNotEmpty())
            <div class="mt-5 space-y-2">
                <p class="text-xs font-bold tracking-wider text-ink-500 uppercase dark:text-ink-400">Berkas tersimpan</p>
                @foreach ($laporan->lampiran as $berkas)
                    <div class="flex items-center gap-3 rounded-xl border border-ink-200 bg-white px-4 py-3 dark:border-ink-700 dark:bg-ink-950/40">
                        <span class="grid size-9 shrink-0 place-items-center rounded-lg bg-brand-100 text-brand-700 dark:bg-brand-900/40 dark:text-brand-300">
                            <x-ikon :nama="$berkas->is_gambar ? 'image' : 'file-text'" class="size-4"/>
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold text-ink-900 dark:text-white">{{ $berkas->nama_file }}</p>
                            <p class="text-xs text-ink-500 dark:text-ink-400">
                                {{ $berkas->ukuran_terbaca }}{{ $berkas->keterangan ? ' · '.$berkas->keterangan : '' }}
                            </p>
                        </div>
                        <button type="button"
                                onclick="document.getElementById('hapus-lampiran-{{ $berkas->id }}').submit()"
                                class="btn btn-ghost btn-sm text-rose-600 hover:bg-rose-50 dark:text-rose-400 dark:hover:bg-rose-950/40">
                            <x-ikon nama="trash" class="size-3.5"/>
                        </button>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="mt-5 space-y-3">
            <template x-for="(b, i) in berkas" :key="i">
                <div class="rounded-2xl border border-ink-200 bg-ink-50/60 p-4 dark:border-ink-700 dark:bg-ink-950/40">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                        <span class="chip shrink-0 bg-brand-100 font-bold text-brand-700 dark:bg-brand-900/40 dark:text-brand-300">
                            #<span x-text="i + 1"></span>
                        </span>

                        <input type="file" :name="`lampiran[${i}][file]`" @change="pilih(i, $event)"
                               class="w-full text-sm text-ink-600 file:mr-3 file:cursor-pointer file:rounded-lg file:border-0 file:bg-brand-600 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-brand-700 dark:text-ink-300">

                        <input type="text" :name="`lampiran[${i}][keterangan]`"
                               placeholder="Keterangan berkas" class="field sm:max-w-56">

                        <div class="flex shrink-0 items-center gap-2">
                            <span class="text-xs font-semibold text-ink-500 tabular-nums dark:text-ink-400" x-text="format(b.ukuran)"></span>
                            <button type="button" @click="hapus(i)"
                                    class="btn btn-ghost btn-sm text-rose-600 hover:bg-rose-50 dark:text-rose-400 dark:hover:bg-rose-950/40">
                                <x-ikon nama="trash" class="size-3.5"/>
                            </button>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <div class="mt-4 flex flex-wrap items-center justify-between gap-3 rounded-xl bg-ink-50 px-4 py-3 dark:bg-ink-800/50">
            <p class="text-xs/6 text-ink-500 dark:text-ink-400">
                Format: <strong>zip, rar, doc, docx, xls, xlsx, ppt, pptx, pdf, jpg, png, gif, mp3, mp4, mov, 3gp</strong> ·
                Maksimal <strong>50 MB</strong> per berkas · Maksimal 10 berkas
            </p>
            <p class="text-xs font-bold text-ink-700 dark:text-ink-200">
                Total: <span x-text="format(total)" class="tabular-nums"></span>
            </p>
        </div>
    </div>

    {{-- =========================================================== --}}
    {{-- Anonimitas & persetujuan                                     --}}
    {{-- =========================================================== --}}
    <div class="card p-6 sm:p-7">
        <div class="flex items-center gap-3 border-b border-ink-100 pb-4 dark:border-ink-800">
            <span class="grid size-10 place-items-center rounded-xl bg-brand-100 text-brand-700 dark:bg-brand-900/40 dark:text-brand-300">
                <x-ikon nama="lock" class="size-5"/>
            </span>
            <div>
                <h3 class="text-base font-bold text-ink-900 dark:text-white">Kerahasiaan &amp; Persetujuan</h3>
                <p class="text-xs text-ink-500 dark:text-ink-400">Atur tingkat keterbukaan identitas Anda</p>
            </div>
        </div>

        <div class="mt-5 space-y-4" x-data="{ anonim: {{ old('is_anonymous', $laporan->is_anonymous) ? 'true' : 'false' }} }">
            <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-ink-200 p-4 transition hover:border-brand-300 hover:bg-brand-50/50 dark:border-ink-700 dark:hover:border-brand-700 dark:hover:bg-brand-950/20">
                <input type="checkbox" name="is_anonymous" value="1" x-model="anonim"
                       class="mt-0.5 size-4 rounded border-ink-300 text-brand-600 focus:ring-2 focus:ring-brand-500/30 dark:border-ink-600 dark:bg-ink-900">
                <span>
                    <span class="block text-sm font-bold text-ink-900 dark:text-white">Kirim sebagai pengaduan anonim</span>
                    <span class="mt-1 block text-xs/6 text-ink-500 dark:text-ink-400">
                        Identitas Anda tidak akan ditampilkan kepada petugas yang menangani berkas.
                    </span>
                </span>
            </label>

            <div x-show="anonim" x-collapse x-cloak>
                <div class="flex gap-3 rounded-xl border border-gold-300 bg-gold-50 p-4 dark:border-gold-800/60 dark:bg-gold-950/25">
                    <x-ikon nama="alert" class="mt-0.5 size-4 shrink-0 text-gold-600 dark:text-gold-400"/>
                    <p class="text-xs/6 text-gold-900 dark:text-gold-200">
                        Dengan memilih anonim, Inspektorat menjadi terbatas dalam meminta klarifikasi maupun data tambahan
                        kepada Anda. Hal ini dapat memperlambat proses pendalaman materi aduan.
                    </p>
                </div>
            </div>

            <div class="rounded-xl bg-ink-50 p-4 dark:bg-ink-800/50">
                <p class="flex items-center gap-2 text-xs font-bold text-ink-700 dark:text-ink-200">
                    <x-ikon nama="alert" class="size-4 text-gold-500"/> PERHATIAN
                </p>
                <p class="mt-2 text-xs/6 text-ink-600 dark:text-ink-300">
                    Sebelum mengirim pengaduan ini, mohon diingat bahwa hanya pengaduan yang memenuhi kriteria
                    yang akan diproses lebih lanjut. Kami mengharapkan keseriusan pengaduan dengan melampirkan
                    data pendukung yang memadai. Menyampaikan keterangan palsu dapat dikenai sanksi sesuai
                    ketentuan peraturan perundang-undangan.
                </p>
            </div>

            <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-ink-200 p-4 transition hover:border-brand-300 hover:bg-brand-50/50 dark:border-ink-700 dark:hover:border-brand-700 dark:hover:bg-brand-950/20">
                <input type="checkbox" name="setuju" value="1" @checked(old('setuju'))
                       class="mt-0.5 size-4 rounded border-ink-300 text-brand-600 focus:ring-2 focus:ring-brand-500/30 dark:border-ink-600 dark:bg-ink-900">
                <span class="text-xs/6 text-ink-600 dark:text-ink-300">
                    Saya menyatakan bahwa informasi yang saya sampaikan adalah <strong>benar</strong> dan dapat
                    dipertanggungjawabkan, serta menyetujui syarat dan ketentuan yang berlaku pada
                    Whistleblowing System Inspektorat Kabupaten Pringsewu.
                </span>
            </label>
        </div>
    </div>

    {{-- =========================================================== --}}
    {{-- Aksi                                                         --}}
    {{-- =========================================================== --}}
    <div class="card sticky bottom-4 flex flex-wrap items-center justify-between gap-3 p-4">
        <a href="{{ $laporan->exists ? route('pelapor.laporan.show', $laporan) : route('pelapor.laporan.index') }}"
           class="btn btn-ghost text-rose-600 hover:bg-rose-50 dark:text-rose-400 dark:hover:bg-rose-950/40">
            <x-ikon nama="x-circle" class="size-4"/> Batal
        </a>

        <div class="flex flex-wrap gap-3">
            <button type="submit" name="aksi" value="draft" class="btn btn-outline" :disabled="mengirim">
                <x-ikon nama="document" class="size-4"/> Simpan Draf
            </button>
            <button type="submit" name="aksi" value="kirim" class="btn btn-primary" :disabled="mengirim">
                <x-ikon nama="send" class="size-4"/>
                <span x-show="!mengirim">Kirim Pengaduan</span>
                <span x-show="mengirim" x-cloak>Mengirim…</span>
            </button>
        </div>
    </div>
</form>

{{-- Formulir tersembunyi untuk menghapus lampiran tersimpan --}}
@if ($laporan->exists)
    @foreach ($laporan->lampiran as $berkas)
        <form id="hapus-lampiran-{{ $berkas->id }}" method="POST"
              action="{{ route('pelapor.laporan.lampiran.hapus', [$laporan, $berkas]) }}" class="hidden">
            @csrf @method('DELETE')
        </form>
    @endforeach
@endif

@endsection
