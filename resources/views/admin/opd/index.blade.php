@extends('layouts.app')

@section('judul', 'Perangkat Daerah')
@section('subjudul', 'Kelola daftar OPD di lingkungan Pemerintah Kabupaten Pringsewu')

@section('konten')

    <div class="card p-5">
        <form method="GET" class="flex flex-col gap-3 sm:flex-row">
            <div class="relative flex-1">
                <span class="pointer-events-none absolute inset-y-0 left-0 grid w-11 place-items-center text-ink-400">
                    <x-ikon nama="search" class="size-[18px]"/>
                </span>
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari nama atau singkatan OPD…" class="field pl-11">
            </div>

            <select name="jenis" class="field sm:max-w-48">
                <option value="">Semua jenis</option>
                @foreach (['sekretariat' => 'Sekretariat', 'dinas' => 'Dinas', 'badan' => 'Badan', 'inspektorat' => 'Inspektorat', 'kecamatan' => 'Kecamatan', 'rsud' => 'RSUD', 'satuan' => 'Satuan', 'lainnya' => 'Lainnya'] as $k => $v)
                    <option value="{{ $k }}" @selected(request('jenis') === $k)>{{ $v }}</option>
                @endforeach
            </select>

            <div class="flex gap-2">
                <button class="btn btn-primary"><x-ikon nama="filter" class="size-4"/> Saring</button>
                <a href="{{ route('admin.opd.create') }}" class="btn btn-gold">
                    <x-ikon nama="plus" class="size-4"/> Tambah OPD
                </a>
            </div>
        </form>
    </div>

    <div class="card overflow-hidden">
        @if ($opd->isEmpty())
            <x-kosong ikon="building" judul="Belum ada OPD" pesan="Tambahkan perangkat daerah untuk mulai digunakan pada pendaftaran pelapor.">
                <a href="{{ route('admin.opd.create') }}" class="btn btn-primary"><x-ikon nama="plus" class="size-4"/> Tambah OPD</a>
            </x-kosong>
        @else
            <div class="overflow-x-auto">
                <table class="tabel">
                    <thead>
                        <tr>
                            <th>Perangkat Daerah</th>
                            <th class="hidden md:table-cell">Jenis</th>
                            <th class="text-right">Pengguna</th>
                            <th class="text-right">Pengaduan</th>
                            <th>Status</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($opd as $item)
                            <tr>
                                <td>
                                    <p class="font-semibold text-ink-900 dark:text-white">{{ $item->nama }}</p>
                                    <p class="text-xs text-ink-500 dark:text-ink-400">{{ $item->singkatan }}</p>
                                </td>
                                <td class="hidden md:table-cell">
                                    <span class="chip bg-ink-100 text-ink-600 dark:bg-ink-800 dark:text-ink-300">{{ $item->jenis_label }}</span>
                                </td>
                                <td class="text-right tabular-nums">{{ $item->users_count }}</td>
                                <td class="text-right tabular-nums">{{ $item->laporan_count }}</td>
                                <td>
                                    <span class="badge {{ $item->is_active
                                        ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-500/15 dark:text-emerald-300 dark:ring-emerald-400/30'
                                        : 'bg-slate-100 text-slate-600 ring-slate-600/20 dark:bg-slate-500/15 dark:text-slate-300 dark:ring-slate-400/30' }}">
                                        {{ $item->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="flex justify-end gap-1">
                                        <a href="{{ route('admin.opd.edit', $item) }}" class="btn btn-ghost btn-sm">
                                            <x-ikon nama="pencil" class="size-3.5"/>
                                        </a>
                                        <form method="POST" action="{{ route('admin.opd.destroy', $item) }}"
                                              onsubmit="return confirm('Hapus OPD {{ $item->nama }}?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-ghost btn-sm text-rose-600 hover:bg-rose-50 dark:text-rose-400 dark:hover:bg-rose-950/40">
                                                <x-ikon nama="trash" class="size-3.5"/>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="border-t border-ink-100 px-5 py-4 dark:border-ink-800">{{ $opd->links() }}</div>
        @endif
    </div>

@endsection
