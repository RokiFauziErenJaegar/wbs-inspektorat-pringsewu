@extends('layouts.app')

@section('judul', 'Rincian Pengaduan')
@section('subjudul', $laporan->nomor_tiket)

@section('konten')

    <div>
        <a href="{{ route('pelapor.laporan.index') }}" class="btn btn-ghost btn-sm">
            <x-ikon nama="arrow-left" class="size-4"/> Kembali ke daftar pengaduan
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
                        @if ($laporan->is_anonymous)
                            <span class="badge bg-white/10 text-white/80 ring-white/20">
                                <x-ikon nama="lock" class="size-3"/> Anonim
                            </span>
                        @endif
                    </div>

                    <h2 class="mt-3 text-xl leading-snug font-extrabold text-white sm:text-2xl">{{ $laporan->judul }}</h2>

                    <div class="mt-3 flex flex-wrap items-center gap-x-5 gap-y-2 text-xs text-white/60">
                        <span class="flex items-center gap-1.5"><x-ikon nama="tag" class="size-3.5"/> {{ $laporan->kategori->nama }}</span>
                        <span class="flex items-center gap-1.5"><x-ikon nama="calendar" class="size-3.5"/> Dibuat {{ $laporan->created_at->translatedFormat('d F Y') }}</span>
                        @if ($laporan->submitted_at)
                            <span class="flex items-center gap-1.5"><x-ikon nama="send" class="size-3.5"/> Dikirim {{ $laporan->submitted_at->translatedFormat('d F Y, H:i') }}</span>
                        @endif
                    </div>
                </div>

                @if ($laporan->isDraft())
                    <div class="flex gap-2">
                        <a href="{{ route('pelapor.laporan.edit', $laporan) }}" class="btn border border-white/20 bg-white/10 text-white backdrop-blur hover:bg-white/20 btn-sm">
                            <x-ikon nama="pencil" class="size-3.5"/> Ubah
                        </a>
                        <button type="button" @click="$dispatch('buka-modal', 'kirim-draf')" class="btn btn-gold btn-sm">
                            <x-ikon nama="send" class="size-3.5"/> Kirim
                        </button>
                    </div>
                @endif
            </div>

            {{-- Kemajuan --}}
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

        {{-- Kode akses --}}
        @unless ($laporan->isDraft())
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-ink-100 bg-brand-50/60 px-6 py-4 dark:border-ink-800 dark:bg-brand-950/20">
                <div class="flex items-center gap-3">
                    <span class="grid size-10 shrink-0 place-items-center rounded-xl bg-brand-600 text-white">
                        <x-ikon nama="key" class="size-5"/>
                    </span>
                    <div>
                        <p class="text-xs font-semibold text-ink-500 dark:text-ink-400">Kode akses untuk pelacakan publik</p>
                        <p class="font-mono text-base font-extrabold tracking-widest text-ink-900 dark:text-white">{{ $laporan->kode_akses }}</p>
                    </div>
                </div>
                <p class="max-w-md text-xs/6 text-ink-500 dark:text-ink-400">
                    Simpan nomor tiket dan kode akses ini untuk melacak status pengaduan melalui halaman
                    <a href="{{ route('lacak.index') }}" class="font-bold text-brand-600 hover:underline dark:text-brand-400">Lacak Aduan</a>
                    tanpa perlu masuk ke akun.
                </p>
            </div>
        @endunless
    </div>

    @if ($laporan->isDraft())
        <div class="flex gap-3 rounded-2xl border border-gold-300 bg-gold-50 p-5 dark:border-gold-800/60 dark:bg-gold-950/25">
            <x-ikon nama="alert" class="mt-0.5 size-5 shrink-0 text-gold-600 dark:text-gold-400"/>
            <div class="text-sm/6 text-gold-900 dark:text-gold-200">
                <p class="font-bold">Pengaduan ini masih berupa draf</p>
                <p class="mt-1">Inspektorat belum menerima pengaduan Anda. Tekan tombol <strong>Kirim</strong> untuk meneruskannya.</p>
            </div>
        </div>
    @endif

    <div class="grid gap-5 lg:grid-cols-3">

        {{-- =================== Kolom utama =================== --}}
        <div class="space-y-5 lg:col-span-2">

            {{-- Uraian --}}
            <div class="card p-6">
                <h3 class="flex items-center gap-2 text-base font-bold text-ink-900 dark:text-white">
                    <x-ikon nama="file-text" class="size-5 text-brand-500"/> Uraian Pengaduan
                </h3>
                <div class="isi-teks mt-4 whitespace-pre-line">{{ $laporan->uraian }}</div>

                <dl class="mt-6 grid gap-4 border-t border-ink-100 pt-5 sm:grid-cols-3 dark:border-ink-800">
                    @foreach ([
                        ['building', 'OPD Terlapor', $laporan->opd?->nama ?? 'Tidak disebutkan'],
                        ['map-pin', 'Lokasi Kejadian', $laporan->lokasi_kejadian ?: 'Tidak disebutkan'],
                        ['calendar', 'Tanggal Kejadian', $laporan->tanggal_kejadian?->translatedFormat('d F Y') ?? 'Tidak disebutkan'],
                    ] as [$ikon, $label, $nilai])
                        <div>
                            <dt class="flex items-center gap-1.5 text-xs font-semibold text-ink-500 dark:text-ink-400">
                                <x-ikon :nama="$ikon" class="size-3.5"/> {{ $label }}
                            </dt>
                            <dd class="mt-1 text-sm font-semibold text-ink-900 dark:text-white">{{ $nilai }}</dd>
                        </div>
                    @endforeach

                    @if ($laporan->nilai_kerugian)
                        <div class="sm:col-span-3">
                            <dt class="flex items-center gap-1.5 text-xs font-semibold text-ink-500 dark:text-ink-400">
                                <x-ikon nama="banknote" class="size-3.5"/> Perkiraan Kerugian
                            </dt>
                            <dd class="mt-1 text-lg font-extrabold text-rose-600 dark:text-rose-400">
                                Rp {{ number_format((float) $laporan->nilai_kerugian, 0, ',', '.') }}
                            </dd>
                        </div>
                    @endif
                </dl>
            </div>

            {{-- Pihak terlapor --}}
            <div class="card p-6">
                <h3 class="flex items-center gap-2 text-base font-bold text-ink-900 dark:text-white">
                    <x-ikon nama="users" class="size-5 text-brand-500"/> Pihak yang Diduga Terlibat
                </h3>

                @if ($laporan->terlapor->isEmpty())
                    <p class="mt-4 text-sm text-ink-500 dark:text-ink-400">Belum ada data pihak terlapor.</p>
                @else
                    <div class="mt-4 space-y-3">
                        @foreach ($laporan->terlapor as $pihak)
                            <div class="flex items-start gap-3 rounded-xl border border-ink-200 p-4 dark:border-ink-700">
                                <span class="grid size-10 shrink-0 place-items-center rounded-xl bg-ink-100 text-ink-500 dark:bg-ink-800 dark:text-ink-400">
                                    <x-ikon nama="user" class="size-5"/>
                                </span>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-bold text-ink-900 dark:text-white">{{ $pihak->nama }}</p>
                                    <p class="mt-0.5 text-xs text-ink-500 dark:text-ink-400">
                                        {{ collect([$pihak->jabatan, $pihak->instansi])->filter()->join(' · ') ?: 'Tidak disebutkan' }}
                                    </p>
                                </div>
                                <span class="chip shrink-0 bg-brand-100 text-brand-700 dark:bg-brand-900/40 dark:text-brand-300">
                                    {{ $pihak->klasifikasi_label }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Lampiran --}}
            <div class="card p-6">
                <h3 class="flex items-center gap-2 text-base font-bold text-ink-900 dark:text-white">
                    <x-ikon nama="paper-clip" class="size-5 text-brand-500"/>
                    Lampiran <span class="chip bg-ink-100 text-ink-500 dark:bg-ink-800 dark:text-ink-400">{{ $laporan->lampiran->count() }}</span>
                </h3>

                @if ($laporan->lampiran->isEmpty())
                    <p class="mt-4 text-sm text-ink-500 dark:text-ink-400">Tidak ada lampiran pada pengaduan ini.</p>
                @else
                    <div class="mt-4 grid gap-3 sm:grid-cols-2">
                        @foreach ($laporan->lampiran as $berkas)
                            <a href="{{ route('pelapor.laporan.lampiran.unduh', [$laporan, $berkas]) }}"
                               class="group flex items-center gap-3 rounded-xl border border-ink-200 p-3.5 transition hover:border-brand-300 hover:bg-brand-50/50 dark:border-ink-700 dark:hover:border-brand-700 dark:hover:bg-brand-950/20">
                                <span class="grid size-10 shrink-0 place-items-center rounded-lg bg-brand-100 text-brand-700 dark:bg-brand-900/40 dark:text-brand-300">
                                    <x-ikon :nama="$berkas->is_gambar ? 'image' : 'file-text'" class="size-5"/>
                                </span>
                                <span class="min-w-0 flex-1">
                                    <span class="block truncate text-sm font-semibold text-ink-900 dark:text-white">{{ $berkas->nama_file }}</span>
                                    <span class="block text-xs text-ink-500 dark:text-ink-400">
                                        {{ strtoupper($berkas->ekstensi) }} · {{ $berkas->ukuran_terbaca }}
                                    </span>
                                </span>
                                <x-ikon nama="download" class="size-4 shrink-0 text-ink-400 transition group-hover:text-brand-600"/>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Komunikasi --}}
            @unless ($laporan->isDraft())
                <div class="card overflow-hidden">
                    <div class="flex items-center gap-2 border-b border-ink-100 px-6 py-4 dark:border-ink-800">
                        <x-ikon nama="chat" class="size-5 text-brand-500"/>
                        <h3 class="text-base font-bold text-ink-900 dark:text-white">Komunikasi dengan Inspektorat</h3>
                    </div>

                    @if ($laporan->is_anonymous)
                        <div class="flex gap-3 border-b border-ink-100 bg-ink-50 px-6 py-4 dark:border-ink-800 dark:bg-ink-800/40">
                            <x-ikon nama="lock" class="mt-0.5 size-4 shrink-0 text-ink-400"/>
                            <p class="text-xs/6 text-ink-500 dark:text-ink-400">
                                Anda mengirim pengaduan ini secara anonim. Inspektorat tidak dapat memulai percakapan,
                                namun Anda tetap dapat menyampaikan informasi tambahan melalui kolom di bawah.
                            </p>
                        </div>
                    @endif

                    <div class="max-h-[30rem] space-y-4 overflow-y-auto px-6 py-5">
                        @forelse ($laporan->pesan as $pesan)
                            @php $dariInspektorat = $pesan->dariInspektorat(); @endphp
                            <div class="flex gap-3 {{ $dariInspektorat ? '' : 'flex-row-reverse' }}">
                                <span class="grid size-9 shrink-0 place-items-center rounded-xl text-xs font-bold text-white
                                    {{ $dariInspektorat ? 'bg-gradient-to-br from-brand-500 to-brand-700' : 'bg-gradient-to-br from-ink-400 to-ink-600' }}">
                                    {{ $dariInspektorat ? 'IN' : $pesan->user->inisial }}
                                </span>

                                <div class="max-w-[80%] {{ $dariInspektorat ? '' : 'text-right' }}">
                                    <p class="text-xs font-semibold text-ink-500 dark:text-ink-400">
                                        {{ $dariInspektorat ? 'Inspektorat' : 'Anda' }} ·
                                        {{ $pesan->created_at->translatedFormat('d M Y, H:i') }}
                                    </p>

                                    <div class="mt-1.5 inline-block rounded-2xl px-4 py-3 text-left text-sm/6
                                        {{ $dariInspektorat
                                            ? 'rounded-tl-sm bg-ink-100 text-ink-800 dark:bg-ink-800 dark:text-ink-100'
                                            : 'rounded-tr-sm bg-brand-600 text-white' }}">
                                        {{ $pesan->isi }}

                                        @if ($pesan->lampiran_path)
                                            <a href="{{ asset('storage/'.$pesan->lampiran_path) }}" target="_blank"
                                               class="mt-2.5 flex items-center gap-2 rounded-lg px-3 py-2 text-xs font-semibold
                                               {{ $dariInspektorat ? 'bg-white dark:bg-ink-900' : 'bg-white/15' }}">
                                                <x-ikon nama="paper-clip" class="size-3.5"/>
                                                {{ Str::limit($pesan->lampiran_nama, 30) }}
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <x-kosong ikon="chat" judul="Belum ada percakapan"
                                      pesan="Gunakan kolom di bawah untuk menyampaikan informasi tambahan kepada Inspektorat."
                                      class="!py-10" />
                        @endforelse
                    </div>

                    <form method="POST" action="{{ route('pelapor.laporan.pesan', $laporan) }}"
                          enctype="multipart/form-data"
                          class="space-y-3 border-t border-ink-100 px-6 py-5 dark:border-ink-800">
                        @csrf

                        <textarea name="isi" rows="3" required
                                  placeholder="Tulis pesan, pertanyaan, atau informasi tambahan…"
                                  class="field resize-y">{{ old('isi') }}</textarea>

                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <input type="file" name="lampiran"
                                   class="max-w-full text-xs text-ink-600 file:mr-3 file:cursor-pointer file:rounded-lg file:border-0 file:bg-ink-100 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-ink-700 hover:file:bg-ink-200 dark:text-ink-300 dark:file:bg-ink-800 dark:file:text-ink-200">
                            <button class="btn btn-primary btn-sm">
                                <x-ikon nama="send" class="size-3.5"/> Kirim Pesan
                            </button>
                        </div>
                    </form>
                </div>
            @endunless
        </div>

        {{-- =================== Kolom samping =================== --}}
        <div class="space-y-5">

            {{-- Hasil penanganan --}}
            @if ($laporan->status === \App\Models\Laporan::STATUS_SELESAI)
                <div class="card overflow-hidden border-emerald-200 dark:border-emerald-900/60">
                    <div class="flex items-center gap-2 bg-emerald-500 px-5 py-3 text-white">
                        <x-ikon nama="check-badge" class="size-5"/>
                        <h3 class="text-sm font-bold">Hasil Penanganan</h3>
                    </div>
                    <div class="space-y-4 p-5">
                        <div>
                            <p class="text-xs font-bold tracking-wider text-ink-500 uppercase dark:text-ink-400">Kesimpulan</p>
                            <p class="mt-1.5 text-sm/6 text-ink-700 dark:text-ink-200">{{ $laporan->kesimpulan }}</p>
                        </div>
                        @if ($laporan->rekomendasi)
                            <div>
                                <p class="text-xs font-bold tracking-wider text-ink-500 uppercase dark:text-ink-400">Rekomendasi</p>
                                <p class="mt-1.5 text-sm/6 text-ink-700 dark:text-ink-200">{{ $laporan->rekomendasi }}</p>
                            </div>
                        @endif
                        <p class="border-t border-ink-100 pt-3 text-xs text-ink-500 dark:border-ink-800 dark:text-ink-400">
                            Diselesaikan {{ $laporan->selesai_at?->translatedFormat('d F Y') }}
                        </p>
                    </div>
                </div>
            @endif

            @if ($laporan->status === \App\Models\Laporan::STATUS_DITOLAK)
                <div class="card overflow-hidden border-rose-200 dark:border-rose-900/60">
                    <div class="flex items-center gap-2 bg-rose-500 px-5 py-3 text-white">
                        <x-ikon nama="x-circle" class="size-5"/>
                        <h3 class="text-sm font-bold">Tidak Dapat Ditindaklanjuti</h3>
                    </div>
                    <div class="p-5">
                        <p class="text-sm/6 text-ink-700 dark:text-ink-200">{{ $laporan->alasan_penolakan }}</p>
                    </div>
                </div>
            @endif

            {{-- Garis waktu --}}
            <div class="card p-6">
                <h3 class="flex items-center gap-2 text-base font-bold text-ink-900 dark:text-white">
                    <x-ikon nama="activity" class="size-5 text-brand-500"/> Riwayat Penanganan
                </h3>

                <ol class="mt-5">
                    @forelse ($laporan->tindakLanjut as $riwayat)
                        <li class="relative flex gap-4 pb-6 last:pb-0">
                            @unless ($loop->last)
                                <span class="absolute top-10 bottom-0 left-[19px] w-px bg-ink-200 dark:bg-ink-800"></span>
                            @endunless

                            <span class="relative grid size-10 shrink-0 place-items-center rounded-xl bg-brand-100 text-brand-700 dark:bg-brand-900/40 dark:text-brand-300">
                                <x-ikon :nama="$riwayat->tampilan['icon']" class="size-[18px]"/>
                            </span>

                            <div class="min-w-0 flex-1 pt-0.5">
                                <p class="text-sm font-bold text-ink-900 dark:text-white">{{ $riwayat->judul }}</p>
                                @if ($riwayat->catatan)
                                    <p class="mt-1 text-xs/6 text-ink-600 dark:text-ink-300">{{ $riwayat->catatan }}</p>
                                @endif
                                <p class="mt-1.5 text-[11px] text-ink-400">
                                    {{ $riwayat->created_at->translatedFormat('d M Y, H:i') }} WIB
                                </p>
                            </div>
                        </li>
                    @empty
                        <li class="text-sm text-ink-500 dark:text-ink-400">Belum ada riwayat penanganan.</li>
                    @endforelse
                </ol>
            </div>

            {{-- Informasi --}}
            <div class="card p-6">
                <h3 class="text-base font-bold text-ink-900 dark:text-white">Informasi Pengaduan</h3>

                <dl class="mt-4 space-y-3.5 text-sm">
                    @foreach ([
                        ['Nomor Tiket', $laporan->nomor_tiket],
                        ['Kategori', $laporan->kategori->nama],
                        ['Status', $laporan->status_label],
                        ['Prioritas', $laporan->prioritas_label],
                        ['Petugas Penanggung Jawab', $laporan->petugas?->name ?? 'Belum ditugaskan'],
                        ['Terakhir Diperbarui', $laporan->updated_at->diffForHumans()],
                    ] as [$label, $nilai])
                        <div class="flex justify-between gap-4">
                            <dt class="shrink-0 text-ink-500 dark:text-ink-400">{{ $label }}</dt>
                            <dd class="text-right font-semibold text-ink-900 dark:text-white">{{ $nilai }}</dd>
                        </div>
                    @endforeach
                </dl>

                @if ($laporan->isDraft())
                    <button type="button" @click="$dispatch('buka-modal', 'hapus-draf')"
                            class="btn btn-outline mt-5 w-full text-rose-600 hover:border-rose-300 hover:bg-rose-50 dark:text-rose-400 dark:hover:bg-rose-950/30">
                        <x-ikon nama="trash" class="size-4"/> Hapus Draf
                    </button>
                @endif
            </div>
        </div>
    </div>

    {{-- Modal kirim draf --}}
    @if ($laporan->isDraft())
        <x-modal nama="kirim-draf" judul="Kirim pengaduan ke Inspektorat?"
                 keterangan="Setelah dikirim, pengaduan tidak dapat diubah lagi.">
            <p class="text-sm/6 text-ink-600 dark:text-ink-300">
                Pastikan uraian kejadian, pihak terlapor, dan lampiran bukti telah lengkap.
                Pengaduan akan langsung masuk ke dasbor Inspektorat Kabupaten Pringsewu untuk diverifikasi.
            </p>

            <form method="POST" action="{{ route('pelapor.laporan.kirim', $laporan) }}" class="mt-6 flex justify-end gap-3">
                @csrf
                <button type="button" @click="buka = false" class="btn btn-outline">Periksa Lagi</button>
                <button class="btn btn-primary"><x-ikon nama="send" class="size-4"/> Ya, Kirim Sekarang</button>
            </form>
        </x-modal>

        <x-modal nama="hapus-draf" judul="Hapus draf pengaduan?" keterangan="Tindakan ini tidak dapat dibatalkan.">
            <p class="text-sm/6 text-ink-600 dark:text-ink-300">
                Draf beserta seluruh lampirannya akan dihapus permanen dari sistem.
            </p>

            <form method="POST" action="{{ route('pelapor.laporan.destroy', $laporan) }}" class="mt-6 flex justify-end gap-3">
                @csrf @method('DELETE')
                <button type="button" @click="buka = false" class="btn btn-outline">Batal</button>
                <button class="btn btn-danger"><x-ikon nama="trash" class="size-4"/> Ya, Hapus</button>
            </form>
        </x-modal>
    @endif

@endsection
