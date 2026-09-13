@extends('layouts.app')

@section('judul', 'Tinjau Pengaduan')
@section('subjudul', $laporan->nomor_tiket)

@section('konten')

@php
    $isAdmin = auth()->user()->isAdmin();
    $aktif = ! $laporan->isSelesai();
@endphp

    <div class="flex flex-wrap items-center justify-between gap-3">
        <a href="{{ route('admin.laporan.index') }}" class="btn btn-ghost btn-sm">
            <x-ikon nama="arrow-left" class="size-4"/> Kembali ke daftar pengaduan
        </a>

        <a href="{{ route('admin.laporan.cetak', $laporan) }}" target="_blank" class="btn btn-outline btn-sm">
            <x-ikon nama="printer" class="size-4"/> Cetak Berkas
        </a>
    </div>

    {{-- Kepala --}}
    <div class="card overflow-hidden">
        <div class="relative overflow-hidden bg-gradient-to-br from-brand-700 via-brand-800 to-ink-950 p-6 sm:p-7">
            <div class="bg-grid absolute inset-0 opacity-20"></div>

            <div class="relative flex flex-wrap items-start justify-between gap-5">
                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="badge bg-white/15 font-mono text-white ring-white/20">{{ $laporan->nomor_tiket }}</span>
                        <x-status :laporan="$laporan" />
                        <span class="badge bg-white/10 text-white/80 ring-white/20">{{ $laporan->prioritas_label }}</span>
                        @if ($laporan->is_anonymous)
                            <span class="badge bg-white/10 text-white/80 ring-white/20">
                                <x-ikon nama="lock" class="size-3"/> Anonim
                            </span>
                        @endif
                    </div>

                    <h2 class="mt-3 text-xl leading-snug font-extrabold text-white sm:text-2xl">{{ $laporan->judul }}</h2>

                    <div class="mt-3 flex flex-wrap items-center gap-x-5 gap-y-2 text-xs text-white/60">
                        <span class="flex items-center gap-1.5"><x-ikon nama="tag" class="size-3.5"/> {{ $laporan->kategori->nama }}</span>
                        <span class="flex items-center gap-1.5"><x-ikon nama="send" class="size-3.5"/> {{ $laporan->submitted_at?->translatedFormat('d F Y, H:i') }}</span>
                        @if ($laporan->deadline)
                            <span class="flex items-center gap-1.5 {{ $laporan->deadline->isPast() && $aktif ? 'font-bold text-rose-300' : '' }}">
                                <x-ikon nama="clock" class="size-3.5"/> Tenggat {{ $laporan->deadline->translatedFormat('d M Y') }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="relative mt-6 space-y-2">
                <div class="flex justify-between text-xs font-semibold text-white/70">
                    <span>Kemajuan penanganan</span>
                    <span class="text-white">{{ $laporan->progres }}%</span>
                </div>
                <div class="h-2 overflow-hidden rounded-full bg-white/15">
                    <div class="h-full rounded-full bg-gradient-to-r from-brand-300 to-gold-300 transition-all duration-1000"
                         style="width: {{ $laporan->progres }}%"></div>
                </div>
            </div>
        </div>

        {{-- Panel tindakan --}}
        <div class="flex flex-wrap items-center gap-2 border-b border-ink-100 bg-ink-50/60 px-6 py-4 dark:border-ink-800 dark:bg-ink-800/30">
            <span class="mr-1 text-xs font-bold tracking-wider text-ink-500 uppercase dark:text-ink-400">Tindakan:</span>

            @if ($laporan->status === \App\Models\Laporan::STATUS_TERKIRIM)
                <form method="POST" action="{{ route('admin.laporan.verifikasi', $laporan) }}">
                    @csrf
                    <button class="btn btn-outline btn-sm">
                        <x-ikon nama="shield-check" class="size-3.5"/> Mulai Verifikasi
                    </button>
                </form>
            @endif

            @if ($aktif)
                <button type="button" @click="$dispatch('buka-modal', 'terima')" class="btn btn-primary btn-sm">
                    <x-ikon nama="check" class="size-3.5"/>
                    {{ $laporan->status === \App\Models\Laporan::STATUS_DIPROSES ? 'Perbarui Penugasan' : 'Terima & Tindak Lanjuti' }}
                </button>

                <button type="button" @click="$dispatch('buka-modal', 'progres')" class="btn btn-outline btn-sm">
                    <x-ikon nama="refresh" class="size-3.5"/> Catat Perkembangan
                </button>

                @if ($isAdmin)
                    <button type="button" @click="$dispatch('buka-modal', 'disposisi')" class="btn btn-outline btn-sm">
                        <x-ikon nama="users" class="size-3.5"/> Disposisi
                    </button>
                @endif

                <button type="button" @click="$dispatch('buka-modal', 'selesai')" class="btn btn-outline btn-sm text-emerald-700 hover:border-emerald-300 hover:bg-emerald-50 dark:text-emerald-400 dark:hover:bg-emerald-950/30">
                    <x-ikon nama="check-badge" class="size-3.5"/> Selesaikan
                </button>

                <button type="button" @click="$dispatch('buka-modal', 'tolak')" class="btn btn-outline btn-sm text-rose-600 hover:border-rose-300 hover:bg-rose-50 dark:text-rose-400 dark:hover:bg-rose-950/30">
                    <x-ikon nama="x-circle" class="size-3.5"/> Tolak
                </button>
            @elseif ($isAdmin)
                <button type="button" @click="$dispatch('buka-modal', 'buka-kembali')" class="btn btn-outline btn-sm">
                    <x-ikon nama="refresh" class="size-3.5"/> Buka Kembali
                </button>
            @endif
        </div>
    </div>

    <div class="grid gap-5 lg:grid-cols-3">

        {{-- =================== Kolom utama =================== --}}
        <div class="space-y-5 lg:col-span-2">

            {{-- Uraian --}}
            <div class="card p-6">
                <h3 class="flex items-center gap-2 text-base font-bold text-ink-900 dark:text-white">
                    <x-ikon nama="file-text" class="size-5 text-brand-500"/> Uraian Pengaduan
                </h3>
                <div class="isi-teks mt-4 whitespace-pre-line">{{ $laporan->uraian }}</div>

                <dl class="mt-6 grid gap-4 border-t border-ink-100 pt-5 sm:grid-cols-2 dark:border-ink-800">
                    @foreach ([
                        ['building', 'OPD Terlapor', $laporan->opd?->nama ?? 'Tidak disebutkan'],
                        ['map-pin', 'Lokasi Kejadian', $laporan->lokasi_kejadian ?: 'Tidak disebutkan'],
                        ['calendar', 'Tanggal Kejadian', $laporan->tanggal_kejadian?->translatedFormat('d F Y') ?? 'Tidak disebutkan'],
                        ['banknote', 'Perkiraan Kerugian', $laporan->nilai_kerugian ? 'Rp '.number_format((float) $laporan->nilai_kerugian, 0, ',', '.') : 'Tidak ada'],
                    ] as [$ikon, $label, $nilai])
                        <div>
                            <dt class="flex items-center gap-1.5 text-xs font-semibold text-ink-500 dark:text-ink-400">
                                <x-ikon :nama="$ikon" class="size-3.5"/> {{ $label }}
                            </dt>
                            <dd class="mt-1 text-sm font-semibold text-ink-900 dark:text-white">{{ $nilai }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>

            {{-- Pihak terlapor --}}
            <div class="card p-6">
                <h3 class="flex items-center gap-2 text-base font-bold text-ink-900 dark:text-white">
                    <x-ikon nama="users" class="size-5 text-brand-500"/> Pihak yang Diduga Terlibat
                </h3>

                <div class="mt-4 overflow-x-auto">
                    <table class="tabel">
                        <thead>
                            <tr><th>Nama</th><th>Jabatan</th><th>Instansi</th><th>Klasifikasi</th></tr>
                        </thead>
                        <tbody>
                            @forelse ($laporan->terlapor as $pihak)
                                <tr>
                                    <td class="font-semibold text-ink-900 dark:text-white">{{ $pihak->nama }}</td>
                                    <td>{{ $pihak->jabatan ?: '—' }}</td>
                                    <td>{{ $pihak->instansi ?: '—' }}</td>
                                    <td><span class="chip bg-brand-100 text-brand-700 dark:bg-brand-900/40 dark:text-brand-300">{{ $pihak->klasifikasi_label }}</span></td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-ink-500">Tidak ada data pihak terlapor.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Lampiran --}}
            <div class="card p-6">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <h3 class="flex items-center gap-2 text-base font-bold text-ink-900 dark:text-white">
                        <x-ikon nama="paper-clip" class="size-5 text-brand-500"/>
                        Lampiran <span class="chip bg-ink-100 text-ink-500 dark:bg-ink-800 dark:text-ink-400">{{ $laporan->lampiran->count() }}</span>
                    </h3>
                    <button type="button" @click="$dispatch('buka-modal', 'unggah')" class="btn btn-outline btn-sm">
                        <x-ikon nama="upload" class="size-3.5"/> Unggah Dokumen
                    </button>
                </div>

                @if ($laporan->lampiran->isEmpty())
                    <p class="mt-4 text-sm text-ink-500 dark:text-ink-400">Belum ada lampiran pada berkas ini.</p>
                @else
                    <div class="mt-4 grid gap-3 sm:grid-cols-2">
                        @foreach ($laporan->lampiran as $berkas)
                            <a href="{{ route('admin.laporan.lampiran.unduh', [$laporan, $berkas]) }}"
                               class="group flex items-center gap-3 rounded-xl border p-3.5 transition hover:bg-brand-50/50 dark:hover:bg-brand-950/20
                               {{ $berkas->is_internal ? 'border-gold-300 dark:border-gold-800/60' : 'border-ink-200 hover:border-brand-300 dark:border-ink-700 dark:hover:border-brand-700' }}">
                                <span class="grid size-10 shrink-0 place-items-center rounded-lg {{ $berkas->is_internal ? 'bg-gold-100 text-gold-700 dark:bg-gold-900/40 dark:text-gold-300' : 'bg-brand-100 text-brand-700 dark:bg-brand-900/40 dark:text-brand-300' }}">
                                    <x-ikon :nama="$berkas->is_gambar ? 'image' : 'file-text'" class="size-5"/>
                                </span>
                                <span class="min-w-0 flex-1">
                                    <span class="block truncate text-sm font-semibold text-ink-900 dark:text-white">{{ $berkas->nama_file }}</span>
                                    <span class="block text-xs text-ink-500 dark:text-ink-400">
                                        {{ strtoupper($berkas->ekstensi) }} · {{ $berkas->ukuran_terbaca }}
                                        {{ $berkas->is_internal ? '· Dokumen internal' : '' }}
                                    </span>
                                </span>
                                <x-ikon nama="download" class="size-4 shrink-0 text-ink-400 transition group-hover:text-brand-600"/>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Komunikasi --}}
            <div class="card overflow-hidden">
                <div class="flex items-center gap-2 border-b border-ink-100 px-6 py-4 dark:border-ink-800">
                    <x-ikon nama="chat" class="size-5 text-brand-500"/>
                    <h3 class="text-base font-bold text-ink-900 dark:text-white">Komunikasi dengan Pelapor</h3>
                </div>

                @if ($laporan->is_anonymous)
                    <div class="flex gap-3 border-b border-ink-100 bg-ink-50 px-6 py-4 dark:border-ink-800 dark:bg-ink-800/40">
                        <x-ikon nama="lock" class="mt-0.5 size-4 shrink-0 text-ink-400"/>
                        <p class="text-xs/6 text-ink-500 dark:text-ink-400">
                            Pelapor memilih anonim. Inspektorat tidak dapat memulai komunikasi, namun pesan
                            yang dikirim pelapor tetap tampil di sini.
                        </p>
                    </div>
                @endif

                <div class="max-h-[30rem] space-y-4 overflow-y-auto px-6 py-5">
                    @forelse ($laporan->pesan as $pesan)
                        @php $dariInspektorat = $pesan->dariInspektorat(); @endphp
                        <div class="flex gap-3 {{ $dariInspektorat ? 'flex-row-reverse' : '' }}">
                            <span class="grid size-9 shrink-0 place-items-center rounded-xl text-xs font-bold text-white
                                {{ $dariInspektorat ? 'bg-gradient-to-br from-brand-500 to-brand-700' : 'bg-gradient-to-br from-ink-400 to-ink-600' }}">
                                {{ $pesan->user->inisial }}
                            </span>

                            <div class="max-w-[80%] {{ $dariInspektorat ? 'text-right' : '' }}">
                                <p class="text-xs font-semibold text-ink-500 dark:text-ink-400">
                                    {{ $dariInspektorat ? $pesan->user->name : ($laporan->is_anonymous ? 'Pelapor Anonim' : $pesan->user->name) }}
                                    · {{ $pesan->created_at->translatedFormat('d M Y, H:i') }}
                                </p>

                                <div class="mt-1.5 inline-block rounded-2xl px-4 py-3 text-left text-sm/6
                                    {{ $dariInspektorat
                                        ? 'rounded-tr-sm bg-brand-600 text-white'
                                        : 'rounded-tl-sm bg-ink-100 text-ink-800 dark:bg-ink-800 dark:text-ink-100' }}">
                                    {{ $pesan->isi }}

                                    @if ($pesan->lampiran_path)
                                        <a href="{{ asset('storage/'.$pesan->lampiran_path) }}" target="_blank"
                                           class="mt-2.5 flex items-center gap-2 rounded-lg px-3 py-2 text-xs font-semibold
                                           {{ $dariInspektorat ? 'bg-white/15' : 'bg-white dark:bg-ink-900' }}">
                                            <x-ikon nama="paper-clip" class="size-3.5"/>
                                            {{ Str::limit($pesan->lampiran_nama, 30) }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <x-kosong ikon="chat" judul="Belum ada percakapan"
                                  pesan="Mintakan klarifikasi atau dokumen tambahan kepada pelapor melalui kolom di bawah."
                                  class="!py-10" />
                    @endforelse
                </div>

                @unless ($laporan->is_anonymous)
                    <form method="POST" action="{{ route('admin.laporan.pesan', $laporan) }}"
                          enctype="multipart/form-data"
                          class="space-y-3 border-t border-ink-100 px-6 py-5 dark:border-ink-800">
                        @csrf
                        <textarea name="isi" rows="3" required
                                  placeholder="Tulis permintaan klarifikasi atau informasi kepada pelapor…"
                                  class="field resize-y">{{ old('isi') }}</textarea>

                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <input type="file" name="lampiran"
                                   class="max-w-full text-xs text-ink-600 file:mr-3 file:cursor-pointer file:rounded-lg file:border-0 file:bg-ink-100 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-ink-700 hover:file:bg-ink-200 dark:text-ink-300 dark:file:bg-ink-800 dark:file:text-ink-200">
                            <button class="btn btn-primary btn-sm">
                                <x-ikon nama="send" class="size-3.5"/> Kirim Pesan
                            </button>
                        </div>
                    </form>
                @endunless
            </div>
        </div>

        {{-- =================== Kolom samping =================== --}}
        <div class="space-y-5">

            {{-- Identitas pelapor --}}
            <div class="card p-6">
                <h3 class="flex items-center gap-2 text-base font-bold text-ink-900 dark:text-white">
                    <x-ikon nama="user-circle" class="size-5 text-brand-500"/> Identitas Pelapor
                </h3>

                @if ($laporan->is_anonymous)
                    <div class="mt-4 rounded-xl border border-ink-200 bg-ink-50 p-4 text-center dark:border-ink-700 dark:bg-ink-800/40">
                        <span class="mx-auto grid size-12 place-items-center rounded-xl bg-ink-200 text-ink-500 dark:bg-ink-700 dark:text-ink-300">
                            <x-ikon nama="lock" class="size-6"/>
                        </span>
                        <p class="mt-3 text-sm font-bold text-ink-800 dark:text-ink-100">Pelapor Anonim</p>
                        <p class="mt-1 text-xs/6 text-ink-500 dark:text-ink-400">
                            Identitas disembunyikan atas permintaan pelapor sesuai ketentuan perlindungan pelapor.
                        </p>
                    </div>
                @else
                    <div class="mt-4 flex items-center gap-3">
                        <span class="grid size-12 shrink-0 place-items-center rounded-xl bg-gradient-to-br from-brand-500 to-brand-700 text-sm font-bold text-white">
                            {{ $laporan->user?->inisial }}
                        </span>
                        <div class="min-w-0">
                            <p class="truncate text-sm font-bold text-ink-900 dark:text-white">{{ $laporan->user?->name }}</p>
                            <p class="truncate text-xs text-ink-500 dark:text-ink-400">{{ $laporan->user?->jabatan ?: 'Jabatan tidak diisi' }}</p>
                        </div>
                    </div>

                    <dl class="mt-4 space-y-2.5 border-t border-ink-100 pt-4 text-xs dark:border-ink-800">
                        @foreach ([
                            ['building', 'OPD', $laporan->user?->opd?->nama ?? '—'],
                            ['mail', 'Email', $laporan->user?->email ?? '—'],
                            ['phone', 'Telepon', $laporan->user?->telepon ?: '—'],
                            ['clipboard', 'NIP', $laporan->user?->nip ?: '—'],
                        ] as [$ikon, $label, $nilai])
                            <div class="flex items-start justify-between gap-3">
                                <dt class="flex shrink-0 items-center gap-1.5 text-ink-500 dark:text-ink-400">
                                    <x-ikon :nama="$ikon" class="size-3.5"/> {{ $label }}
                                </dt>
                                <dd class="text-right font-semibold break-all text-ink-900 dark:text-white">{{ $nilai }}</dd>
                            </div>
                        @endforeach
                    </dl>
                @endif
            </div>

            {{-- Penugasan --}}
            <div class="card p-6">
                <h3 class="flex items-center gap-2 text-base font-bold text-ink-900 dark:text-white">
                    <x-ikon nama="briefcase" class="size-5 text-brand-500"/> Penanganan
                </h3>

                <dl class="mt-4 space-y-3 text-sm">
                    <div class="flex items-start justify-between gap-3">
                        <dt class="shrink-0 text-ink-500 dark:text-ink-400">Petugas</dt>
                        <dd class="text-right font-semibold text-ink-900 dark:text-white">
                            {{ $laporan->petugas?->name ?? 'Belum ditugaskan' }}
                        </dd>
                    </div>
                    <div class="flex items-start justify-between gap-3">
                        <dt class="shrink-0 text-ink-500 dark:text-ink-400">Tenggat</dt>
                        <dd class="text-right font-semibold {{ $laporan->deadline && $laporan->deadline->isPast() && $aktif ? 'text-rose-600 dark:text-rose-400' : 'text-ink-900 dark:text-white' }}">
                            {{ $laporan->deadline?->translatedFormat('d M Y') ?? 'Belum ditetapkan' }}
                        </dd>
                    </div>
                    <div class="flex items-start justify-between gap-3">
                        <dt class="shrink-0 text-ink-500 dark:text-ink-400">Diverifikasi oleh</dt>
                        <dd class="text-right font-semibold text-ink-900 dark:text-white">
                            {{ $laporan->verifikator?->name ?? '—' }}
                        </dd>
                    </div>
                </dl>

                {{-- Prioritas --}}
                <form method="POST" action="{{ route('admin.laporan.prioritas', $laporan) }}" class="mt-5 border-t border-ink-100 pt-4 dark:border-ink-800">
                    @csrf
                    <label class="label">Ubah Prioritas</label>
                    <div class="flex gap-2">
                        <select name="prioritas" class="field">
                            @foreach (\App\Models\Laporan::PRIORITAS as $kunci => $label)
                                <option value="{{ $kunci }}" @selected($laporan->prioritas === $kunci)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <button class="btn btn-primary shrink-0"><x-ikon nama="check" class="size-4"/></button>
                    </div>
                </form>
            </div>

            {{-- Hasil --}}
            @if ($laporan->status === \App\Models\Laporan::STATUS_SELESAI)
                <div class="card overflow-hidden border-emerald-200 dark:border-emerald-900/60">
                    <div class="flex items-center gap-2 bg-emerald-500 px-5 py-3 text-white">
                        <x-ikon nama="check-badge" class="size-5"/>
                        <h3 class="text-sm font-bold">Hasil Penanganan</h3>
                    </div>
                    <div class="space-y-4 p-5 text-sm/6">
                        <div>
                            <p class="text-xs font-bold tracking-wider text-ink-500 uppercase dark:text-ink-400">Kesimpulan</p>
                            <p class="mt-1.5 text-ink-700 dark:text-ink-200">{{ $laporan->kesimpulan }}</p>
                        </div>
                        @if ($laporan->rekomendasi)
                            <div>
                                <p class="text-xs font-bold tracking-wider text-ink-500 uppercase dark:text-ink-400">Rekomendasi</p>
                                <p class="mt-1.5 text-ink-700 dark:text-ink-200">{{ $laporan->rekomendasi }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            @if ($laporan->status === \App\Models\Laporan::STATUS_DITOLAK)
                <div class="card overflow-hidden border-rose-200 dark:border-rose-900/60">
                    <div class="flex items-center gap-2 bg-rose-500 px-5 py-3 text-white">
                        <x-ikon nama="x-circle" class="size-5"/>
                        <h3 class="text-sm font-bold">Alasan Penolakan</h3>
                    </div>
                    <p class="p-5 text-sm/6 text-ink-700 dark:text-ink-200">{{ $laporan->alasan_penolakan }}</p>
                </div>
            @endif

            {{-- Garis waktu --}}
            <div class="card p-6">
                <h3 class="flex items-center gap-2 text-base font-bold text-ink-900 dark:text-white">
                    <x-ikon nama="activity" class="size-5 text-brand-500"/> Riwayat Penanganan
                </h3>

                <ol class="mt-5">
                    @foreach ($laporan->tindakLanjut as $riwayat)
                        <li class="relative flex gap-4 pb-6 last:pb-0">
                            @unless ($loop->last)
                                <span class="absolute top-10 bottom-0 left-[19px] w-px bg-ink-200 dark:bg-ink-800"></span>
                            @endunless

                            <span class="relative grid size-10 shrink-0 place-items-center rounded-xl {{ $riwayat->is_internal
                                ? 'bg-gold-100 text-gold-700 dark:bg-gold-900/40 dark:text-gold-300'
                                : 'bg-brand-100 text-brand-700 dark:bg-brand-900/40 dark:text-brand-300' }}">
                                <x-ikon :nama="$riwayat->tampilan['icon']" class="size-[18px]"/>
                            </span>

                            <div class="min-w-0 flex-1 pt-0.5">
                                <div class="flex flex-wrap items-center gap-2">
                                    <p class="text-sm font-bold text-ink-900 dark:text-white">{{ $riwayat->judul }}</p>
                                    @if ($riwayat->is_internal)
                                        <span class="chip bg-gold-100 text-[10px] text-gold-700 dark:bg-gold-900/40 dark:text-gold-300">Internal</span>
                                    @endif
                                </div>
                                @if ($riwayat->catatan)
                                    <p class="mt-1 text-xs/6 text-ink-600 dark:text-ink-300">{{ $riwayat->catatan }}</p>
                                @endif
                                <p class="mt-1.5 text-[11px] text-ink-400">
                                    {{ $riwayat->user?->name ?? 'Sistem' }} ·
                                    {{ $riwayat->created_at->translatedFormat('d M Y, H:i') }}
                                </p>
                            </div>
                        </li>
                    @endforeach
                </ol>
            </div>
        </div>
    </div>

    {{-- ============================================================= --}}
    {{-- Modal tindakan                                                 --}}
    {{-- ============================================================= --}}

    <x-modal nama="terima" judul="Terima &amp; Tindak Lanjuti Pengaduan"
             keterangan="Tetapkan prioritas dan petugas penanggung jawab.">
        <form method="POST" action="{{ route('admin.laporan.terima', $laporan) }}" class="space-y-4">
            @csrf

            <x-select nama="prioritas" label="Prioritas Penanganan" wajib
                      :nilai="$laporan->prioritas" :pilihan="\App\Models\Laporan::PRIORITAS" />

            @if ($isAdmin)
                <x-select nama="petugas_id" label="Petugas Penanggung Jawab"
                          :nilai="$laporan->petugas_id" kosong="— Tentukan nanti —"
                          :pilihan="$petugasList->pluck('name', 'id')" />
            @endif

            <x-input nama="deadline" label="Tenggat Penyelesaian" tipe="date"
                     :nilai="$laporan->deadline?->toDateString()" min="{{ date('Y-m-d') }}" />

            <x-textarea nama="catatan" label="Catatan Disposisi" baris="3"
                        placeholder="Arahan penanganan untuk petugas (opsional)" />

            <div class="flex justify-end gap-3 pt-2">
                <button type="button" @click="buka = false" class="btn btn-outline">Batal</button>
                <button class="btn btn-primary"><x-ikon nama="check" class="size-4"/> Simpan &amp; Lanjutkan</button>
            </div>
        </form>
    </x-modal>

    <x-modal nama="tolak" judul="Tolak Pengaduan"
             keterangan="Alasan penolakan akan dikirimkan kepada pelapor.">
        <form method="POST" action="{{ route('admin.laporan.tolak', $laporan) }}" class="space-y-4">
            @csrf

            <x-textarea nama="alasan_penolakan" label="Alasan Tidak Dapat Ditindaklanjuti" wajib baris="5"
                        placeholder="Contoh: Materi pengaduan merupakan kewenangan instansi lain sehingga tidak dapat diperiksa Inspektorat."
                        bantuan="Minimal 20 karakter. Jelaskan secara objektif agar pelapor memahami keputusan ini." />

            <div class="flex justify-end gap-3 pt-2">
                <button type="button" @click="buka = false" class="btn btn-outline">Batal</button>
                <button class="btn btn-danger"><x-ikon nama="x-circle" class="size-4"/> Tolak Pengaduan</button>
            </div>
        </form>
    </x-modal>

    @if ($isAdmin)
        <x-modal nama="disposisi" judul="Disposisi Pengaduan"
                 keterangan="Tugaskan berkas ini kepada petugas Inspektorat.">
            <form method="POST" action="{{ route('admin.laporan.disposisi', $laporan) }}" class="space-y-4">
                @csrf

                <x-select nama="petugas_id" label="Petugas Penanggung Jawab" wajib
                          :nilai="$laporan->petugas_id" kosong="— Pilih petugas —"
                          :pilihan="$petugasList->pluck('name', 'id')" />

                <x-input nama="deadline" label="Tenggat Penyelesaian" tipe="date"
                         :nilai="$laporan->deadline?->toDateString()" min="{{ date('Y-m-d') }}" />

                <x-textarea nama="catatan" label="Catatan Disposisi" baris="3"
                            placeholder="Arahan penanganan (hanya terlihat oleh Inspektorat)" />

                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" @click="buka = false" class="btn btn-outline">Batal</button>
                    <button class="btn btn-primary"><x-ikon nama="users" class="size-4"/> Disposisikan</button>
                </div>
            </form>
        </x-modal>
    @endif

    <x-modal nama="progres" judul="Catat Perkembangan Penanganan"
             keterangan="Perkembangan tampil pada garis waktu pengaduan.">
        <form method="POST" action="{{ route('admin.laporan.progres', $laporan) }}" class="space-y-4">
            @csrf

            <x-input nama="judul" label="Judul Perkembangan" wajib
                     placeholder="Contoh: Permintaan keterangan kepada pihak terkait" />

            <x-textarea nama="catatan" label="Uraian Perkembangan" wajib baris="5"
                        placeholder="Jelaskan langkah penanganan yang telah dilakukan." />

            <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-ink-200 p-4 transition hover:border-gold-300 hover:bg-gold-50/50 dark:border-ink-700 dark:hover:border-gold-700 dark:hover:bg-gold-950/20">
                <input type="checkbox" name="is_internal" value="1"
                       class="mt-0.5 size-4 rounded border-ink-300 text-gold-600 focus:ring-2 focus:ring-gold-500/30 dark:border-ink-600 dark:bg-ink-900">
                <span>
                    <span class="block text-sm font-bold text-ink-900 dark:text-white">Catatan internal</span>
                    <span class="mt-0.5 block text-xs/6 text-ink-500 dark:text-ink-400">
                        Hanya terlihat oleh Inspektorat, tidak ditampilkan kepada pelapor.
                    </span>
                </span>
            </label>

            <div class="flex justify-end gap-3 pt-2">
                <button type="button" @click="buka = false" class="btn btn-outline">Batal</button>
                <button class="btn btn-primary"><x-ikon nama="refresh" class="size-4"/> Simpan Perkembangan</button>
            </div>
        </form>
    </x-modal>

    <x-modal nama="selesai" judul="Selesaikan Penanganan" lebar="max-w-xl"
             keterangan="Kesimpulan dan rekomendasi akan dikirimkan kepada pelapor.">
        <form method="POST" action="{{ route('admin.laporan.selesai', $laporan) }}" class="space-y-4">
            @csrf

            <x-textarea nama="kesimpulan" label="Kesimpulan Hasil Pemeriksaan" wajib baris="5"
                        placeholder="Uraikan hasil pemeriksaan atas materi pengaduan."
                        bantuan="Minimal 20 karakter." />

            <x-textarea nama="rekomendasi" label="Rekomendasi Tindak Lanjut" baris="4"
                        placeholder="Contoh: Menjatuhkan hukuman disiplin sesuai ketentuan dan memperbaiki prosedur layanan." />

            <div class="flex justify-end gap-3 pt-2">
                <button type="button" @click="buka = false" class="btn btn-outline">Batal</button>
                <button class="btn btn-primary"><x-ikon nama="check-badge" class="size-4"/> Tandai Selesai</button>
            </div>
        </form>
    </x-modal>

    @if ($isAdmin && $laporan->isSelesai())
        <x-modal nama="buka-kembali" judul="Buka Kembali Pengaduan"
                 keterangan="Berkas akan kembali ke tahap tindak lanjut.">
            <form method="POST" action="{{ route('admin.laporan.buka-kembali', $laporan) }}" class="space-y-4">
                @csrf

                <x-textarea nama="catatan" label="Alasan Membuka Kembali" wajib baris="4"
                            placeholder="Contoh: Ditemukan bukti baru yang perlu didalami lebih lanjut." />

                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" @click="buka = false" class="btn btn-outline">Batal</button>
                    <button class="btn btn-primary"><x-ikon nama="refresh" class="size-4"/> Buka Kembali</button>
                </div>
            </form>
        </x-modal>
    @endif

    <x-modal nama="unggah" judul="Unggah Dokumen Internal"
             keterangan="Dokumen ini hanya dapat diakses oleh Inspektorat.">
        <form method="POST" action="{{ route('admin.laporan.lampiran.unggah', $laporan) }}"
              enctype="multipart/form-data" class="space-y-4">
            @csrf

            <div>
                <label for="file" class="label">Berkas <span class="text-rose-500">*</span></label>
                <input type="file" name="file" id="file" required
                       class="w-full text-sm text-ink-600 file:mr-3 file:cursor-pointer file:rounded-lg file:border-0 file:bg-brand-600 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-brand-700 dark:text-ink-300">
                <p class="help">PDF, DOC, XLS, PPT, JPG, PNG, ZIP, RAR · maksimal 50 MB</p>
            </div>

            <x-input nama="keterangan" label="Keterangan" placeholder="Misal: Berita Acara Permintaan Keterangan" />

            <div class="flex justify-end gap-3 pt-2">
                <button type="button" @click="buka = false" class="btn btn-outline">Batal</button>
                <button class="btn btn-primary"><x-ikon nama="upload" class="size-4"/> Unggah</button>
            </div>
        </form>
    </x-modal>

@endsection
