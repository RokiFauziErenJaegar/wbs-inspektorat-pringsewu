@extends('layouts.app')

@section('judul', 'Pengaduan Masuk')
@section('subjudul', 'Seluruh pengaduan yang diterima Inspektorat')

@section('konten')

    {{-- Penyaring --}}
    <div class="card p-5" x-data="{ lanjutan: {{ request()->hasAny(['opd', 'prioritas', 'petugas', 'dari', 'sampai']) ? 'true' : 'false' }} }">
        <form method="GET" class="space-y-4">

            <div class="flex flex-wrap gap-2">
                @php $tabStatus = ['' => 'Semua'] + \App\Models\Laporan::STATUS; unset($tabStatus['draft']); @endphp

                @foreach ($tabStatus as $nilai => $label)
                    <a href="{{ route('admin.laporan.index', array_filter(['status' => $nilai] + request()->except(['status', 'page']))) }}"
                       class="rounded-xl px-3.5 py-2 text-xs font-bold transition {{ (string) request('status', '') === (string) $nilai
                           ? 'bg-brand-600 text-white shadow-[var(--shadow-glow)]'
                           : 'bg-ink-100 text-ink-600 hover:bg-ink-200 dark:bg-ink-800 dark:text-ink-300 dark:hover:bg-ink-700' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            <div class="flex flex-col gap-3 sm:flex-row">
                <input type="hidden" name="status" value="{{ request('status') }}">

                <div class="relative flex-1">
                    <span class="pointer-events-none absolute inset-y-0 left-0 grid w-11 place-items-center text-ink-400">
                        <x-ikon nama="search" class="size-[18px]"/>
                    </span>
                    <input type="search" name="q" value="{{ request('q') }}"
                           placeholder="Cari nomor tiket, judul, atau isi pengaduan…" class="field pl-11">
                </div>

                <select name="kategori" class="field sm:max-w-52">
                    <option value="">Semua kategori</option>
                    @foreach ($kategoriList as $item)
                        <option value="{{ $item->id }}" @selected(request('kategori') == $item->id)>{{ $item->nama }}</option>
                    @endforeach
                </select>

                <div class="flex gap-2">
                    <button class="btn btn-primary"><x-ikon nama="filter" class="size-4"/> Saring</button>
                    <button type="button" @click="lanjutan = !lanjutan" class="btn btn-outline">
                        <x-ikon nama="cog" class="size-4"/>
                        <span class="hidden sm:inline">Lanjutan</span>
                    </button>
                </div>
            </div>

            <div x-show="lanjutan" x-collapse x-cloak>
                <div class="grid gap-3 border-t border-ink-100 pt-4 sm:grid-cols-2 lg:grid-cols-5 dark:border-ink-800">
                    <select name="opd" class="field">
                        <option value="">Semua OPD terlapor</option>
                        @foreach ($opdList as $item)
                            <option value="{{ $item->id }}" @selected(request('opd') == $item->id)>{{ $item->nama }}</option>
                        @endforeach
                    </select>

                    <select name="prioritas" class="field">
                        <option value="">Semua prioritas</option>
                        @foreach (\App\Models\Laporan::PRIORITAS as $kunci => $label)
                            <option value="{{ $kunci }}" @selected(request('prioritas') === $kunci)>{{ $label }}</option>
                        @endforeach
                    </select>

                    <select name="petugas" class="field">
                        <option value="">Semua petugas</option>
                        <option value="belum" @selected(request('petugas') === 'belum')>Belum didisposisi</option>
                        @foreach ($petugasList as $item)
                            <option value="{{ $item->id }}" @selected(request('petugas') == $item->id)>{{ $item->name }}</option>
                        @endforeach
                    </select>

                    <input type="date" name="dari" value="{{ request('dari') }}" class="field" title="Dikirim dari tanggal">
                    <input type="date" name="sampai" value="{{ request('sampai') }}" class="field" title="Dikirim sampai tanggal">
                </div>
            </div>

            <div class="flex flex-wrap items-center justify-between gap-3 border-t border-ink-100 pt-4 dark:border-ink-800">
                <p class="text-xs text-ink-500 dark:text-ink-400">
                    Menampilkan <strong class="text-ink-900 dark:text-white">{{ $laporan->total() }}</strong> pengaduan
                </p>

                <div class="flex gap-2">
                    @if (request()->hasAny(['q', 'kategori', 'status', 'opd', 'prioritas', 'petugas', 'dari', 'sampai']))
                        <a href="{{ route('admin.laporan.index') }}" class="btn btn-ghost btn-sm">
                            <x-ikon nama="refresh" class="size-3.5"/> Atur Ulang
                        </a>
                    @endif
                    <a href="{{ route('admin.statistik.ekspor', request()->only(['status'])) }}" class="btn btn-outline btn-sm">
                        <x-ikon nama="download" class="size-3.5"/> Ekspor CSV
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- Tabel --}}
    <div class="card overflow-hidden">
        @if ($laporan->isEmpty())
            <x-kosong ikon="inbox" judul="Tidak ada pengaduan"
                      pesan="Belum ada pengaduan yang sesuai dengan penyaring yang Anda pilih." />
        @else
            <div class="overflow-x-auto">
                <table class="tabel">
                    <thead>
                        <tr>
                            <th>Pengaduan</th>
                            <th class="hidden lg:table-cell">Pelapor</th>
                            <th class="hidden xl:table-cell">OPD Terlapor</th>
                            <th>Status</th>
                            <th class="hidden md:table-cell">Prioritas</th>
                            <th class="hidden lg:table-cell">Petugas</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($laporan as $item)
                            <tr>
                                <td class="max-w-sm">
                                    <div class="flex items-start gap-3">
                                        <span class="grid size-9 shrink-0 place-items-center rounded-lg bg-brand-100 text-brand-700 dark:bg-brand-900/40 dark:text-brand-300">
                                            <x-ikon :nama="$item->kategori->icon" class="size-4"/>
                                        </span>
                                        <div class="min-w-0">
                                            <div class="flex items-center gap-2">
                                                <span class="font-mono text-[11px] font-bold text-brand-700 dark:text-brand-400">{{ $item->nomor_tiket }}</span>
                                                @if ($item->unread_admin > 0)
                                                    <span class="chip bg-rose-500 px-1.5 py-0 text-[10px] text-white">{{ $item->unread_admin }}</span>
                                                @endif
                                            </div>
                                            <p class="mt-0.5 line-clamp-1 font-semibold text-ink-900 dark:text-white">{{ $item->judul }}</p>
                                            <p class="mt-0.5 text-xs text-ink-500 dark:text-ink-400">
                                                {{ $item->kategori->nama }} · {{ $item->submitted_at?->translatedFormat('d M Y') }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <td class="hidden lg:table-cell">
                                    @if ($item->is_anonymous)
                                        <span class="chip bg-ink-100 text-ink-500 dark:bg-ink-800 dark:text-ink-400">
                                            <x-ikon nama="lock" class="size-3"/> Anonim
                                        </span>
                                    @else
                                        <p class="text-xs font-semibold text-ink-900 dark:text-white">{{ $item->user?->name }}</p>
                                        <p class="text-[11px] text-ink-500 dark:text-ink-400">{{ $item->user?->opd?->singkatan }}</p>
                                    @endif
                                </td>

                                <td class="hidden text-xs xl:table-cell">{{ $item->opd?->nama_singkat ?? '—' }}</td>

                                <td><x-status :laporan="$item" /></td>

                                <td class="hidden md:table-cell">
                                    <span class="chip {{ $item->prioritas_kelas }}">{{ $item->prioritas_label }}</span>
                                </td>

                                <td class="hidden lg:table-cell">
                                    @if ($item->petugas)
                                        <span class="flex items-center gap-2">
                                            <span class="grid size-7 shrink-0 place-items-center rounded-lg bg-gradient-to-br from-brand-500 to-brand-700 text-[10px] font-bold text-white">
                                                {{ $item->petugas->inisial }}
                                            </span>
                                            <span class="truncate text-xs">{{ Str::limit($item->petugas->name, 18) }}</span>
                                        </span>
                                    @else
                                        <span class="chip bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300">Belum</span>
                                    @endif
                                </td>

                                <td class="text-right">
                                    <a href="{{ route('admin.laporan.show', $item) }}" class="btn btn-primary btn-sm">
                                        <x-ikon nama="eye" class="size-3.5"/> Tinjau
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="border-t border-ink-100 px-5 py-4 dark:border-ink-800">
                {{ $laporan->links() }}
            </div>
        @endif
    </div>

@endsection
