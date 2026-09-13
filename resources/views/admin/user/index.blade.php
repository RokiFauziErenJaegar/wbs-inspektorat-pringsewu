@extends('layouts.app')

@section('judul', 'Manajemen Pengguna')
@section('subjudul', 'Kelola akun administrator, petugas, dan pelapor OPD')

@section('konten')

    <div class="card p-5">
        <form method="GET" class="flex flex-col gap-3 lg:flex-row">
            <div class="relative flex-1">
                <span class="pointer-events-none absolute inset-y-0 left-0 grid w-11 place-items-center text-ink-400">
                    <x-ikon nama="search" class="size-[18px]"/>
                </span>
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari nama, username, atau email…" class="field pl-11">
            </div>

            <select name="role" class="field lg:max-w-44">
                <option value="">Semua peran</option>
                <option value="admin" @selected(request('role') === 'admin')>Administrator</option>
                <option value="petugas" @selected(request('role') === 'petugas')>Petugas</option>
                <option value="pelapor" @selected(request('role') === 'pelapor')>Pelapor OPD</option>
            </select>

            <select name="opd" class="field lg:max-w-52">
                <option value="">Semua OPD</option>
                @foreach ($opdList as $item)
                    <option value="{{ $item->id }}" @selected(request('opd') == $item->id)>{{ $item->nama_singkat }}</option>
                @endforeach
            </select>

            <select name="status" class="field lg:max-w-36">
                <option value="">Semua status</option>
                <option value="aktif" @selected(request('status') === 'aktif')>Aktif</option>
                <option value="nonaktif" @selected(request('status') === 'nonaktif')>Nonaktif</option>
            </select>

            <div class="flex gap-2">
                <button class="btn btn-primary"><x-ikon nama="filter" class="size-4"/> Saring</button>
                <a href="{{ route('admin.user.create') }}" class="btn btn-gold">
                    <x-ikon nama="plus" class="size-4"/> Tambah
                </a>
            </div>
        </form>
    </div>

    <div class="card overflow-hidden">
        @if ($users->isEmpty())
            <x-kosong ikon="users" judul="Tidak ada pengguna" pesan="Belum ada pengguna yang sesuai dengan penyaring." />
        @else
            <div class="overflow-x-auto">
                <table class="tabel">
                    <thead>
                        <tr>
                            <th>Pengguna</th>
                            <th class="hidden lg:table-cell">OPD</th>
                            <th>Peran</th>
                            <th class="hidden md:table-cell text-right">Pengaduan</th>
                            <th class="hidden xl:table-cell">Terakhir Masuk</th>
                            <th>Status</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $item)
                            <tr>
                                <td>
                                    <div class="flex items-center gap-3">
                                        @if ($item->avatar_url)
                                            <img src="{{ $item->avatar_url }}" alt="" class="size-9 shrink-0 rounded-lg object-cover">
                                        @else
                                            <span class="grid size-9 shrink-0 place-items-center rounded-lg bg-gradient-to-br from-brand-500 to-brand-700 text-[10px] font-bold text-white">
                                                {{ $item->inisial }}
                                            </span>
                                        @endif
                                        <div class="min-w-0">
                                            <p class="truncate font-semibold text-ink-900 dark:text-white">{{ $item->name }}</p>
                                            <p class="truncate text-xs text-ink-500 dark:text-ink-400">{{ '@'.$item->username }} · {{ $item->email }}</p>
                                        </div>
                                    </div>
                                </td>

                                <td class="hidden text-xs lg:table-cell">{{ $item->opd?->nama_singkat ?? '—' }}</td>

                                <td>
                                    @php
                                        $gayaRole = match ($item->role) {
                                            'admin' => 'bg-violet-100 text-violet-700 dark:bg-violet-900/40 dark:text-violet-300',
                                            'petugas' => 'bg-sky-100 text-sky-700 dark:bg-sky-900/40 dark:text-sky-300',
                                            default => 'bg-ink-100 text-ink-600 dark:bg-ink-800 dark:text-ink-300',
                                        };
                                    @endphp
                                    <span class="chip {{ $gayaRole }}">
                                        {{ ['admin' => 'Administrator', 'petugas' => 'Petugas', 'pelapor' => 'Pelapor'][$item->role] }}
                                    </span>
                                </td>

                                <td class="hidden text-right tabular-nums md:table-cell">{{ $item->laporan_count }}</td>

                                <td class="hidden text-xs xl:table-cell">
                                    {{ $item->last_login_at?->diffForHumans() ?? 'Belum pernah' }}
                                </td>

                                <td>
                                    <span class="badge {{ $item->is_active
                                        ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-500/15 dark:text-emerald-300 dark:ring-emerald-400/30'
                                        : 'bg-rose-50 text-rose-700 ring-rose-600/20 dark:bg-rose-500/15 dark:text-rose-300 dark:ring-rose-400/30' }}">
                                        {{ $item->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>

                                <td>
                                    <div class="flex justify-end gap-1">
                                        <form method="POST" action="{{ route('admin.user.toggle', $item) }}">
                                            @csrf
                                            <button class="btn btn-ghost btn-sm" title="{{ $item->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                                <x-ikon :nama="$item->is_active ? 'lock' : 'check'" class="size-3.5"/>
                                            </button>
                                        </form>

                                        <a href="{{ route('admin.user.edit', $item) }}" class="btn btn-ghost btn-sm">
                                            <x-ikon nama="pencil" class="size-3.5"/>
                                        </a>

                                        <form method="POST" action="{{ route('admin.user.destroy', $item) }}"
                                              onsubmit="return confirm('Hapus pengguna {{ $item->name }}?')">
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

            <div class="border-t border-ink-100 px-5 py-4 dark:border-ink-800">{{ $users->links() }}</div>
        @endif
    </div>

@endsection
